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

### Interface and layout

All six pages load `src/css/interface.css` after their base styles. The banner
keeps its original appearance. Application content uses clearer text contrast,
consistent form sizing, soft card shadows, subtle gradients, red primary actions,
and slate secondary actions. No data-entry fields were added.

- Dashboard: overview actions, totals, attendance chart, recent-session cards,
  quick links, and an expandable **Data backup** section at the bottom.
- People: a table for comparing directory records, compact rows as cards on
  phones with visible actions, refreshed search, filters, pagination, and dialogs.
- Families: searchable, paginated household cards with the existing view, edit,
  and delete actions.
- Events: service-type cards and a separate sessions table.
- Attendance: a monthly session-card browser, clearer counts, grouped marking
  controls, and the attendance table.
- Reports: consistent filter panels, summary cards, tabs, and report tables.

Dashboard logic lives in `src/js/dashboard.js`. Totals use database counts without
downloading session IDs, and failures show unavailable values rather than zero.
The recent-session list loads six records. The chart reuses counts for 30 seconds
when changing filters; selecting **View** clears its cache and fetches fresh data.
Obsolete chart loads stop scheduling more queries. Decorative animated page
backgrounds and table-row scaling are removed; banner animations remain intact.

Displayed dates use month names (for example, **October 28, 2026**) and service
times use **5:30 PM** instead of raw database times. The banner clock and today's
date use Asia/Manila, with a 12-hour clock. Native date/time inputs and raw backup
data retain their original formats. Scrollable areas use a thin maroon gradient
scrollbar in Chromium/WebKit and a solid maroon fallback in Firefox.

### Report periods

Reports uses shared **Year**, **Month**, and **Week** controls across By Event,
By Person, and By Period. It defaults to the current Philippine month and all
weeks. Select **Full year** for the whole year, or choose a week by its readable
date range. Changing year/month resets the week to **All weeks**. Weeks run
Monday–Sunday and are clipped to the selected month/year so dates never overlap.
The selected inclusive date range is shown below the controls. There are no
calendar or From/To inputs. Report reads are batched; person/date filters are
applied on the database, and session counts are computed from separately fetched
attendance records to avoid nested row limits. Source: `src/js/reports.js` and
`src/reports.html`.

### Finding attendance sessions

Attendance opens with session cards for the current Philippine month, latest
first. Use **Previous month**, **Next month**, or the month/year controls to browse
older sessions, and **This Month** to return. Today's sessions are highlighted.
**Search all sessions** searches the entire history by service name, sub-label,
weekday, or date (for example, `October 25, 2026` or `2026-10-25`). Cards are
paginated in groups of 12. Only the selected month's metadata is fetched until
a search needs the full history; reads are batched to avoid database row limits.

Select **Open Attendance** to load a session. The browser collapses and the
selected service and date remain visible; use **Change Session** to browse again.
Unsaved changes require confirmation before opening another session. Opening the
currently selected card preserves its draft. Session links from the Dashboard,
Events, and Reports continue to open the correct session and month. Missing or
failed session links show a recoverable message. People and attendance records
load in batches when opening a session, and outdated responses are ignored.

Implementation: `src/js/session-browser.js`, `src/attendance.html`, and
`src/css/interface.css`.

### Weekly and monthly attendance chart

The Dashboard defaults to weekly present check-ins for the full current year in
Asia/Manila. Enter a year and select **View**. Use **View by** to switch between
weekly and monthly bars. Choose a **Month** to focus on that month's weeks or
monthly total; select **Full year** to return to the whole year.

Weeks run Monday–Sunday. The first and last periods are clipped to the selected
year or month, so every selected calendar date belongs to exactly one period.
Week labels (W1, W2, etc.) are numbered within the selection, not ISO week numbers.
The full-year weekly chart labels months along its axis; hover over a bar or open
the totals table for the exact week dates and count. Weekly and monthly views
cover the same records, so their full-year totals agree for unchanged data.
Each present attendance record counts once for its session, using the session's
date rather than the date the record was entered. Multiple sessions attended by
one person count separately. Absent and unmarked attendance are excluded.

The chart includes the selected period's total, highest week(s) or month(s), and an
expandable table of exact counts and period labels. Empty periods show zero.
Database count queries avoid row limits, with at most four requests in flight per
chart load; failed queries show a retry message instead of incomplete totals. The
implementation lives in `src/js/dashboard-chart.js`, `src/index.html`, and
`src/css/app.css`.

### Full data backup

On the Dashboard, expand **Data backup** and click **Export All Data**. The app reads all six tables in
batches of up to 500 rows and downloads a single timestamped JSON file containing
raw saved fields, IDs, relationships, per-table row counts, and export timestamps.
This includes people, families, service types/events (`attendance_types`), sessions,
attendance records, and attendance summaries, including inactive and older records.
Empty tables are included. Page filters do not limit the backup.

Every batch must succeed and each table's fetched row count must match its counts
before and after fetching. Otherwise, no backup file is downloaded; retry after
the error is resolved. Pause editing while exporting: these browser requests are
not a single database snapshot, and count checks cannot detect every concurrent edit.
Only data accessible to the current database client is exported.

People CSV and selected-session attendance CSV also fetch all their source rows
in batches. Attendance CSV preserves the selected session's current draft edits;
the full JSON backup includes saved data only. Keep backups private because they
contain personal details. This is a data export, not a database/schema backup;
automatic restore/import is not provided.

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
