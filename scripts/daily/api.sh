#!/usr/bin/env bash
# Tiny API client for the daily automation.
#
#   bash scripts/daily/api.sh GET    /api/v1/plan/today
#   bash scripts/daily/api.sh POST   /api/v1/materials path/to/item.json
#   bash scripts/daily/api.sh DELETE /api/v1/materials/<slug>
#
# Secrets are NOT in this repo. They live in ~/.french-lms/config.env (override with FRENCH_CONFIG):
#   FRENCH_HOST=french.truespire.in      host name of the site
#   FRENCH_IP=217.217.249.12             optional: connect to this IP (skips DNS)
#   FRENCH_INSECURE=1                    optional: accept a self-signed certificate
#   FRENCH_KEY=<HERMES_API_KEY>          bearer key for the API (sent as X-Hermes-Key)
#   FRENCH_BASIC=user:password           optional: HTTP basic auth of the web server (private site)
#
# Prints the response body. Exit code 0 on HTTP 2xx, 1 otherwise (HTTP status goes to stderr).
set -euo pipefail

CFG="${FRENCH_CONFIG:-$HOME/.french-lms/config.env}"
[ -f "$CFG" ] || { echo "missing config file: $CFG" >&2; exit 2; }
# shellcheck disable=SC1090
. "$CFG"

method="${1:?usage: api.sh GET|POST|DELETE /api/v1/... [file.json]}"
path="${2:?usage: api.sh GET|POST|DELETE /api/v1/... [file.json]}"
file="${3:-}"

args=(-sS -m 90 -X "$method" -H "X-Hermes-Key: ${FRENCH_KEY:?FRENCH_KEY missing}" -H 'Accept: application/json')
[ -n "${FRENCH_IP:-}" ] && args+=(--resolve "${FRENCH_HOST}:443:${FRENCH_IP}")
[ "${FRENCH_INSECURE:-0}" = "1" ] && args+=(-k)
[ -n "${FRENCH_BASIC:-}" ] && args+=(-u "$FRENCH_BASIC")
if [ -n "$file" ]; then
    [ -f "$file" ] || { echo "no such file: $file" >&2; exit 2; }
    args+=(-H 'Content-Type: application/json' --data-binary "@$file")
fi

out="$(curl "${args[@]}" -w $'\n%{http_code}' "https://${FRENCH_HOST}${path}")"
code="${out##*$'\n'}"
body="${out%$'\n'*}"
printf '%s\n' "$body"
case "$code" in
    2??) exit 0 ;;
    *) echo "HTTP $code" >&2; exit 1 ;;
esac
