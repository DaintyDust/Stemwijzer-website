#!/bin/bash
# Auto-deployment script that runs on the server

echo "=== Starting Stemwijzer deployment ==="
cd ~/pub || exit 1
git pull

echo "Stopping existing services..."
pkill -f caddy 2>/dev/null
pkill -f php 2>/dev/null
sleep 2

echo "Creating Caddyfile..."
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

echo "Starting PHP server..."
nohup php -S 127.0.0.1:9000 -t /home/daintydust/pub > ~/php.log 2>&1 &

echo "Starting Caddy..."
nohup caddy run --config ~/Caddyfile > ~/caddy.log 2>&1 &

sleep 3

echo "Checking deployment..."
if [ -S ~/.daintydust.hackclub.app.webserver.sock ]; then
    echo "✓ Socket created successfully"
    ls -la ~/.daintydust.hackclub.app.webserver.sock
else
    echo "✗ Socket not found"
fi

if pgrep -f "php -S.*9000" > /dev/null; then
    echo "✓ PHP server running"
else
    echo "✗ PHP server not running"
fi

if pgrep -f "caddy run" > /dev/null; then
    echo "✓ Caddy running"
else
    echo "✗ Caddy not running"
fi

echo "=== Deployment complete! ==="
echo "Visit: https://daintydust.hackclub.app"
