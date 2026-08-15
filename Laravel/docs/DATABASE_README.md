# TRGC Attendance — Database Reference

## Overview

The database tracks **who attended which church service on which date**.
It is organized around 3 core concepts:

```
Service Template  →  Specific Occurrence  →  Who Attended
(attendance_types)   (attendance_sessions)   (attendance_records)
```

---

## Table-by-Table Reference

---

### `families`
A **family group** that people can belong to. Optional — a person can exist without a family.

| Field            | Type    | Purpose |
|------------------|---------|---------|
| `id`             | PK      | |
| `family_name`    | string  | e.g., "Santos Family" |
| `address`        | string  | Street address |
| `barangay`       | string  | Barangay (Philippine locality) |
| `contact_person` | string  | Name of the primary contact in the family |
| `contact_number` | string  | Phone number of the family contact |
| `notes`          | text    | Any internal notes |

**Relationships:**
- Has many `people`

---

### `people`
Every individual tracked in the system — members, regular attendees, visitors, kids, etc.

| Field               | Type    | Purpose |
|---------------------|---------|---------|
| `id`                | PK      | |
| `family_id`         | FK      | Optional link to `families` |
| `first_name`        | string  | |
| `last_name`         | string  | |
| `birthdate`         | date    | Used to auto-compute age and category |
| `gender`            | enum    | Male / Female |
| `civil_status`      | enum    | Single / Married / Widowed / Separated |
| `category`          | enum    | Kids / Youth / Adults / Seniors / Visitors — **manual override**. If null, auto-computed from age |
| `membership_status` | enum    | Member / Regular Attendee / Visitor / Inactive |
| `joined_date`       | date    | When they first joined the church |
| `date_of_baptism`   | date    | Baptism date if applicable |
| `address`           | string  | Personal address (may differ from family address) |
| `contact_number`    | string  | Personal phone |
| `email`             | string  | Personal email |
| `notes`             | text    | Internal notes |

**Computed (not stored):**
- `age` — derived from `birthdate`
- `effective_category` — uses `category` if set, otherwise auto-computes from `age`

**Relationships:**
- Belongs to `families` (optional)
- Has many `attendance_records`

---

### `attendance_types`
A **service template** — the recurring definition of a church service or event.
Think of this as the "what" — not a specific date, but the type of gathering.

**Examples:** "Sunday Morning Service", "Wednesday Bible Study", "Youth Fellowship"

| Field          | Type    | Purpose |
|----------------|---------|---------|
| `id`           | PK      | |
| `name`         | string  | Display name, e.g., "Sunday Morning Service" |
| `description`  | string  | Brief description of the service |
| `is_recurring` | boolean | Whether this repeats on a schedule |
| `day_of_week`  | string  | e.g., "Sunday" — the usual day it occurs |
| `start_time`   | time    | e.g., "09:30" — usual start time |
| `location`     | string  | e.g., "Main Sanctuary" |
| `is_active`    | boolean | Whether this service is still running |

**Relationships:**
- Has many `attendance_sessions`

---

### `attendance_sessions`
A **specific occurrence** of a service on a specific date.
Think of this as the "when" — one row per actual gathering held.

**Example:** "Sunday Morning Service on January 5, 2026"

| Field                 | Type    | Purpose |
|-----------------------|---------|---------|
| `id`                  | PK      | |
| `attendance_type_id`  | FK      | Which service template this belongs to |
| `date`                | date    | The actual date this service was held |
| `service_name`        | string  | **Optional sub-label** for this specific occurrence, e.g., "Baptism Sunday", "Christmas Service". NOT the service type name — that comes from `attendance_types.name` |
| `notes`               | text    | Notes specific to this session |

**Unique constraint:** `(attendance_type_id, date, service_name)` — prevents duplicate sessions.

**⚠️ Common confusion:** `service_name` here is NOT the same as `attendance_types.name`.
- `attendance_types.name` = "Sunday Morning Service" (always the same)
- `attendance_sessions.service_name` = "Baptism Sunday" (optional, changes per session)

**Relationships:**
- Belongs to `attendance_types`
- Has many `attendance_records`

---

### `attendance_records`
**Who attended** a specific session. One row per person per session.

| Field                    | Type    | Purpose |
|--------------------------|---------|---------|
| `id`                     | PK      | |
| `attendance_session_id`  | FK      | Which session this record belongs to |
| `person_id`              | FK      | Which person attended |
| `status`                 | enum    | `present` or `absent` (currently only `present` records are stored) |
| `remarks`                | string  | Optional note for this specific attendance (e.g., "arrived late") |

**Unique constraint:** `(attendance_session_id, person_id)` — one record per person per session.

**Relationships:**
- Belongs to `attendance_sessions`
- Belongs to `people`

---

### `attendance_summaries` *(new — computed cache)*
A **pre-computed summary** per person for fast dashboard queries.
This is a **cache table** — it is recomputed whenever attendance is saved.
Without this, every dashboard query would need to scan all `attendance_records`.

| Field              | Type      | Purpose |
|--------------------|-----------|---------|
| `id`               | PK        | |
| `person_id`        | FK unique | One row per person |
| `total_present`    | int       | Total sessions this person attended |
| `total_sessions`   | int       | Total sessions held across all services they could attend |
| `last_attended_at` | date      | Date of their most recent attendance |
| `attendance_rate`  | decimal   | `total_present / total_sessions * 100` (0–100) |
| `streak`           | int       | Consecutive sessions attended (current streak) |

**Powers:**
- Dashboard "Top Attendees" and "Inactive Members"
- Attendance rate per person
- "Who hasn't attended in 30+ days"

---

## Full Relationship Map

```
families
  └── people (family_id → families.id, nullOnDelete)
        └── attendance_records (person_id → people.id, cascadeOnDelete)
              └── attendance_sessions (attendance_session_id → attendance_sessions.id, cascadeOnDelete)
                    └── attendance_types (attendance_type_id → attendance_types.id, cascadeOnDelete)

people
  └── attendance_summaries (person_id → people.id, cascadeOnDelete) [1:1]
```

---

## Cascade Delete Rules

| Delete this...       | Also deletes...                                      |
|----------------------|------------------------------------------------------|
| `families` row       | Sets `people.family_id = NULL` (nullOnDelete)        |
| `people` row         | All their `attendance_records`, `attendance_summaries` |
| `attendance_types`   | All its `attendance_sessions` and their `attendance_records` |
| `attendance_sessions`| All its `attendance_records`                         |

---

## Redundant / Clarified Fields

| Field | Status | Reason |
|-------|--------|--------|
| `attendance_sessions.service_name` | ✅ Keep | It's a **per-session sub-label**, not the service name. Rename mentally to "sermon title" or "special occasion label". |
| `attendance_records.status` | ✅ Keep | Currently only `present` records are stored, but `absent` is reserved for future explicit absence tracking. |
| `people.category` vs `effective_category` | ✅ Keep both | `category` is the manual override; `effective_category` is the computed accessor. |

---

## Naming Clarification (Plain English)

| You say...       | The table is...         | Example value |
|------------------|-------------------------|---------------|
| "Service"        | `attendance_types`      | "Sunday Morning Service" |
| "Session / Date" | `attendance_sessions`   | "Sunday Morning Service — Jan 5, 2026" |
| "Check-in"       | `attendance_records`    | "Juan dela Cruz was present on Jan 5" |
| "Summary"        | `attendance_summaries`  | "Juan attended 45 of 52 sessions (86%)" |
