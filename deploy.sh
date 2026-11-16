#!/bin/zsh
# Deploys the committed state of this repository to weihnachtsessen.wbg.ch (Hostpoint) over SSH,
# using your own SSH key. Run from your Mac: ./deploy.sh
#
# Frontend assets are built locally and committed (npm run build). The app needs PHP 8.4:
# locally Herd's php84, on the server /usr/local/php84/bin/php, and the web server's PHP version
# comes from the "Use php-fpm php84" line in public/.htaccess (versioned in this repo).
# The server's .env, storage, caches and the database (with SQLite's journal and WAL files and any
# database.sqlite.bak-* copies next to it) are never overwritten or deleted.
#
# First deploy (no .env on the server yet): creates .env for production, generates the app key,
# creates the database, seeds the event and the admins (their passwords are printed ONCE — save
# them), and adds the scheduler cron line (daily digest at 18:00). Mail is sent via Hostpoint SMTP
# from weihnachtsessen@wbg.ch, but stays on "log" until MAIL_PASSWORD is set in the server's .env.
set -euo pipefail
cd "$(dirname "$0")"

HOST=wbgch18@wbg.ch
DEST=/home/wbgch18/www/weihnachtsessen.wbg.ch
URL=https://weihnachtsessen.wbg.ch
PHP="$HOME/Library/Application Support/Herd/bin/php84"
REMOTE_PHP=/usr/local/php84/bin/php

if [[ -n $(git status --porcelain) ]]; then
  echo "You have uncommitted changes. Commit them first; only committed code is deployed."
  exit 1
fi
if [[ ! -f public/build/manifest.json ]]; then
  echo "public/build is missing. Run 'npm run build' and commit the result."
  exit 1
fi
if [[ ! -x $PHP ]]; then
  echo "PHP 8.4 not found at $PHP (install it in Herd)."
  exit 1
fi

echo "Running tests…"
"$PHP" artisan test --testsuite=Feature

STAGE=$(mktemp -d)
trap 'rm -rf "$STAGE"' EXIT
# mktemp creates the folder as 700; rsync would copy that onto the site folder and lock out the web server.
chmod 755 "$STAGE"

echo "Preparing $(git rev-parse --short HEAD)…"
git archive HEAD | tar -x -C "$STAGE"
"$PHP" "$(command -v composer)" install --working-dir="$STAGE" --no-interaction --no-progress --no-dev --optimize-autoloader --quiet

echo "Uploading…"
rsync -az --delete \
  --exclude '/.data' --exclude '/.github' --exclude '/node_modules' --exclude '/tests' \
  --exclude '/.env' --exclude '/public/hot' --exclude '/deploy.sh' \
  --filter 'protect /storage/**' \
  --filter 'protect /bootstrap/cache/*.php' \
  --filter 'protect /database/database.sqlite*' \
  "$STAGE/" "$HOST:$DEST/"

if ! ssh "$HOST" "test -f '$DEST/.env'"; then
  echo "First deploy: creating .env, database, admins and scheduler cron…"
  # The server runs FreeBSD: no GNU 'sed -i', so write the result to .env instead.
  # If any step fails, .env is removed again so the next run retries the whole setup.
  ssh "$HOST" "cd '$DEST' && { sed \\
      -e 's|^APP_ENV=.*|APP_ENV=production|' \\
      -e 's|^APP_DEBUG=.*|APP_DEBUG=false|' \\
      -e 's|^APP_URL=.*|APP_URL=$URL|' \\
      -e 's|^LOG_LEVEL=.*|LOG_LEVEL=warning|' \\
      -e 's|^MAIL_MAILER=.*|MAIL_MAILER=log|' \\
      -e 's|^MAIL_HOST=.*|MAIL_HOST=asmtp.mail.hostpoint.ch|' \\
      -e 's|^MAIL_PORT=.*|MAIL_PORT=587|' \\
      -e 's|^MAIL_USERNAME=.*|MAIL_USERNAME=weihnachtsessen@wbg.ch|' \\
      .env.example > .env \\
    && chmod 600 .env \\
    && $REMOTE_PHP artisan key:generate --force >/dev/null \\
    && touch database/database.sqlite \\
    && $REMOTE_PHP artisan migrate --seed --force; } || { rm -f .env; exit 1; }"
  CRON="* * * * * cd $DEST && $REMOTE_PHP artisan schedule:run >> /dev/null 2>&1"
  ssh "$HOST" "crontab -l 2>/dev/null | grep -qF '$DEST' || (crontab -l 2>/dev/null; echo '$CRON') | crontab -"
  echo
  echo "⚠  Save the admin passwords above. Then set MAIL_MAILER=smtp and MAIL_PASSWORD (mailbox"
  echo "   weihnachtsessen@wbg.ch) in $DEST/.env and run ./deploy.sh again."
  echo
fi

echo "Migrating and refreshing caches…"
ssh "$HOST" "cd '$DEST' && $REMOTE_PHP artisan migrate --force >/dev/null && $REMOTE_PHP artisan optimize:clear >/dev/null && $REMOTE_PHP artisan optimize >/dev/null"

echo "Deployed $(git rev-parse --short HEAD) to $URL"
