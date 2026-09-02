-- Run once in Supabase SQL Editor for an existing installation.
-- New installations already receive these changes from schema.sql.

alter table people
  drop constraint if exists people_age_check;

alter table people
  add constraint people_age_check check (age between 0 and 130) not valid;

alter table people validate constraint people_age_check;

create index if not exists people_name_idx
  on people(last_name, first_name);

create index if not exists attendance_sessions_date_type_idx
  on attendance_sessions(date desc, attendance_type_id);

create index if not exists attendance_records_session_status_idx
  on attendance_records(attendance_session_id, status);
