-- Run this file in Supabase SQL Editor for existing installations.
-- Adds functions, triggers and constraints only. No columns, tables or RLS changes.
begin;

alter table public.people drop constraint if exists people_family_id_fkey;
alter table public.people add constraint people_family_id_fkey foreign key (family_id) references public.families(id) on delete restrict;
alter table public.attendance_sessions drop constraint if exists attendance_sessions_attendance_type_id_fkey;
alter table public.attendance_sessions add constraint attendance_sessions_attendance_type_id_fkey foreign key (attendance_type_id) references public.attendance_types(id) on delete restrict;
alter table public.attendance_records drop constraint if exists attendance_records_attendance_session_id_fkey;
alter table public.attendance_records add constraint attendance_records_attendance_session_id_fkey foreign key (attendance_session_id) references public.attendance_sessions(id) on delete restrict;
alter table public.attendance_records drop constraint if exists attendance_records_person_id_fkey;
alter table public.attendance_records add constraint attendance_records_person_id_fkey foreign key (person_id) references public.people(id) on delete restrict;
alter table public.attendance_summaries drop constraint if exists attendance_summaries_person_id_fkey;
alter table public.attendance_summaries add constraint attendance_summaries_person_id_fkey foreign key (person_id) references public.people(id) on delete restrict;

create or replace function public.prevent_history_delete()
returns trigger language plpgsql security invoker set search_path = public, pg_temp as $$
begin
  raise exception 'Permanent deletion is disabled to preserve history. Use existing inactive statuses for people and services; keep families and sessions.' using errcode = '23514';
end;
$$;
drop trigger if exists preserve_people_history on public.people;
create trigger preserve_people_history before delete on public.people for each row execute function public.prevent_history_delete();
drop trigger if exists preserve_family_history on public.families;
create trigger preserve_family_history before delete on public.families for each row execute function public.prevent_history_delete();
drop trigger if exists preserve_service_history on public.attendance_types;
create trigger preserve_service_history before delete on public.attendance_types for each row execute function public.prevent_history_delete();
drop trigger if exists preserve_session_history on public.attendance_sessions;
create trigger preserve_session_history before delete on public.attendance_sessions for each row execute function public.prevent_history_delete();

-- Every record writer takes the same parent lock, including direct table writes.
create or replace function public.lock_attendance_parent()
returns trigger language plpgsql security invoker set search_path = public, pg_temp as $$
begin
  if tg_op = 'DELETE' then
    perform 1 from public.attendance_sessions where id = old.attendance_session_id for update;
    return old;
  elsif tg_op = 'UPDATE' then
    perform 1 from public.attendance_sessions where id in (old.attendance_session_id, new.attendance_session_id) order by id for update;
  else
    perform 1 from public.attendance_sessions where id = new.attendance_session_id for update;
  end if;
  return new;
end;
$$;
drop trigger if exists attendance_parent_lock on public.attendance_records;
create trigger attendance_parent_lock before insert or update or delete on public.attendance_records for each row execute function public.lock_attendance_parent();

-- A fingerprint of existing data and PostgreSQL row versions replaces a revision column.
create or replace function public.load_attendance_session(p_session_id uuid)
returns jsonb language sql volatile security invoker set search_path = public, pg_temp as $$
  select jsonb_build_object(
    'revision', md5(jsonb_build_object('session', s.xmin::text, 'records', coalesce((
      select jsonb_agg(jsonb_build_array(r.id, r.xmin::text, r.person_id, r.status, r.remarks) order by r.person_id)
      from public.attendance_records r where r.attendance_session_id = s.id
    ), '[]'::jsonb))::text),
    'records', coalesce((select jsonb_agg(jsonb_build_object(
      'id', r.id, 'person_id', r.person_id, 'status', r.status, 'remarks', r.remarks
    ) order by r.person_id) from public.attendance_records r where r.attendance_session_id = s.id), '[]'::jsonb)
  ) from public.attendance_sessions s where s.id = p_session_id;
$$;

create or replace function public.save_attendance_session(p_session_id uuid, p_expected_revision text, p_changes jsonb)
returns jsonb language plpgsql security invoker set search_path = public, pg_temp as $$
declare
  snapshot jsonb;
  change_row record;
  already_applied boolean := true;
begin
  if p_expected_revision is null or jsonb_typeof(p_changes) is distinct from 'array' then
    raise exception 'Invalid attendance request.' using errcode = '22023';
  end if;
  if jsonb_array_length(p_changes) > 10000 then raise exception 'Too many attendance changes.' using errcode = '22023'; end if;
  perform 1 from public.attendance_sessions where id = p_session_id for update;
  if not found then raise exception 'Session not found or inaccessible.' using errcode = 'P0002'; end if;
  snapshot := public.load_attendance_session(p_session_id);
  if exists (select 1 from jsonb_array_elements(p_changes) item where jsonb_typeof(item) <> 'object') then
    raise exception 'Invalid attendance change.' using errcode = '22023';
  end if;
  if exists (select 1 from jsonb_array_elements(p_changes) item, jsonb_object_keys(item) key where key not in ('person_id', 'status', 'remarks')) then
    raise exception 'Unexpected attendance field.' using errcode = '22023';
  end if;
  if (select count(*) <> count(distinct person_id) or count(person_id) <> count(*) from jsonb_to_recordset(p_changes) as x(person_id uuid)) then
    raise exception 'Duplicate or missing person.' using errcode = '22023';
  end if;
  for change_row in select * from jsonb_to_recordset(p_changes) as x(person_id uuid, status text, remarks text) loop
    if change_row.status is not null and change_row.status not in ('present', 'absent') then
      raise exception 'Invalid attendance status.' using errcode = '22023';
    end if;
    if length(coalesce(change_row.remarks, '')) > 2000 or (change_row.status is null and coalesce(change_row.remarks, '') <> '') then
      raise exception 'Mark attendance before adding remarks; limit notes to 2000 characters.' using errcode = '22023';
    end if;
    if not exists (select 1 from public.people where id = change_row.person_id) then
      raise exception 'Person not found or inaccessible.' using errcode = '23514';
    end if;
    if change_row.status is null then
      if exists (select 1 from public.attendance_records where attendance_session_id = p_session_id and person_id = change_row.person_id) then already_applied := false; end if;
    elsif not exists (select 1 from public.attendance_records where attendance_session_id = p_session_id and person_id = change_row.person_id
      and status = change_row.status and coalesce(remarks, '') = coalesce(change_row.remarks, '')) then already_applied := false;
    end if;
  end loop;
  -- Assignment-based retries are safe when all requested values already match.
  if already_applied then return snapshot; end if;
  if snapshot->>'revision' is distinct from p_expected_revision then
    raise exception 'Attendance changed in another window. Reload saved attendance before saving.' using errcode = '40001';
  end if;
  for change_row in select * from jsonb_to_recordset(p_changes) as x(person_id uuid, status text, remarks text) loop
    if change_row.status is null then
      delete from public.attendance_records where attendance_session_id = p_session_id and person_id = change_row.person_id;
    else
      insert into public.attendance_records(attendance_session_id, person_id, status, remarks)
      values (p_session_id, change_row.person_id, change_row.status, nullif(change_row.remarks, ''))
      on conflict (attendance_session_id, person_id) do update set status = excluded.status, remarks = excluded.remarks
      where attendance_records.status is distinct from excluded.status or attendance_records.remarks is distinct from excluded.remarks;
    end if;
  end loop;
  return public.load_attendance_session(p_session_id);
end;
$$;
notify pgrst, 'reload schema';
commit;
