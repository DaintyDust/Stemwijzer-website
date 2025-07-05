#!/bin/bash

# Diagnostic script for Stemwijzer deployment
echo "=== Stemwijzer Diagnostics ==="
echo "Time: $(date)"
echo ""

echo "=== Current Directory ==="
pwd
echo ""

echo "=== Directory Contents ==="
ls -la
echo ""

echo "=== PHP Version ==="
php --version
echo ""

echo "=== Caddy Version ==="
caddy version 2>/dev/null || echo "Caddy not found in PATH"
echo ""

echo "=== Running Processes ==="
ps aux | grep -E "(php|caddy)" | grep -v grep
echo ""

echo "=== Socket Files ==="
ls -la ~/.*.sock 2>/dev/null || echo "No socket files found"
echo ""

echo "=== Log Files ==="
echo "Caddy log (last 10 lines):"
tail -10 ~/caddy.log 2>/dev/null || echo "No Caddy log found"
echo ""
echo "PHP-FPM log (last 10 lines):"
tail -10 ~/php/fpm.log 2>/dev/null || echo "No PHP-FPM log found"
echo ""

echo "=== File Permissions ==="
ls -la ~/Caddyfile 2>/dev/null || echo "No Caddyfile found"
ls -la ~/php/fpm.conf 2>/dev/null || echo "No PHP-FPM config found"
echo ""

echo "=== Network/Port Status ==="
netstat -tlnp 2>/dev/null | grep -E ":(80|443|9000|8080)" || echo "No relevant ports listening"
echo ""

echo "=== Database Files ==="
ls -la private/data/ 2>/dev/null || echo "No database data directory found"
echo ""

echo "=== Test PHP Execution ==="
php -r "echo 'PHP execution test: OK\n';" 2>/dev/null || echo "PHP execution failed"
echo ""

echo "=== File Structure Check ==="
echo "Key files present:"
[ -f "index.php" ] && echo "✓ index.php" || echo "✗ index.php missing"
[ -f "private/database_file.php" ] && echo "✓ database_file.php" || echo "✗ database_file.php missing"
[ -f "private/databaseinfo.php" ] && echo "✓ databaseinfo.php" || echo "✗ databaseinfo.php missing"
[ -f "php_require/autoload.php" ] && echo "✓ autoload.php" || echo "✗ autoload.php missing"
echo ""

echo "=== End Diagnostics ==="
