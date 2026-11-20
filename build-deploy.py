#!/usr/bin/env python3
"""
Builds the shared-hosting package.

    python build-deploy.py --domain https://yourdomain.com --db-name cpuser_hermes --db-user cpuser_hermes --db-pass '...' --notify you@domain.com

Output: dist/public_html/  (+ dist/public_html.zip)  -> upload the CONTENTS of public_html to the host's public_html.
Secrets (admin password, Hermes API key) are generated once and kept in dist/CREDENTIALS.txt so rebuilds keep them.
Add --local-test to use SQLite instead of MySQL for trying the site on your PC.
"""

import argparse
import json
import secrets
import shutil
import subprocess
import sys
import zipfile
from pathlib import Path

ROOT = Path(__file__).resolve().parent
DIST = ROOT / "dist"
PUB = DIST / "public_html"
SECRETS = DIST / "CREDENTIALS.txt"


def php_str(v: str) -> str:
    return "'" + v.replace("\\", "\\\\").replace("'", "\\'") + "'"


def password_hash(pw: str) -> str:
    """bcrypt hash compatible with PHP password_verify (uses PHP itself so no extra deps)."""
    try:
        out = subprocess.run(["php", "-r", "echo password_hash($argv[1], PASSWORD_BCRYPT);", pw],
                             capture_output=True, text=True, check=True)
        return out.stdout.strip()
    except Exception:
        raise SystemExit("PHP CLI is required to hash the admin password (php.net). Install PHP or pass --admin-hash.")


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument("--domain", default="https://yourdomain.com", help="Public URL, e.g. https://example.com")
    ap.add_argument("--db-host", default="localhost")
    ap.add_argument("--db-name", default="CHANGE_ME")
    ap.add_argument("--db-user", default="CHANGE_ME")
    ap.add_argument("--db-pass", default="CHANGE_ME")
    ap.add_argument("--notify", default="", help="Email that receives new-lead alerts")
    ap.add_argument("--admin-password", help="Dashboard password (random if omitted, first build only)")
    ap.add_argument("--local-test", action="store_true", help="SQLite config for testing on your PC")
    ap.add_argument("--skip-build", action="store_true", help="Reuse the existing ./out folder")
    args = ap.parse_args()

    if not args.skip_build:
        npm = "npm.cmd" if sys.platform == "win32" else "npm"
        subprocess.run([npm, "run", "build"], cwd=ROOT, check=True)
    out = ROOT / "out"
    if not (out / "index.html").exists():
        raise SystemExit("./out/index.html missing. Run `npm run build` first.")

    DIST.mkdir(exist_ok=True)
    creds = {}
    if SECRETS.exists():
        for line in SECRETS.read_text(encoding="utf-8").splitlines():
            if ":" in line and not line.startswith("#"):
                k, v = line.split(":", 1)
                creds[k.strip()] = v.strip()
    hermes_key = creds.get("HERMES_API_KEY") or secrets.token_hex(24)
    admin_pw = args.admin_password or creds.get("ADMIN_PASSWORD") or secrets.token_urlsafe(12)

    if PUB.exists():
        shutil.rmtree(PUB)
    shutil.copytree(out, PUB)
    shutil.copytree(ROOT / "server" / "api", PUB / "api", ignore=shutil.ignore_patterns("config.sample.php", "config.php"))
    shutil.copytree(ROOT / "server" / "blog", PUB / "blog")
    shutil.copy(ROOT / "server" / "htaccess-root", PUB / ".htaccess")
    books_src = ROOT.parent / "books"
    if books_src.is_dir():
        (PUB / "books").mkdir(parents=True, exist_ok=True)
        for pdf in books_src.glob("*.pdf"):
            dest = PUB / "books" / pdf.name
            if not dest.exists():
                shutil.copy(pdf, dest)
    (PUB / "robots.txt").write_text(
        f"User-agent: *\nDisallow: /admin/\nDisallow: /api/\nSitemap: {args.domain.rstrip('/')}/sitemap.xml\n", encoding="utf-8")

    if args.local_test:
        db = ["    'db_driver' => 'sqlite',", f"    'sqlite_path' => {php_str(str((DIST / 'local.sqlite').as_posix()))},"]
    else:
        db = ["    'db_driver' => 'mysql',", f"    'db_host' => {php_str(args.db_host)},", f"    'db_name' => {php_str(args.db_name)},",
              f"    'db_user' => {php_str(args.db_user)},", f"    'db_pass' => {php_str(args.db_pass)},"]
    config = "<?php\nreturn [\n" + "\n".join(db) + "\n" + "\n".join([
        f"    'site_url' => {php_str(args.domain.rstrip('/'))},",
        f"    'notify_email' => {php_str(args.notify)},",
        f"    'admin_password_hash' => {php_str(password_hash(admin_pw))},",
        f"    'hermes_api_key' => {php_str(hermes_key)},",
    ]) + "\n];\n"
    (PUB / "api" / "config.php").write_text(config, encoding="utf-8")

    SECRETS.write_text(
        "# Keep this file private. Never upload it.\n"
        f"ADMIN_URL: {args.domain.rstrip('/')}/admin/\n"
        f"ADMIN_PASSWORD: {admin_pw}\n"
        f"HERMES_API_KEY: {hermes_key}\n", encoding="utf-8")

    zip_path = DIST / "public_html.zip"
    with zipfile.ZipFile(zip_path, "w", zipfile.ZIP_DEFLATED) as z:
        for f in PUB.rglob("*"):
            if f.is_file():
                z.write(f, f.relative_to(PUB).as_posix())

    print(f"\nBuilt {PUB}  ({sum(1 for _ in PUB.rglob('*') if _.is_file())} files)")
    print(f"Zip for cPanel File Manager: {zip_path}")
    print(f"Admin password + Hermes key saved in: {SECRETS}")
    print(f"Next: python ../hermes.py config --url {args.domain.rstrip('/')} --key <HERMES_API_KEY from that file>")


if __name__ == "__main__":
    main()
