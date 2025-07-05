# Manual Deployment Commands for Stemwijzer on Hack Club Nest

# Copy and paste these commands one by one in your SSH session

# Step 1: Connect to server

ssh daintydust@hackclub.app

# Step 2: Navigate and update code

cd ~/pub
git pull

# Step 3: Stop any existing services

pkill -f php 2>/dev/null
pkill -f caddy 2>/dev/null

# Step 4: Create directories

mkdir -p private/data
mkdir -p php

# Step 5: Create test file

echo '<?php echo "PHP is working! Time: " . date("Y-m-d H:i:s"); ?>' > test.php

# Step 6: Create Caddyfile

cat > ~/Caddyfile << 'EOF'
{
admin unix//home/daintydust/caddy-admin.sock
auto_https off
}

daintydust.hackclub.app {
bind unix/.daintydust.hackclub.app.webserver.sock|777
root \* /home/daintydust/pub
php_fastcgi unix//home/daintydust/php-fpm.sock
try_files {path} {path}.php {path}/index.php
file_server {
index index.php index.html
hide .git .env private/data
}
}
EOF

# Step 7: Create PHP-FPM config

cat > ~/php/fpm.conf << 'EOF'
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
EOF

# Step 8: Start PHP-FPM

nohup php-fpm --nodaemonize --fpm-config ~/php/fpm.conf > ~/php/fpm.log 2>&1 &

# Step 9: Start Caddy

nohup caddy run --config ~/Caddyfile > ~/caddy.log 2>&1 &

# Step 10: Check if everything is working

sleep 5
ls -la ~/.\*.sock
ps aux | grep -E "(php-fpm|caddy)" | grep -v grep

# Step 11: Test the website

# Visit https://daintydust.hackclub.app in your browser

# Or test: https://daintydust.hackclub.app/test.php

# If there are issues, check logs:

# tail ~/caddy.log

# tail ~/php/fpm.log
