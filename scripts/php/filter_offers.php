<?php

require __DIR__ . '/config/bootstrap.php';

header('Content-Type: application/json');

$category = trim($_GET['category'] ?? '');
$price = trim($_GET['price'] ?? '');
$search = trim($_GET['search'] ?? '');

$sql = '
    SELECT o.*, u.username
    FROM offers o
    JOIN users u ON o.user_id = u.id
    WHERE 1=1
';
$params = [];

if ($category !== '' && $category !== 'All Categories') {
    $sql .= ' AND o.category = ?';
    $params[] = $category;
}

if ($search !== '') {
    $sql .= ' AND (o.title LIKE ? OR o.description LIKE ?)';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}

if ($price === '1') {
    $sql .= ' AND o.price BETWEEN 10000 AND 50000';
}
if ($price === '2') {
    $sql .= ' AND o.price BETWEEN 50000 AND 250000';
}
if ($price === '3') {
    $sql .= ' AND o.price BETWEEN 250000 AND 1000000';
}
if ($price === '4') {
    $sql .= ' AND o.price > 1000000';
}

$sql .= ' ORDER BY o.created_at DESC';

$stmt = $db->prepare($sql);
$stmt->execute($params);

$offers = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($offers as &$offer) {
    $offer['id'] = (int) $offer['id'];
    $offer['user_id'] = (int) $offer['user_id'];
    $offer['price'] = (float) $offer['price'];
}
unset($offer);

echo json_encode($offers);