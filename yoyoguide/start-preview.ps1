$env:SITE_DOMAIN = 'yoyoguide.com'
$env:BLOG_API_BASE_URL = 'https://api.blog-panel.clickstar.pl'
Set-Location -LiteralPath $PSScriptRoot
$phpExecutable = 'C:\xampp\php\php.exe'
if (-not (Test-Path -LiteralPath $phpExecutable)) {
    $phpExecutable = (Get-Command php -ErrorAction Stop).Source
}
Write-Host 'Yoyoguide preview: http://127.0.0.1:8080/ (live blog panel content)'
& $phpExecutable -S 127.0.0.1:8080 router.php
