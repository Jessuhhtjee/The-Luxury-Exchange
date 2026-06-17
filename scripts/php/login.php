<?php

require __DIR__ . '/config/bootstrap.php';

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    header('Location: ' . appUrl('pages/login_register.php?error=empty'));
    exit();
}

$stmt = $db->prepare('SELECT id, username, password_hash FROM users WHERE email = ?');
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password_hash'])) {
    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['username'] = $user['username'];
    header('Location: ' . appUrl('pages/profile.php'));
    exit();
}

header('Location: ' . appUrl('pages/login_register.php?error=invalid'));
exit();
