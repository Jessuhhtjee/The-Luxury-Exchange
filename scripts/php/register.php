<?php

require __DIR__ . '/config/bootstrap.php';

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$avatar = $_POST['avatar'] ?? 'avatar1.png';

if ($username === '' || $email === '' || $password === '') {
    header('Location: ../../pages/login_register.php?error=empty&form=register');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../../pages/login_register.php?error=email&form=register');
    exit();
}

$allowedAvatars = ['avatar1.png', 'avatar2.png', 'avatar3.png'];
if (!in_array($avatar, $allowedAvatars, true)) {
    $avatar = 'avatar1.png';
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $db->prepare('INSERT INTO users (username, email, password_hash, avatar) VALUES (?, ?, ?, ?)');

try {
    $stmt->execute([$username, $email, $passwordHash, $avatar]);
} catch (PDOException $e) {
    header('Location: ../../pages/login_register.php?error=exists&form=register');
    exit();
}

header('Location: ../../pages/login_register.php?success=registered');
exit();
