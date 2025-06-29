<?php
require 'php_require/autoload.php';

logoutUser();
header('Location: login.php');

exit();
