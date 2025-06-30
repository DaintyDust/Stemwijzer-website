<?php
// Session compatibility layer
if (!function_exists('session_start')) {
    $_SESSION = array();
    function session_start() { return true; }
    function session_destroy() { $_SESSION = array(); return true; }
    function session_id($id = null) { return $id ? true : 'fake_session_id'; }
    function session_name($name = null) { return $name ? true : 'PHPSESSID'; }
} else {
    session_start();
}

require 'private/database.php';
require 'private/checklogin.php';
?>