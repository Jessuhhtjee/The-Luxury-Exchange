<?php

require __DIR__ . '/config/bootstrap.php';

header('Content-Type: application/json');

$stmt = $db->query('
    SELECT o.*, u.username
    FROM offers o
    JOIN users u ON o.user_id = u.id
    ORDER BY RANDOM()
    LIMIT 4
');

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
