# Project Structure

Static application layout following the topic-tracker project conventions.

## Files Used (Source of Truth)

- Pages: `index.html`, `pages/people.html`, `pages/families.html`, `pages/events.html`, `pages/attendance.html`, `pages/reports.html`, and `pages/offline.html`.
- Shared styles and scripts: `assets/css/` and `assets/js/`.
- Images and app icons: `assets/images/`.
- Progressive Web App: `manifest.webmanifest`, `sw.js`, and `assets/js/pwa.js`.
- Database setup and maintenance: `supabase/schema.sql` and the supporting SQL scripts in `supabase/`.
- Deployment: `vercel.json`.
- Verification: `tests/*.test.cjs`.
- Design reference: `MD/Design.md`.
- Original feature proposal: `MD/To be developed/Project.txt` (planned requirements, not a description of every implemented feature).

## Conditions (If / Then)

- If running locally:
  - Then serve the repository root with `npx serve .`.
  - Then use localhost HTTP to enable service-worker testing.
- If deploying to Vercel:
  - Then the static build includes `index.html`, `pages/`, `assets/`, `sw.js`, and `manifest.webmanifest`.
  - Then SQL scripts, documentation, tests, and development rules remain outside the deployment output.
- If hosting under a subdirectory:
  - Then relative navigation, parent-relative shared assets and manifest, and root worker URLs stay inside that application directory.
- If moving shared scripts or images:
  - Then update HTML references, CSS image URLs, manifest icons, worker registration, and test file paths together.

## Sequences (Typical Flow)

1. Run `supabase/schema.sql` in a Supabase project as described in `README.md`.
2. Configure the client in `assets/js/supabase.js`.
3. Serve the repository root and open `index.html`.
4. Run `node --test tests/*.test.cjs` for the existing automated checks.

See `README.md` for application behavior and installation instructions.
