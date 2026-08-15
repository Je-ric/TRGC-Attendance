-- Disable RLS on all tables
alter table families              disable row level security;
alter table people                disable row level security;
alter table attendance_types      disable row level security;
alter table attendance_sessions   disable row level security;
alter table attendance_records    disable row level security;
alter table attendance_summaries  disable row level security;

-- If disabling doesn't take effect (Supabase sometimes re-enables it),
-- add a permissive allow-all policy as a fallback:
create policy "allow_all" on families             for all using (true) with check (true);
create policy "allow_all" on people               for all using (true) with check (true);
create policy "allow_all" on attendance_types     for all using (true) with check (true);
create policy "allow_all" on attendance_sessions  for all using (true) with check (true);
create policy "allow_all" on attendance_records   for all using (true) with check (true);
create policy "allow_all" on attendance_summaries for all using (true) with check (true);
