# Kleinmeistersonntag – Progress

Handoff document for continuing in a new session. Status as of **2026-11-13**.
See also `NOTES.md` (setup, conventions, design deviations).

## What this is

RSVP page for a client event ("Weihnachtsessen 2026"). Same client as `../kartei.site`.
Guests sign up (one or more persons, yes/no), see who's coming, and can edit their own entries.
Admins log in and can edit/delete all entries and edit the event text.

- Repo: https://github.com/marceli-to/kleinmeistersonntag (branch `main`)
- Local: http://kleinmeistersonntag.test (Laravel Herd), SQLite
- Design: `.data/Kleinmeistersonntag_261113.pdf` (12 pages)

## Stack

- Laravel 13, PHP 8.4
- Inertia 3 + Vue 3 (`resources/js/Pages`, `Components`, `Layouts`)
- Tailwind 4 (CSS config in `resources/css/app.css`), Vite 8
- `public/build` **is committed** (like kartei.site) → run `npm run build` before committing frontend changes

## User conventions (apply to all new code)

- Slim controllers; logic in **Action classes** (`app/Actions/...`, method `execute()`),
  validation in **FormRequest classes**, output via **API Resources**, permissions via **Policies**
- **Tabs** for indentation everywhere (PHP, Vue, JS, CSS, JSON) – `.editorconfig` is set
- Tailwind spacing: **1 unit = 1px** (`--spacing: 0.0625rem`), so `px-12` = 12px
- Tailwind font scale like Tailwind's names but **no `base`**: `xs`, `sm`, `lg`, `xl`
- Commit/push only when asked; commit messages end with the Claude co-author line

## Design spec (extracted from the PDF)

Values are px at 1:1 (desktop artboard 1600px, mobile 320px).

### Fonts
| Role | Face | Size / line height | Letter-spacing |
|---|---|---|---|
| Title | ABC Otto Medium (Dinamo) | 40/48 desktop, 23/28 mobile (`xl` / `lg`) | 0.01em |
| Body, inputs, buttons, rows | Muoto Regular (205TF) | 15/20 (`sm`) | 0.0375em |
| Placeholders | Muoto Italic | 15 | |
| Intro sentence | Muoto Medium | 15/20 | |
| Labels | Muoto Bold | 11/16 (`xs`) | 0.06em |

Letter-spacing isn't stored in the PDF; it was derived from the PDF text widths and matches within ±1px.
Font files: `resources/fonts/*.woff2` from the client's `fonts-web.zip`, declared in `resources/css/fonts.css`.

### Colors
- `olive` #505028 – default text, borders, icons
- `black` #000000 – input text/border on hover, focus, and when filled (filled = Regular instead of Italic)
- `orange` #e65000 – hover (radio rows: text + border + radio; buttons: orange fill, white text; own entry row)
- `olive-light` #b4b49f – dividers between list rows (top rule and the rules around "Total" are olive)

### Grid & layout
- Desktop: 12 columns × 116px, 16px gutters, 16px outer margin. Content starts at column 2 (x=148), 512px wide.
- Tablet (808px): 12 × 50px columns, content also starts at column 2, still 512px.
- Mobile: content x=32–288, radios at x=10.
- Implemented as the `page-frame` utility + `max-w-content` (512px) in `app.css`.
- Radios sit outside the content to the left (16px gap); pencil/trash icons sit 12px to the right of the content.

### Components
- Inputs/buttons: 28px tall, 0.5px border, 8.5px text inset, square corners. Textarea 84px.
- Radio: 12px circle, 5px dot.
- List rows: 28px pitch, 0.5px rules; ✓ icon 14×10, ✕ 10×10, both 8px from the right edge.
- Modal: white, 0.5px olive border (implemented as an outline so the fields stay on the grid), 40px side padding
  (overhangs the content by 40px each side), 20px top / 40px bottom; page behind fades to ~80% white.

### Vertical rhythm (desktop, y = top edge – verified in the browser)
Title baseline 56 · description baselines 83/103 · labels sit 4px above fields · 12px between field rows ·
32px before "Person hinzufügen" · 28px before "Deine Antwort" · 8px between radio rows · 32px before
"Antwort speichern" · 40px before "Bisher haben zugesagt" · 28px after "Total" before "Bisher haben abgesagt".
Desktop inputs at y=216/276, buttons at 336/508, first list rule at 596 – all equal to the PDF.

### PDF pages
1 desktop default · 2–3 desktop/tablet with grid overlay · 4 mobile · 5 hover/focus/filled states ·
6–7 own entry (pencil, orange on hover) · 8 own entry edit modal · 9 two persons ("Eure Antwort", trash icon) ·
10 admin view (pencils everywhere, pencil next to title, no intro sentence) · 11 admin edit modal (+ "Löschen") ·
12 admin event modal (Titel, Beschreibung, Speichern/Abbrechen, Link with copy icon)

