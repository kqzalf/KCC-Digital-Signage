# KCC Digital Signage - Dependency Verification Script
# This script checks for all required dependencies and provides installation instructions

function Write-Header {
    param($Message)
    Write-Host "`n=== $Message ===" -ForegroundColor Cyan
}

function Write-Status {
    param($Name, $Installed, $Version = "", $Required = "")
    $status = if ($Installed) { "[OK]" } else { "[X]" }
    $color = if ($Installed) { "Green" } else { "Red" }
    Write-Host "$status $Name" -NoNewline -ForegroundColor $color
    if ($Version) {
        Write-Host " (v$Version)" -NoNewline
        if ($Required) {
            Write-Host " [Required: $Required]" -NoNewline
        }
    }
    Write-Host ""
}

function Get-PhpVersion {
    try {
        $output = php -v 2>&1
        if ($output -match "PHP (\d+\.\d+\.\d+)") {
            return $matches[1]
        }
    } catch {}
    return $null
}

function Get-ComposerVersion {
    try {
        $output = composer -V 2>&1
        if ($output -match "Composer version (\d+\.\d+\.\d+)") {
            return $matches[1]
        }
    } catch {}
    return $null
}

function Get-ApacheVersion {
    try {
        $output = httpd -v 2>&1
        if ($output -match "Apache/(\d+\.\d+\.\d+)") {
            return $matches[1]
        }
    } catch {}
    return $null
}

Write-Header "System Requirements"

# Check PHP
$phpVersion = Get-PhpVersion
Write-Status "PHP" ($phpVersion -ne $null) $phpVersion ">=7.4"

# Check Composer
$composerVersion = Get-ComposerVersion
Write-Status "Composer" ($composerVersion -ne $null) $composerVersion

# Check Apache
$apacheVersion = Get-ApacheVersion
Write-Status "Apache" ($apacheVersion -ne $null) $apacheVersion ">=2.4"

# Check required PHP extensions
Write-Header "PHP Extensions"
$requiredExtensions = @(
    "json",
    "fileinfo",
    "pdo",
    "mbstring",
    "xml",
    "curl"
)

if ($phpVersion) {
    $installedExtensions = php -m
    foreach ($ext in $requiredExtensions) {
        $installed = $installedExtensions -contains $ext
        Write-Status $ext $installed
    }
} else {
    Write-Host "Cannot check PHP extensions - PHP is not installed" -ForegroundColor Yellow
}

# Check directory structure
Write-Header "Directory Structure"
$requiredDirs = @(
    "content",
    "config",
    "scripts",
    "src",
    "templates",
    "vendor"
)

foreach ($dir in $requiredDirs) {
    $exists = Test-Path $dir
    Write-Status $dir $exists
}

# Check configuration files
Write-Header "Configuration Files"
$requiredFiles = @(
    ".env",
    "composer.json",
    "config/apache.conf"
)

foreach ($file in $requiredFiles) {
    $exists = Test-Path $file
    Write-Status $file $exists
}

# Installation Instructions
Write-Header "Installation Instructions"

if (-not $phpVersion) {
    Write-Host "`nTo install PHP:" -ForegroundColor Yellow
    Write-Host "1. Download PHP from https://windows.php.net/download/"
    Write-Host "2. Extract to C:\php"
    Write-Host "3. Add C:\php to your PATH environment variable"
    Write-Host "4. Copy php.ini-development to php.ini"
}

if (-not $composerVersion) {
    Write-Host "`nTo install Composer:" -ForegroundColor Yellow
    Write-Host "1. Download Composer from https://getcomposer.org/download/"
    Write-Host "2. Run the installer"
}

if (-not $apacheVersion) {
    Write-Host "`nTo install Apache:" -ForegroundColor Yellow
    Write-Host "1. Download Apache from https://www.apachelounge.com/download/"
    Write-Host "2. Extract to C:\Apache24"
    Write-Host "3. Run httpd.exe -k install"
}

# Summary
Write-Header "Summary"
$allInstalled = $phpVersion -and $composerVersion -and $apacheVersion
if ($allInstalled) {
    Write-Host "All core dependencies are installed!" -ForegroundColor Green
} else {
    Write-Host "Some dependencies are missing. Please install them before proceeding." -ForegroundColor Red
}

# Next Steps
Write-Header "Next Steps"
Write-Host "1. Install missing dependencies (if any)"
Write-Host "2. Run 'composer install' to install PHP dependencies"
Write-Host "3. Copy .env.example to .env and configure it"
Write-Host "4. Configure Apache virtual host"
Write-Host "5. Set proper directory permissions" 