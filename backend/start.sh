#!/bin/bash
find . -type f \( -name "*.php" -o -name "*.blade.php" -o -name "*.css" -o -name "*.js" -o -name "*.json" -o -name "*.scss" -o -name ".env" \) | entr -r php artisan serve
