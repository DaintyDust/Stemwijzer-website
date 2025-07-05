#!/bin/bash

# Simple deployment script for Hack Club Nest
# This script sets up a PHP website without requiring sudo access

echo "Setting up Stemwijzer website on Hack Club Nest..."

# Kill any existing processes
pkill -f php 2>/dev/null
pkill -f caddy 2>/dev/null

# Navigate to the pub directory
cd ~/pub || exit 1

# Pull latest changes
git pull

# Create data directory for file-based database
mkdir -p private/data

# Create a simple Caddyfile that follows Hack Club conventions
cat > ~/Caddyfile << 'EOF'
{
    admin unix//home/daintydust/caddy-admin.sock
}

daintydust.hackclub.app {
    bind unix/.daintydust.hackclub.app.webserver.sock|777
    root * /home/daintydust/pub
    
    # Enable PHP processing
    php_fastcgi 127.0.0.1:9000
    
    # File server with PHP support
    file_server {
        index index.php index.html
        hide .git .env
    }
    
    # Handle PHP files
    @php path *.php
    reverse_proxy @php 127.0.0.1:9000
}
EOF

# Start PHP built-in server on port 9000 (FastCGI mode would be ideal, but built-in server as fallback)
echo "Starting PHP server..."
nohup php -S 127.0.0.1:9000 -t /home/daintydust/pub > ~/php-server.log 2>&1 &

# Start Caddy
echo "Starting Caddy..."
nohup caddy run --config ~/Caddyfile > ~/caddy.log 2>&1 &

echo "Services started. Check logs at ~/php-server.log and ~/caddy.log"
echo "Socket should be created at ~/.daintydust.hackclub.app.webserver.sock"

# Wait a moment and check status
sleep 3
if [ -S ~/.daintydust.hackclub.app.webserver.sock ]; then
    echo "✓ Socket created successfully"
else
    echo "✗ Socket not found"
fi

if pgrep -f "php -S" > /dev/null; then
    echo "✓ PHP server is running"
else
    echo "✗ PHP server not running"
fi

if pgrep -f "caddy run" > /dev/null; then
    echo "✓ Caddy server is running"
else
    echo "✗ Caddy server not running"
fi

echo "Setup complete!"
