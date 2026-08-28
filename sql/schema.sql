-- ============================================================
-- Church Attendance Management System — Supabase Schema
-- ============================================================

-- Enable UUID extension
create extension if not exists "uuid-ossp";

-- ============================================================
-- FAMILIES
-- ============================================================
create table families (
  id            uuid primary key default uuid_generate_v4(),
  family_name   text not null,
  address       text,
  barangay      text,
  contact_person text,
  contact_number text,
  notes         text,
  created_at    timestamptz not null default now(),
  updated_at    timestamptz not null default now()
);

-- ============================================================
-- PEOPLE
-- ============================================================
create table people (
  id                uuid primary key default uuid_generate_v4(),
  family_id         uuid references families(id) on delete set null,
  first_name        text not null,
  last_name         text not null,
  birthdate         date,
  age               int,
  gender            text check (gender in ('Male', 'Female')),
  civil_status      text check (civil_status in ('Single', 'Married', 'Widowed', 'Separated')),
  category          text check (category in ('Kids', 'Youth', 'Adults', 'Seniors')),
  membership_status text not null default 'Visitor' check (membership_status in ('Member', 'Regular Attendee', 'Visitor', 'Inactive')),
  joined_date       date,
  date_of_baptism   date,
  address           text,
  contact_number    text,
  email             text,
  notes             text,
  created_at        timestamptz not null default now(),
  updated_at        timestamptz not null default now()
);

-- ============================================================
-- ATTENDANCE TYPES (service templates)
-- ============================================================
create table attendance_types (
  id          uuid primary key default uuid_generate_v4(),
  name        text not null,
  description text,
  is_recurring boolean not null default true,
  day_of_week text,
  start_time  time,
  location    text,
  is_active   boolean not null default true,
  created_at  timestamptz not null default now(),
  updated_at  timestamptz not null default now()
);

-- ============================================================
-- ATTENDANCE SESSIONS (specific occurrences)
-- ============================================================
create table attendance_sessions (
  id                   uuid primary key default uuid_generate_v4(),
  attendance_type_id   uuid references attendance_types(id) on delete cascade,
  date                 date not null,
  service_name         text,  -- optional sub-label / sermon title
  notes                text,
  created_at           timestamptz not null default now(),
  updated_at           timestamptz not null default now(),
  unique (attendance_type_id, date, service_name)
);

-- ============================================================
-- ATTENDANCE RECORDS (who attended)
-- ============================================================
create table attendance_records (
  id                     uuid primary key default uuid_generate_v4(),
  attendance_session_id  uuid not null references attendance_sessions(id) on delete cascade,
  person_id              uuid not null references people(id) on delete cascade,
  status                 text not null default 'present' check (status in ('present', 'absent')),
  remarks                text,
  created_at             timestamptz not null default now(),
  unique (attendance_session_id, person_id)
);

-- ============================================================
-- ATTENDANCE SUMMARIES (computed cache — 1 row per person)
-- ============================================================
create table attendance_summaries (
  id               uuid primary key default uuid_generate_v4(),
  person_id        uuid not null unique references people(id) on delete cascade,
  total_present    int not null default 0,
  total_sessions   int not null default 0,
  last_attended_at date,
  attendance_rate  numeric(5,2) not null default 0,
  streak           int not null default 0,
  updated_at       timestamptz not null default now()
);

-- ============================================================
-- INDEXES
-- ============================================================
create index on people(family_id);
create index on people(membership_status);
create index on people(category);
create index on attendance_sessions(attendance_type_id);
create index on attendance_sessions(date);
create index on attendance_records(attendance_session_id);
create index on attendance_records(person_id);

-- ============================================================
-- updated_at trigger helper
-- ============================================================
create or replace function set_updated_at()
returns trigger language plpgsql as $$
begin
  new.updated_at = now();
  return new;
end;
$$;

create trigger trg_families_updated_at before update on families
  for each row execute function set_updated_at();
create trigger trg_people_updated_at before update on people
  for each row execute function set_updated_at();
create trigger trg_attendance_types_updated_at before update on attendance_types
  for each row execute function set_updated_at();
create trigger trg_attendance_sessions_updated_at before update on attendance_sessions
  for each row execute function set_updated_at();

-- ============================================================
-- RLS — disable for now (enable + add policies when auth is added)
-- ============================================================
alter table families disable row level security;
alter table people disable row level security;
alter table attendance_types disable row level security;
alter table attendance_sessions disable row level security;
alter table attendance_records disable row level security;
alter table attendance_summaries disable row level security;
