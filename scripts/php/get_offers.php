<?php

require __DIR__ . '/config/bootstrap.php';

header('Content-Type: application/json');

$stmt = $db->query('
    SELECT o.*, u.username
    FROM offers o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
');

$offers = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($offers as &$offer) {
    $offer['id'] = (int) $offer['id'];
    $offer['user_id'] = (int) $offer['user_id'];
    $offer['price'] = (float) $offer['price'];
}
unset($offer);

echo json_encode($offers);
