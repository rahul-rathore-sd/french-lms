# Deploy to shared hosting (PHP + MySQL)

1. In cPanel create a **MySQL database + user** (note the full names, e.g. `cpuser_hermes`).
2. Build the package (from `french-lms/`):
   ```powershell
   python build-deploy.py --domain https://yourdomain.com --db-name cpuser_hermes --db-user cpuser_hermes --db-pass "DBPASSWORD" --notify you@yourdomain.com
   ```
   This runs `npm run build`, writes `dist/public_html/` and `dist/public_html.zip`, and creates
   `dist/CREDENTIALS.txt` (admin password + Hermes API key). Keep that file private, never upload it.
3. cPanel File Manager -> `public_html` -> upload `public_html.zip` -> Extract. Make sure `.htaccess` is there (show hidden files).
4. Open `https://yourdomain.com/admin/`, sign in, and check Overview. Tables are created automatically on first request.
5. Enable SSL (AutoSSL) and uncomment the HTTPS redirect lines in `.htaccess`.
6. On your PC connect Hermes:
   ```powershell
   python D:\hermes\hermes.py config --url https://yourdomain.com --key <HERMES_API_KEY>
   python D:\hermes\hermes.py status
   ```
7. Email: `python hermes.py mail config --user info@yourdomain.com --imap-host mail.yourdomain.com --smtp-host mail.yourdomain.com --password-prompt`
   then `hermes mail sync`, `hermes mail draft`; approve replies in the dashboard; `hermes mail send`.
8. LLM for drafting: start FreeLLM API (Docker) and `hermes config --llm-key <FreeLLM unified key>`.
9. Automate (Task Scheduler): `python D:\hermes\hermes.py autopilot` every few hours and `... mail send` every 15 minutes.

Local test without MySQL: add `--local-test`, then `php -S localhost:8080 -t dist/public_html server/dev-router.php`.
