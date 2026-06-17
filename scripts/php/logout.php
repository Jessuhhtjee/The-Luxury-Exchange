<?php

require __DIR__ . '/config/bootstrap.php';

$_SESSION = [];
session_destroy();

header('Location: ' . appUrl('pages/login_register.php'));
exit();
