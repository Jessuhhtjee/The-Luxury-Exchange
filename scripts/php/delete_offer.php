<?php

require __DIR__ . '/config/bootstrap.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../pages/login_register.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../pages/profile.php');
    exit();
}

$offerId = (int) ($_POST['offer_id'] ?? 0);
$userId = (int) $_SESSION['user_id'];

$stmt = $db->prepare('SELECT * FROM offers WHERE id = ? AND user_id = ?');
$stmt->execute([$offerId, $userId]);
$offer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$offer) {
    header('Location: ../../pages/profile.php');
    exit();
}

if (str_starts_with($offer['image'], 'media/uploads/')) {
    $imagePath = __DIR__ . '/../../' . $offer['image'];
    if (is_file($imagePath)) {
        unlink($imagePath);
    }
}

$stmt = $db->prepare('DELETE FROM offers WHERE id = ? AND user_id = ?');
$stmt->execute([$offerId, $userId]);

header('Location: ../../pages/profile.php?deleted=1');
exit();
