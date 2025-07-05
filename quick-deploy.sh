#!/bin/bash

# Simple deployment for Hack Club Nest
echo "=== Quick Deploy for Hack Club Nest ==="

# Go to website directory
cd ~/pub
git pull

# Kill existing processes
pkill -f caddy 2>/dev/null
pkill -f php 2>/dev/null
sleep 2

# Create minimal Caddyfile
cat > ~/Caddyfile << 'EOF'
{
    admin unix//home/daintydust/caddy-admin.sock
}

daintydust.hackclub.app {
    bind unix/.daintydust.hackclub.app.webserver.sock|777
    root * /home/daintydust/pub
    file_server
    php_fastcgi 127.0.0.1:9000
}
EOF

# Start PHP built-in server
echo "Starting PHP server..."
nohup php -S 127.0.0.1:9000 -t /home/daintydust/pub &

# Start Caddy  
echo "Starting Caddy..."
nohup caddy run --config ~/Caddyfile &

# Wait and check
sleep 5
echo "Socket status:"
ls -la ~/.*.sock 2>/dev/null || echo "No sockets found"

echo "Process status:"
ps aux | grep -E "(php.*9000|caddy)" | grep -v grep

echo "Done! Check https://daintydust.hackclub.app"
