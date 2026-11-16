# Optional preview with an official PHP 5.6 runtime, separate from WAMP Apache.
param([string]$PhpPath = "$PSScriptRoot\tests\runtime\php\php.exe", [int]$Port = 8056)
$ErrorActionPreference = 'Stop'
if (!(Test-Path -LiteralPath $PhpPath)) {
    throw 'Supply -PhpPath pointing to an official PHP 5.6 installation containing php_mysql.dll.'
}
$extensionPath = Join-Path (Split-Path -Parent $PhpPath) 'ext'
& $PhpPath -n "-dextension_dir=$extensionPath" -d extension=php_mysql.dll -d extension=php_mbstring.dll -d extension=php_openssl.dll -S "127.0.0.1:$Port" -t $PSScriptRoot
