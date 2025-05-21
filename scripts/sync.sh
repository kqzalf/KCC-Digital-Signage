#!/bin/bash

# KCC Digital Signage - Content Synchronization Script
# This script synchronizes content from GitHub and manages the content structure

# Exit on any error
set -e

# Load environment variables
if [ -f .env ]; then
    export $(cat .env | grep -v '^#' | xargs)
fi

# Log function with timestamps
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1"
}

# Error handling function
handle_error() {
    log "ERROR: An error occurred on line $1"
    exit 1
}

# Set error handler
trap 'handle_error $LINENO' ERR

# Set working directory to the repository root
cd "$(dirname "$0")/.."
REPO_ROOT=$(pwd)

log "Starting content synchronization..."

# Pull latest changes from Git
if [ "$GIT_AUTO_PULL" = "true" ]; then
    log "Pulling latest changes from Git..."
    git fetch origin
    git reset --hard origin/main
    git clean -df
fi

# Ensure content directories exist
log "Ensuring content directory structure..."
for location in ${LOCATIONS//,/ }; do
    for orientation in "horizontal" "vertical"; do
        for type in ${CONTENT_TYPES//,/ }; do
            dir="content/$location/$orientation/$type"
            if [ ! -d "$dir" ]; then
                log "Creating directory: $dir"
                mkdir -p "$dir"
            fi
        done
    done
done

# Set proper permissions
log "Setting permissions..."
find content -type d -exec chmod 755 {} \;
find content -type f -exec chmod 644 {} \;

# Clean up old files if needed
if [ -n "$MAX_FILES_PER_DIRECTORY" ]; then
    log "Cleaning up old files..."
    find content -type d -exec sh -c '
        dir="$1"
        count=$(ls -1 "$dir" | wc -l)
        if [ "$count" -gt "$MAX_FILES_PER_DIRECTORY" ]; then
            cd "$dir"
            ls -t | tail -n +$(($MAX_FILES_PER_DIRECTORY + 1)) | xargs -r rm
        fi
    ' sh {} \;
fi

log "Synchronization complete!" 