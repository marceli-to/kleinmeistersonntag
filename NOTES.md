# Kleinmeistersonntag – Notes

Design: `.data/Kleinmeistersonntag_261113.pdf` (12 pages, desktop 1600px, mobile 320px).

## Fonts

`resources/fonts/` (woff2 only), declared in `resources/css/fonts.css`:

- **Muoto** (205TF) – Regular 400, Italic 400, Medium 500, Bold 700
- **ABC Otto** (Dinamo) – Medium 500

Source: `fonts-web.zip` from the client (2026-11-13), files renamed without spaces.

## Setup

```bash
composer install && npm install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed   # creates the event + admins (+ demo guests when APP_ENV=local)
npm run build                # or npm run dev
```

Admin login: `/login` (no registration). Admins come from `database/seeders/AdminSeeder.php`
(Marcel Stadelmann, Benedikt Flüeler). Missing admins are created with a random password
(`XXXX-XXXX-XXXX-XXXX`), printed **once** in the seeder output. Existing admins are never touched, so re-seeding
doesn't reset passwords. To reset one: delete the user, then `php artisan db:seed --class=AdminSeeder`.

## How it works

- **Guests** don't log in. Every submission gets a random token stored in an encrypted, long-lived cookie
  (`kms_entries`). Rows whose token is in the cookie are "own" entries → pencil + edit modal.
  Clearing cookies / switching device = no more edit access (admin can still edit).
- **Admin** sees the same page with pencils on all rows, a delete button in the edit modal, and a pencil next to
  the title (title, description, copyable link).
- `/` always shows the most recent event (`Event::current()`).
- Emails are only sent to the browser for own entries or the admin.

## Deploy

`./deploy.sh` deploys the committed HEAD to https://weihnachtsessen.wbg.ch (`wbgch18@wbg.ch:~/www/weihnachtsessen.wbg.ch`,
web root `public/`), based on the kimiyuch deploy scripts. PHP 8.4 everywhere: Herd's php84 locally,
`/usr/local/php84/bin/php` on the server, and `Use php-fpm php84` in `public/.htaccess` for the web server (the server's
default CLI PHP is 8.3). The first run creates the production `.env` (Hostpoint SMTP as weihnachtsessen@wbg.ch;
mail = log until `MAIL_MAILER=smtp` and `MAIL_PASSWORD` are set),
the SQLite DB, the admins (passwords printed once) and the `schedule:run` cron line.
The server runs **FreeBSD** (BSD tools: no GNU `sed -i`, use `sha1sum`/`sed … > file`). Mail login via
`asmtp.mail.hostpoint.ch:587` with the mailbox weihnachtsessen@wbg.ch; the password lives only in the server `.env`.

## Testing on live

`php artisan attendees:flush` deletes all entries (keeps the event and the admins), with a confirmation;
`--force` skips it. On the server: `cd ~/www/weihnachtsessen.wbg.ch && /usr/local/php84/bin/php artisan attendees:flush`.
Meant for testing before go-live – remove the command once real guests sign up.

## Emails

- **Confirmation** (`App\Mail\AttendeeConfirmation`): sent synchronously on signup, one email per distinct
  address (persons sharing an address get one email). A mail failure is only reported and doesn't break the
  signup. The "Eintrag bearbeiten" link is a signed URL with no expiry (`/eintrag/{id}/bearbeiten`). It adds the
  entry's token to the cookie, so it also works on another device, then redirects to `/` with the
  `editAttendee` flash value, which opens the edit modal.
- **Daily digest** (`attendees:digest`, `App\Mail\DailyDigest`): scheduled daily at 18:00 Europe/Zurich in
  `routes/console.php`. Lists the entries created in the last 24 h plus the current totals, sent to
  `MAIL_TO` (default bf@wbg.ch, several addresses comma-separated). Nothing is sent when there are no new entries.
  **Production needs the scheduler cron:** `* * * * * cd /path && php artisan schedule:run >> /dev/null 2>&1`.
- Design: `resources/views/mail/*` with inline styles, white background, left-aligned, no header. Uses Inter
  with a system fallback instead of Muoto/Otto. Page colors, 11px bold labels, hairline rows, outlined button.
- Local: MailHog (`MAIL_MAILER=smtp`, port 1025, UI http://localhost:8025).
- Local PHP: the site is isolated to PHP 8.4 in Herd (lock file needs >= 8.4.1), so use `herd php artisan …`.

## Tailwind conventions

- Spacing: 1 unit = 1px (`--spacing: 0.0625rem`), so `px-12` = 12px.
- Font sizes (no `base`): `xs` 11/16 (labels), `sm` 15/20 (body), `lg` 23/28 (mobile title), `xl` 40/48 (title).
- Colors: `olive` #505028, `olive-light` #b4b49f (row dividers), `orange` #e65000 (hover), `black` (focus/filled).
- Layout: `page-frame` utility = 12-col grid, content starts at column 2, max width 512px (`max-w-content`).

## Deviations / decisions not covered by the design

- **Letter-spacing** isn't stored in the PDF; derived from the PDF text widths with the real fonts:
  15px 0.0375em, 11px labels 0.06em, title 0.01em (all within ±1px of the PDF).
- The PDF uses thin spaces (U+2009) in "Weihnachtsessen 2026" and "Vorname *". The title is admin-editable
  text, so it uses whatever the admin types (normal space → title ~4px wider than in the PDF).
- **Hover on "Antwort speichern"**: PDF keeps the olive border on the orange button; we use an orange border.
- **Modal button spacing**: PDF uses 8px (p. 8) and 4px (p. 11, 12) between stacked buttons → unified to 4px.
- **"Person hinzufügen" inside the edit modal** (p. 8, 11) is left out – editing works per person.
  To clarify: should it be possible to add people to an existing entry?
- **Admin view**: intro sentence hidden (as in p. 10). "Abmelden" link added below the lists (not designed).
- **Login page** isn't designed – built in the same style.
- **Validation errors**: not designed – 11px bold orange text below the field, orange field border.
- **Mobile description**: design shows one item per line ("Da Michelangelo / Gertrudstrasse 37 / …"),
  desktop shows comma-separated lines. The description is a single text field, so line breaks are whatever
  the admin enters.
- **Modal backdrop**: the page behind fades to ~80% white (estimated from the PDF rendering).
- No success message after saving (not designed) – the entry simply appears in the list.
- **Modal position**: vertically centred in the viewport (as on p. 11 and 12). P. 8 sits higher – treated as an
  inconsistency in the design.
