#!/usr/bin/env bash
# Deploy the committed code (git HEAD) to the VPS. Run from the project root:  bash scripts/deploy.sh
# Needs: SSH key access to the server. Build the assets first (npm run build) and commit public/build.
set -euo pipefail

HOST="${DEPLOY_HOST:-root@217.217.249.12}"
DIR="${DEPLOY_DIR:-/home/french/htdocs/french.truespire.in}"
SITE_USER="${DEPLOY_USER:-french}"
TMP="$(mktemp -d)"
trap 'rm -rf "$TMP"' EXIT

[ -z "$(git status --porcelain)" ] || { echo "Uncommitted changes: commit first (only HEAD is deployed)." >&2; exit 1; }

git archive --format=tar -o "$TMP/app.tar" HEAD
scp -o BatchMode=yes "$TMP/app.tar" "$HOST:/root/app-deploy.tar" >/dev/null

ssh -o BatchMode=yes "$HOST" "DIR='$DIR' SITE_USER='$SITE_USER' bash -s" <<'REMOTE'
set -euo pipefail
cp "$DIR/.env" "/root/backups/env-$(date +%F-%H%M).bak" 2>/dev/null || true
rm -rf /root/app-new && mkdir /root/app-new && tar -xf /root/app-deploy.tar -C /root/app-new
rsync -a --delete --exclude=.env --exclude=storage --exclude=vendor --exclude=bootstrap/cache /root/app-new/ "$DIR/"
chown -R "$SITE_USER:$SITE_USER" "$DIR"; chown "$SITE_USER:$SITE_USER" "$DIR/.env"
cd "$DIR"
run() { sudo -u "$SITE_USER" -H php8.3 "$@"; }
run /usr/local/bin/composer install --no-dev --optimize-autoloader --no-interaction 2>&1 | tail -1
run artisan config:clear >/dev/null; run artisan route:clear >/dev/null; run artisan view:clear >/dev/null
run artisan migrate --force --seed 2>&1 | tail -3
run artisan config:cache | tail -1; run artisan route:cache | tail -1; run artisan view:cache | tail -1
chmod -R ug+rwX storage bootstrap/cache
echo "deployed $(date -Is)"
REMOTE
