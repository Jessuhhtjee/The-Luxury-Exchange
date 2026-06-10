<?php

require __DIR__ . '/config/bootstrap.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../pages/login_register.php');
    exit();
}

$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$category = trim($_POST['category'] ?? '');
$price = (float) ($_POST['price'] ?? 0);

$allowedCategories = [
    'Luxury Homes',
    'Private Yachts',
    'Exotic Cars',
    'Designer Watches',
    'Private Jets'
];

if ($title === '' || $description === '' || !in_array($category, $allowedCategories, true) || $price <= 0) {
    header('Location: ../../pages/create-offer.php?error=invalid');
    exit();
}

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    header('Location: ../../pages/create-offer.php?error=image');
    exit();
}

$uploadDir = __DIR__ . '/../../media/uploads/';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$imageName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', basename($_FILES['image']['name']));
$uploadPath = $uploadDir . $imageName;

if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
    header('Location: ../../pages/create-offer.php?error=upload');
    exit();
}

$stmt = $db->prepare('INSERT INTO offers (user_id, title, description, category, price, image) VALUES (?, ?, ?, ?, ?, ?)');

$stmt->execute([
    (int) $_SESSION['user_id'],
    $title,
    $description,
    $category,
    $price,
    'media/uploads/' . $imageName
]);

header('Location: ../../pages/offers.php?created=1');
exit();
