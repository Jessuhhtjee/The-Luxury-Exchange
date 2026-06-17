<?php

require __DIR__ . '/config/bootstrap.php';

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: ' . appUrl('pages/login_register.php'));
        exit();
    }
}

function currentUserId(): ?int
{
    return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
}

function currentUsername(): string
{
    return $_SESSION['username'] ?? '';
}

function currentUser(): ?array
{
    if (!isLoggedIn()) {
        return null;
    }

    global $db;

    $stmt = $db->prepare('SELECT id, username, email, avatar, created_at FROM users WHERE id = ?');
    $stmt->execute([currentUserId()]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    return $user ?: null;
}
