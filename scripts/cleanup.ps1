# KCC Digital Signage - Cleanup Script
# This script removes old directories and migrates content to the new structure

# Function to write log messages
function Write-Log {
    param($Message)
    Write-Host "[$([DateTime]::Now.ToString('yyyy-MM-dd HH:mm:ss'))] $Message"
}

# Set working directory to the repository root
Set-Location $PSScriptRoot\..
$REPO_ROOT = Get-Location

# Create new content structure
Write-Log "Creating new content directory structure..."
New-Item -ItemType Directory -Force -Path "content" | Out-Null

# Function to migrate content
function Migrate-Content {
    param(
        [string]$Source,
        [string]$Location,
        [string]$Type,
        [string]$Orientation
    )

    if (Test-Path $Source) {
        $target = "content\$Location\$Orientation\$Type"
        New-Item -ItemType Directory -Force -Path $target | Out-Null
        
        # Move all image and video files
        Get-ChildItem -Path $Source -File -Include "*.jpg","*.jpeg","*.png","*.mp4" -Recurse | 
        ForEach-Object {
            $targetFile = Join-Path $target $_.Name
            if (Test-Path $targetFile) {
                $newName = [System.IO.Path]::GetFileNameWithoutExtension($_.Name) + "_" + (Get-Date -Format "yyyyMMddHHmmss") + $_.Extension
                Move-Item $_.FullName -Destination (Join-Path $target $newName) -Force
            } else {
                Move-Item $_.FullName -Destination $target -Force
            }
        }
    }
}

# Migrate content from old structure to new
Write-Log "Migrating content to new structure..."

# Migrate vertical content
Migrate-Content "vert flower" "Flower" "flower" "vertical"
Migrate-Content "vert nonflower" "NonFlower" "nonflower" "vertical"
Migrate-Content "vert other" "Other" "other" "vertical"

# Migrate horizontal content
Migrate-Content "Flower" "Flower" "flower" "horizontal"
Migrate-Content "nonflower" "NonFlower" "nonflower" "horizontal"
Migrate-Content "other" "Other" "other" "horizontal"

# Migrate location-specific content
@("Kearney", "Excelsior") | ForEach-Object {
    $location = $_
    Migrate-Content "$location\Flower" $location "flower" "horizontal"
    Migrate-Content "$location\nonflower" $location "nonflower" "horizontal"
    Migrate-Content "$location\other" $location "other" "horizontal"
    Migrate-Content "$location\vert flower" $location "flower" "vertical"
    Migrate-Content "$location\vert nonflower" $location "nonflower" "vertical"
    Migrate-Content "$location\vert other" $location "other" "vertical"
}

# Remove old directories
Write-Log "Removing old directories..."
$oldDirs = @(
    "vert flower",
    "vert nonflower",
    "vert other",
    "Flower",
    "nonflower",
    "other",
    "Kearney",
    "Excelsior",
    "dashboard"
)

foreach ($dir in $oldDirs) {
    if (Test-Path $dir) {
        Write-Log "Removing $dir..."
        Remove-Item -Path $dir -Recurse -Force
    }
}

Write-Log "Cleanup complete!" 