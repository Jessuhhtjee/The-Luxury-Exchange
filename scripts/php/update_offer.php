<?php

require __DIR__ . '/config/bootstrap.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../pages/login_register.php');
    exit();
}

$offerId = (int) ($_POST['offer_id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$category = trim($_POST['category'] ?? '');
$price = (float) ($_POST['price'] ?? 0);
$userId = (int) $_SESSION['user_id'];

$allowedCategories = [
    'Luxury Homes',
    'Private Yachts',
    'Exotic Cars',
    'Designer Watches',
    'Private Jets'
];

$stmt = $db->prepare('SELECT * FROM offers WHERE id = ? AND user_id = ?');
$stmt->execute([$offerId, $userId]);
$offer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$offer) {
    header('Location: ../../pages/edit-offer.php?id=' . $offerId . '&error=notfound');
    exit();
}

if ($title === '' || $description === '' || !in_array($category, $allowedCategories, true) || $price <= 0) {
    header('Location: ../../pages/edit-offer.php?id=' . $offerId . '&error=invalid');
    exit();
}

$imagePath = $offer['image'];

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/../../media/uploads/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $imageName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', basename($_FILES['image']['name']));
    $uploadPath = $uploadDir . $imageName;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
        header('Location: ../../pages/edit-offer.php?id=' . $offerId . '&error=upload');
        exit();
    }

    if (str_starts_with($offer['image'], 'media/uploads/')) {
        $oldPath = __DIR__ . '/../../' . $offer['image'];
        if (is_file($oldPath)) {
            unlink($oldPath);
        }
    }

    $imagePath = 'media/uploads/' . $imageName;
}

$stmt = $db->prepare('
    UPDATE offers
    SET title = ?, description = ?, category = ?, price = ?, image = ?
    WHERE id = ? AND user_id = ?
');

$stmt->execute([
    $title,
    $description,
    $category,
    $price,
    $imagePath,
    $offerId,
    $userId
]);

header('Location: ../../pages/profile.php?updated=1');
exit();
