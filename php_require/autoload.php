<?php
ini_set('session.save_path', '/home/daintydust/php_sessions');
if (function_exists('session_start')) {
    if (!is_dir('/home/daintydust/php_sessions')) {
        @mkdir('/home/daintydust/php_sessions', 0700, true);
    }
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
} else {
    error_log('session_start() unavailable under current php-fpm build');
}
require 'private/database.php';
require 'private/checklogin.php';
?>