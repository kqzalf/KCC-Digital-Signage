#!/bin/bash

# KCC Digital Signage - Cleanup Script
# This script removes old directories and migrates content to the new structure

# Exit on any error
set -e

# Log function with timestamps
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1"
}

# Set working directory to the repository root
cd "$(dirname "$0")/.."
REPO_ROOT=$(pwd)

# Create new content structure
mkdir -p content

# Function to migrate content
migrate_content() {
    local src="$1"
    local location="$2"
    local type="$3"
    local orientation="$4"

    if [ -d "$src" ]; then
        local target="content/$location/$orientation/$type"
        mkdir -p "$target"
        
        # Move all image and video files
        find "$src" -type f \( -name "*.jpg" -o -name "*.jpeg" -o -name "*.png" -o -name "*.mp4" \) -exec mv {} "$target/" \;
        
        # Remove old directory if empty
        rmdir "$src" 2>/dev/null || true
    fi
}

# Migrate content from old structure to new
log "Migrating content to new structure..."

# Migrate vertical content
migrate_content "vert flower" "Flower" "flower" "vertical"
migrate_content "vert nonflower" "NonFlower" "nonflower" "vertical"
migrate_content "vert other" "Other" "other" "vertical"

# Migrate horizontal content
migrate_content "Flower" "Flower" "flower" "horizontal"
migrate_content "nonflower" "NonFlower" "nonflower" "horizontal"
migrate_content "other" "Other" "other" "horizontal"

# Migrate location-specific content
for location in Kearney Excelsior; do
    migrate_content "$location/Flower" "$location" "flower" "horizontal"
    migrate_content "$location/nonflower" "$location" "nonflower" "horizontal"
    migrate_content "$location/other" "$location" "other" "horizontal"
    migrate_content "$location/vert flower" "$location" "flower" "vertical"
    migrate_content "$location/vert nonflower" "$location" "nonflower" "vertical"
    migrate_content "$location/vert other" "$location" "other" "vertical"
    
    # Remove old location directory if empty
    rmdir "$location" 2>/dev/null || true
done

# Remove old dashboard directory
rm -rf dashboard

log "Cleanup complete!" 