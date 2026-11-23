# Rebuilds the learner UI (frontend/) and copies the static export into Laravel's public/ folder.
# Run from the project root:   powershell -File scripts\build-frontend.ps1
$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent $PSScriptRoot
Push-Location "$root\frontend"
try {
    if (-not (Test-Path node_modules)) { npm ci --no-audit --no-fund }
    npm run build
    if (-not (Test-Path out\index.html)) { throw 'frontend/out/index.html missing: the build failed.' }
    # drop the previous build's hashed assets so stale CSS/JS files do not pile up in git
    if (Test-Path "$root\public\_next") { Remove-Item "$root\public\_next" -Recurse -Force }
    # copy into public/ (Laravel's own public/index.php, .htaccess, robots.txt are left alone)
    robocopy "$root\frontend\out" "$root\public" /E /NFL /NDL /NJH /NJS /NP | Out-Null
    if ($LASTEXITCODE -ge 8) { throw "robocopy failed ($LASTEXITCODE)" }
    $global:LASTEXITCODE = 0
    Write-Host 'Frontend copied to public/.'
} finally {
    Pop-Location
}
