#!/bin/bash

# Safe Delete Script for Cursor/VS Code
# This script moves files to .trash instead of deleting them

TRASH_DIR=".trash"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")

# Create .trash directory if it doesn't exist
mkdir -p "$TRASH_DIR"

# Function to move file to trash
move_to_trash() {
    local file_path="$1"
    local file_name=$(basename "$file_path")
    local file_dir=$(dirname "$file_path")
    
    # Create timestamped filename to avoid conflicts
    local timestamped_name="${TIMESTAMP}_${file_name}"
    
    # Move file to trash
    if mv "$file_path" "$TRASH_DIR/$timestamped_name"; then
        echo "✅ File moved to trash: $file_path -> $TRASH_DIR/$timestamped_name"
        return 0
    else
        echo "❌ Failed to move file to trash: $file_path"
        return 1
    fi
}

# Check if file path is provided
if [ $# -eq 0 ]; then
    echo "Usage: $0 <file_path>"
    exit 1
fi

# Process each file
for file_path in "$@"; do
    if [ -e "$file_path" ]; then
        move_to_trash "$file_path"
    else
        echo "⚠️  File not found: $file_path"
    fi
done
