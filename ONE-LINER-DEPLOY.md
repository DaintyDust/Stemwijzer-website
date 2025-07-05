# One-liner deployment for Hack Club Nest

# Copy this entire command and paste it in your SSH session:

ssh daintydust@hackclub.app 'cd ~/pub && git pull && pkill -f caddy 2>/dev/null; pkill -f php 2>/dev/null; sleep 2 && cat > ~/Caddyfile << "EOF"
{
admin unix//home/daintydust/caddy-admin.sock
}
daintydust.hackclub.app {
bind unix/.daintydust.hackclub.app.webserver.sock|777
root _ /home/daintydust/pub
file_server
php_fastcgi 127.0.0.1:9000
}
EOF
nohup php -S 127.0.0.1:9000 -t /home/daintydust/pub > /dev/null 2>&1 & nohup caddy run --config ~/Caddyfile > /dev/null 2>&1 & sleep 3 && echo "Deployment complete!" && ls -la ~/._.sock'

# Alternative if the above doesn't work, run these commands step by step:

# 1. Connect to server

ssh daintydust@hackclub.app

# 2. Once connected, run this:

cd ~/pub && git pull && pkill -f caddy; pkill -f php; sleep 2

# 3. Then this:

cat > ~/Caddyfile << 'EOF'
{
admin unix//home/daintydust/caddy-admin.sock
}
daintydust.hackclub.app {
bind unix/.daintydust.hackclub.app.webserver.sock|777
root \* /home/daintydust/pub
file_server
php_fastcgi 127.0.0.1:9000
}
EOF

# 4. Start services:

nohup php -S 127.0.0.1:9000 -t /home/daintydust/pub &
nohup caddy run --config ~/Caddyfile &

# 5. Check status:

sleep 3 && ls -la ~/.*.sock && ps aux | grep -E "(php.*9000|caddy)" | grep -v grep