## Architecture

### Data
- `events`: title, description (multi-line text). `/` always shows the latest (`Event::current()`).
- `attendees`: event_id, firstname, lastname, email, attending (bool), token (shared by all persons of one submission).
- `users`: admins only (no registration).

### Guest identification
No login for guests. Each submission gets a random 64-char token, stored in the encrypted, long-lived cookie
`kms_entries` (JSON list of tokens) – see `app/Support/GuestTokens.php`. Rows whose token is in the cookie are
"own" → pencil + edit modal. Other people's emails are never sent to the browser (`AttendeeResource`).

### Routes (`routes/web.php`)
| Method | Path | Who |
|---|---|---|
| GET | `/` | everyone |
| POST | `/attendees` | everyone (throttle 20/min) |
| PUT | `/attendees/{attendee}` | owner (cookie) or admin – `AttendeePolicy` |
| DELETE | `/attendees/{attendee}` | admin |
| PUT | `/events/{event}` | admin |
| GET/POST | `/login` | guests; POST `/logout` for admins |

### Key files
- Actions: `app/Actions/Attendee/{StoreAttendees,UpdateAttendee,DeleteAttendee}`, `Event/UpdateEvent`, `Auth/{Login,Logout}`
- Requests: `app/Http/Requests/{Attendee,Event,Auth}/*`
- Resources: `AttendeeResource` (name, attending, own, editable; email/first/lastname only if editable), `EventResource` (+ link)
- Controllers: `EventController`, `AttendeeController`, `Auth/SessionController`
- German validation messages: `lang/de/validation.php`, `lang/de/auth.php` (`APP_LOCALE=de`)
- Vue: `Pages/Event/Show.vue`, `Pages/Auth/Login.vue`, `Components/{PersonFields,AnswerFields,AttendeeList,EventHeader,EditAttendeeModal,EditEventModal,Modal,BaseButton}`, `Components/Form/*`, `Components/Icons/*`

### Admins
`database/seeders/AdminSeeder.php` creates **Marcel Stadelmann (m@marceli.to)** and **Benedikt Flüeler (bf@wbg.ch)**
with random passwords in the format `XXXX-XXXX-XXXX-XXXX` (uppercase letters), printed once in the seeder output.
Existing admins are never touched (re-seeding doesn't reset passwords). Reset = delete the user + re-run the seeder.
Local passwords were generated on 2026-11-13 and handed over in the chat (not stored anywhere in plain text).
On the server, `php artisan migrate --seed` creates them again with new passwords.

`DatabaseSeeder`: creates the event if none exists, calls `AdminSeeder`, and `DemoSeeder` (placeholder guests from
the PDF) only when `APP_ENV=local`.

### Tests
`php artisan test` – 12 tests: page props/privacy, multi-person signup + cookie, validation, own-entry editing,
guests can't edit/delete others, admin edit/delete, event update permissions, login/logout, admin seeder
(password format, never resets).

## Done

- [x] Design extracted from the PDF (fonts, sizes, colors, grid, spacing, states)
- [x] Laravel/Inertia/Vue/Tailwind setup with the user's conventions
- [x] Guest signup (multiple persons, "Deine/Eure Antwort"), own-entry editing via cookie token
- [x] Admin login, edit/delete any entry, edit event title/description, copy link
- [x] Desktop + mobile layout verified against the PDF in the browser
- [x] Real fonts from the client installed (stand-ins removed), letter-spacing calibrated
- [x] Admin seeder (2 admins, random passwords)
- [x] Feature tests
- [x] Pushed to GitHub (initial commit `e40dd44`)
- [x] Confirmation email with "Eintrag bearbeiten" link (opens the edit modal) + daily 18:00 digest to the owner

## Open questions / not designed (decisions taken – see `NOTES.md`)

- **"Person hinzufügen" in the edit modal** (PDF p. 8, 11) left out – editing works per person.
  Ask the client whether adding people to an existing entry is needed.
- **Mobile description**: PDF shows one item per line on mobile, comma-separated on desktop; it's one text field,
  so line breaks are whatever the admin enters.
- **"Antwort speichern" hover**: PDF keeps an olive border on the orange button – we use an orange border.
- **Modal button gap**: PDF has 8px and 4px – unified to 4px.
- Not designed, built in the same style: login page, "Abmelden" link, validation errors (11px bold orange),
  no success message after saving.
- Title uses a normal space (PDF has a thin space) → ~4px wider than the PDF.

## Possible next steps

- Clarify the open questions above with the client
- First deploy with `./deploy.sh` (sets up .env, DB, admins, cron), then add the SMTP credentials to the server's .env
- Optional: CSV export for admins, closing registration after a deadline
- Laravel's default `AGENTS.md` / `CLAUDE.md` (Boost guidelines) are in the repo unchanged – replace or remove
