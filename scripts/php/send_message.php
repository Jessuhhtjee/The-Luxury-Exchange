<?php

require __DIR__ . '/config/bootstrap.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . appUrl('pages/login_register.php'));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . appUrl('pages/messages.php'));
    exit();
}

$senderId = (int) $_SESSION['user_id'];
$receiverId = (int) ($_POST['receiver_id'] ?? 0);
$offerId = (int) ($_POST['offer_id'] ?? 0);
$body = trim($_POST['body'] ?? '');

if ($receiverId <= 0 || $body === '') {
    header('Location: ' . appUrl('pages/messages.php?to=' . $receiverId . '&error=empty'));
    exit();
}

if ($receiverId === $senderId) {
    header('Location: ' . appUrl('pages/messages.php?error=self'));
    exit();
}

$stmt = $db->prepare('SELECT id FROM users WHERE id = ?');
$stmt->execute([$receiverId]);

if (!$stmt->fetch()) {
    header('Location: ' . appUrl('pages/messages.php?error=invalid'));
    exit();
}

$offerIdValue = null;

if ($offerId > 0) {
    $stmt = $db->prepare('SELECT id FROM offers WHERE id = ? AND user_id = ?');
    $stmt->execute([$offerId, $receiverId]);

    if ($stmt->fetch()) {
        $offerIdValue = $offerId;
    }
}

try {
    $stmt = $db->prepare('INSERT INTO messages (sender_id, receiver_id, offer_id, body) VALUES (?, ?, ?, ?)');
    $stmt->bindValue(1, $senderId, PDO::PARAM_INT);
    $stmt->bindValue(2, $receiverId, PDO::PARAM_INT);

    if ($offerIdValue === null) {
        $stmt->bindValue(3, null, PDO::PARAM_NULL);
    } else {
        $stmt->bindValue(3, $offerIdValue, PDO::PARAM_INT);
    }

    $stmt->bindValue(4, $body, PDO::PARAM_STR);
    $stmt->execute();
} catch (PDOException $e) {
    header('Location: ' . appUrl('pages/messages.php?to=' . $receiverId . '&error=failed'));
    exit();
}

$redirect = appUrl('pages/messages.php?to=' . $receiverId . '&sent=1');

if ($offerIdValue !== null) {
    $redirect = appUrl('pages/messages.php?to=' . $receiverId . '&offer=' . $offerIdValue . '&sent=1');
}

header('Location: ' . $redirect);
exit();
