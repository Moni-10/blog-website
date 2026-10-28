$projectRoot = Split-Path -Parent $PSScriptRoot
$phpPath = 'C:\xampp\php\php.exe'
if (-not (Test-Path $phpPath)) { $phpPath = (Get-Command php -ErrorAction Stop).Source }
if (-not $env:SITE_DOMAIN) { $env:SITE_DOMAIN = 'hits9.com' }
Write-Host 'Hits9 preview: http://localhost:8100 (existing live API)'
& $phpPath -S '127.0.0.1:8100' -t $projectRoot "$projectRoot/router.php"
