#!/bin/sh
# Local smoke test for the PHP API (needs PHP with pdo_sqlite). Usage: sh server/test-api.sh
set -e
HERE="$(cd "$(dirname "$0")" && pwd)"
T="${TMPDIR:-/tmp}/hermes-api-test"
rm -rf "$T" && mkdir -p "$T/site"
cp -r "$HERE/api" "$HERE/blog" "$T/site/"
cat > "$T/site/api/config.php" <<'CFG'
<?php return ['db_driver'=>'sqlite','sqlite_path'=>__DIR__.'/../t.sqlite','site_url'=>'http://localhost:8099','admin_password_hash'=>password_hash('pw123',PASSWORD_DEFAULT),'hermes_api_key'=>'testkey_testkey_testkey_testkey_1234'];
CFG
php -S localhost:8099 -t "$T/site" "$HERE/dev-router.php" >"$T/php.log" 2>&1 &
PID=$!
trap 'kill $PID' EXIT
sleep 2
K="Authorization: Bearer testkey_testkey_testkey_testkey_1234"
J="Content-Type: application/json"
U=localhost:8099
echo "status:"; curl -s $U/api/v1/status; echo
echo "lead:"; curl -s -X POST $U/api/v1/leads -H "$J" -d '{"full_name":"Asha","email":"asha@x.com","message":"TEF prep","phone":"+91 99999"}'; echo
echo "leads (no auth):"; curl -s $U/api/v1/leads; echo
echo "leads:"; curl -s $U/api/v1/leads -H "$K" | head -c 300; echo
echo "blog create:"; curl -s -X POST $U/api/v1/blog -H "$K" -H "$J" -d '{"title":"Passe compose guide","content_md":"## Intro\n\nHello **world** <script>x</script> [a](https://e.com)\n\n- one\n- two","status":"published","excerpt":"ex"}' | head -c 200; echo
echo "blog page:"; curl -s $U/blog/passe-compose-guide/ > "$T/post.html"; grep -c "<h1>Passe compose guide</h1>" "$T/post.html"; echo "raw script tags (must be 0):"; grep -c "<script>x" "$T/post.html" || true
echo "inbound mail:"; curl -s -X POST $U/api/v1/emails -H "$K" -H "$J" -d '{"direction":"in","message_id":"<a@b>","from_addr":"asha@x.com","subject":"Hi","body":"price?"}' | head -c 250; echo
echo "outbound draft (hermes tries approved):"; curl -s -X POST $U/api/v1/emails -H "$K" -H "$J" -d '{"to_addr":"asha@x.com","subject":"Re","body":"hello","status":"approved","reply_to_id":1}' | head -c 250; echo
echo "hermes approve (must be 403):"; curl -s -X PATCH $U/api/v1/emails/2 -H "$K" -H "$J" -d '{"status":"approved"}'; echo
echo "admin login:"; curl -s -c "$T/cj" -X POST $U/api/v1/admin/login -H "$J" -H "X-Requested-With: fetch" -d '{"password":"pw123"}'; echo
echo "admin approve:"; curl -s -b "$T/cj" -X PATCH $U/api/v1/emails/2 -H "$J" -H "X-Requested-With: fetch" -d '{"status":"approved"}' | head -c 200; echo
echo "admin no csrf header (403):"; curl -s -b "$T/cj" -X PATCH $U/api/v1/emails/2 -H "$J" -d '{"status":"draft"}'; echo
echo "stats:"; curl -s $U/api/v1/stats -H "$K"; echo
echo "sitemap:"; curl -s $U/sitemap.xml | head -c 300; echo
echo "php errors:"; cat "$T/php.log" | grep -i "error\|warning" || echo none
