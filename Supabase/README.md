# Church Attendance Management System

Static web app — HTML + Tailwind CSS + Vanilla JS + Supabase. Deployable to Vercel with zero build step.

---

## Stack

| Layer      | Tech                          |
|------------|-------------------------------|
| Frontend   | HTML5, Tailwind CSS (CDN), Vanilla JS |
| Database   | Supabase (PostgreSQL)         |
| Hosting    | Vercel (static)               |

---

## Setup

### 1. Create a Supabase project

1. Go to [supabase.com](https://supabase.com) → New project.
2. Open the **SQL Editor** and run `sql/schema.sql` in full.
3. Copy your **Project URL** and **anon public key** from Project Settings → API.

### 2. Configure the client

Edit `src/js/supabase.js`:

```js
const SUPABASE_URL = 'https://YOUR_PROJECT.supabase.co';
const SUPABASE_ANON_KEY = 'YOUR_ANON_KEY';
```

### 3. Run locally

Just open `src/index.html` in a browser — no build step needed.
Or use a simple static server:

```bash
npx serve src
```

### 4. Deploy to Vercel

```bash
npm i -g vercel
vercel --prod
```

Vercel will pick up `vercel.json` and serve `src/` as the root.

---

## Pages

| File                  | Purpose                                      |
|-----------------------|----------------------------------------------|
| `src/index.html`      | Dashboard — stats + recent sessions          |
| `src/people.html`     | People CRUD — add, edit, delete, view history |
| `src/families.html`   | Family CRUD — manage groups + members        |
| `src/events.html`     | Service types + session management           |
| `src/attendance.html` | Take attendance for a session                |
| `src/reports.html`    | Reports by event / person / date             |

---

## Database Tables

| Table                  | Purpose                                      |
|------------------------|----------------------------------------------|
| `families`             | Family groups                                |
| `people`               | Individual people                            |
| `attendance_types`     | Service templates (Sunday Service, etc.)     |
| `attendance_sessions`  | Specific occurrences (Jan 5 Sunday Service)  |
| `attendance_records`   | Who attended which session                   |
| `attendance_summaries` | Computed cache for dashboard queries         |

See `sql/schema.sql` for full schema with indexes and cascade rules.

---

## Category Auto-Computation

| Age       | Category |
|-----------|----------|
| ≤ 12      | Kids     |
| 13 – 24   | Youth    |
| 25 – 59   | Adults   |
| 60+       | Seniors  |
| No DOB    | Visitors |

Manual override always wins over auto-computed category.
