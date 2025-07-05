#!/bin/bash

# Complete deployment script for Stemwijzer on Hack Club Nest
# This script will set up everything needed for the PHP website

echo "=== Stemwijzer Deployment Script ==="
echo "Starting deployment at $(date)"

# Kill any existing processes
echo "Stopping existing services..."
pkill -f php 2>/dev/null || true
pkill -f caddy 2>/dev/null || true
sleep 2

# Navigate to the pub directory
cd ~/pub || { echo "Error: Could not find ~/pub directory"; exit 1; }

# Pull latest changes from GitHub
echo "Updating code from GitHub..."
git pull origin main

# Create necessary directories
echo "Creating directories..."
mkdir -p private/data
mkdir -p private/db
chmod 755 private/data
chmod 755 private/db

# Create a simple test file
echo "<?php echo 'PHP is working! Time: ' . date('Y-m-d H:i:s'); ?>" > test.php

# Create the Caddyfile for Hack Club's routing system
echo "Creating Caddyfile..."
cat > ~/Caddyfile << 'CADDY_EOF'
{
    admin unix//home/daintydust/caddy-admin.sock
    auto_https off
}

daintydust.hackclub.app {
    bind unix/.daintydust.hackclub.app.webserver.sock|777
    root * /home/daintydust/pub
    
    # Enable PHP processing
    php_fastcgi unix//home/daintydust/php-fpm.sock
    
    # Try files in order
    try_files {path} {path}.php {path}/index.php
    
    # File server
    file_server {
        index index.php index.html
        hide .git .env private/data
    }
}
CADDY_EOF

# Create PHP-FPM configuration
echo "Creating PHP-FPM configuration..."
mkdir -p ~/php
cat > ~/php/fpm.conf << 'PHP_EOF'
[global]
error_log = /home/daintydust/php/error.log
daemonize = no

[www]
listen = /home/daintydust/php-fpm.sock
listen.mode = 0666
pm = ondemand
pm.max_children = 5
pm.process_idle_timeout = 10s
chdir = /home/daintydust/pub
PHP_EOF

# Start PHP-FPM
echo "Starting PHP-FPM..."
nohup php-fpm --nodaemonize --fpm-config ~/php/fpm.conf > ~/php/fpm.log 2>&1 &
PHP_PID=$!
sleep 3

# Check if PHP-FPM started
if ! ps -p $PHP_PID > /dev/null 2>&1; then
    echo "Warning: PHP-FPM may not have started properly"
    echo "PHP-FPM log:"
    cat ~/php/fmp.log 2>/dev/null || echo "No PHP-FPM log found"
fi

# Start Caddy
echo "Starting Caddy..."
nohup caddy run --config ~/Caddyfile > ~/caddy.log 2>&1 &
CADDY_PID=$!
sleep 5

# Check if Caddy started
if ! ps -p $CADDY_PID > /dev/null 2>&1; then
    echo "Warning: Caddy may not have started properly"
    echo "Caddy log:"
    tail -20 ~/caddy.log 2>/dev/null || echo "No Caddy log found"
fi

# Verify socket creation
echo "Checking services..."
if [ -S ~/.daintydust.hackclub.app.webserver.sock ]; then
    echo "✓ Caddy socket created successfully"
    ls -la ~/.daintydust.hackclub.app.webserver.sock
else
    echo "✗ Caddy socket not found"
    echo "Looking for any sockets:"
    ls -la ~/.*.sock 2>/dev/null || echo "No sockets found"
fi

if [ -S ~/php-fpm.sock ]; then
    echo "✓ PHP-FPM socket created successfully"
else
    echo "✗ PHP-FPM socket not found"
fi

# Check processes
echo "Active processes:"
ps aux | grep -E "(php-fpm|caddy)" | grep -v grep || echo "No processes found"

# Test local functionality
echo "Testing PHP functionality..."
if php -r "echo 'PHP syntax OK\n';" 2>/dev/null; then
    echo "✓ PHP is working"
else
    echo "✗ PHP has issues"
fi

# Test if we can read our files
if [ -f "index.php" ]; then
    echo "✓ index.php found"
else
    echo "✗ index.php not found"
fi

if [ -f "private/database_file.php" ]; then
    echo "✓ database_file.php found"
else
    echo "✗ database_file.php not found"
fi

# Show recent logs
echo ""
echo "=== Recent Caddy Log ==="
tail -10 ~/caddy.log 2>/dev/null || echo "No Caddy log available"

echo ""
echo "=== Recent PHP-FPM Log ==="
tail -10 ~/php/fpm.log 2>/dev/null || echo "No PHP-FPM log available"

echo ""
echo "=== Deployment Complete ==="
echo "Website should be accessible at: https://daintydust.hackclub.app"
echo "Test page available at: https://daintydust.hackclub.app/test.php"
echo ""
echo "To check status later, run:"
echo "  ps aux | grep -E '(php-fpm|caddy)' | grep -v grep"
echo "  ls -la ~/.*.sock"
echo ""
echo "To stop services, run:"
echo "  pkill -f php-fpm"
echo "  pkill -f caddy"
