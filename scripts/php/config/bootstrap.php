<?php

if (session_status() === PHP_SESSION_NONE) {
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');

    if (str_contains($scriptName, '/pages/')) {
        $appBasePath = substr($scriptName, 0, strpos($scriptName, '/pages/'));
    } elseif (str_contains($scriptName, '/scripts/php/')) {
        $appBasePath = substr($scriptName, 0, strpos($scriptName, '/scripts/php/'));
    } else {
        $appBasePath = rtrim(dirname($scriptName), '/');
    }

    $cookiePath = ($appBasePath === '' || $appBasePath === '.') ? '/' : $appBasePath . '/';

    session_set_cookie_params([
        'path' => $cookiePath,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

require __DIR__ . '/database.php';

function appUrl(string $path): string
{
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');

    if (str_contains($scriptName, '/pages/')) {
        $basePath = substr($scriptName, 0, strpos($scriptName, '/pages/'));
    } elseif (str_contains($scriptName, '/scripts/php/')) {
        $basePath = substr($scriptName, 0, strpos($scriptName, '/scripts/php/'));
    } else {
        $basePath = rtrim(dirname($scriptName), '/');
    }

    if ($basePath === '' || $basePath === '.') {
        return '/' . ltrim($path, '/');
    }

    return $basePath . '/' . ltrim($path, '/');
}

$schema = file_get_contents(__DIR__ . '/../database/schema.sql');
$statements = array_filter(array_map('trim', explode(';', $schema)));

foreach ($statements as $statement) {
    if ($statement !== '') {
        $db->exec($statement);
    }
}

$messagesTable = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='messages'")->fetchColumn();

if (!$messagesTable) {
    $db->exec('
        CREATE TABLE messages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            sender_id INTEGER NOT NULL,
            receiver_id INTEGER NOT NULL,
            offer_id INTEGER,
            body TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (sender_id) REFERENCES users(id),
            FOREIGN KEY (receiver_id) REFERENCES users(id),
            FOREIGN KEY (offer_id) REFERENCES offers(id)
        )
    ');
}

$userCount = (int) $db->query('SELECT COUNT(*) FROM users')->fetchColumn();

if ($userCount === 0) {
    $stmt = $db->prepare('INSERT INTO users (username, email, password_hash, avatar) VALUES (?, ?, ?, ?)');
    $stmt->execute([
        'demo',
        'demo@luxuryexchange.com',
        password_hash('demo123', PASSWORD_DEFAULT),
        'avatar1.png'
    ]);
}

$stmt = $db->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute(['seller@luxuryexchange.com']);
$sellerId = $stmt->fetchColumn();

if (!$sellerId) {
    $stmt = $db->prepare('INSERT INTO users (username, email, password_hash, avatar) VALUES (?, ?, ?, ?)');
    $stmt->execute([
        'luxury_seller',
        'seller@luxuryexchange.com',
        password_hash('demo123', PASSWORD_DEFAULT),
        'avatar2.png'
    ]);
    $sellerId = (int) $db->lastInsertId();
} else {
    $sellerId = (int) $sellerId;
}

$offerCount = (int) $db->query('SELECT COUNT(*) FROM offers')->fetchColumn();

if ($offerCount === 0) {
    $demoId = (int) $db->query("SELECT id FROM users WHERE email = 'demo@luxuryexchange.com'")->fetchColumn();
    $samples = [
        ['Villa Amalfi Coast', 'Exclusive cliffside villa with panoramic sea views.', 'Luxury Homes', 2500000, 'media/images/house.png', $sellerId],
        ['Sunseeker 88 Yacht', 'Luxury motor yacht with full crew and Mediterranean delivery.', 'Private Yachts', 4200000, 'media/images/yachts.png', $sellerId],
        ['Lamborghini Revuelto', 'Brand new exotic supercar, limited edition specification.', 'Exotic Cars', 650000, 'media/images/car.png', $demoId],
        ['Rolex Daytona Platinum', 'Rare platinum chronograph with original papers.', 'Designer Watches', 185000, 'media/images/rolex.png', $demoId],
        ['Gulfstream G650ER', 'Long-range private jet, turnkey ownership package.', 'Private Jets', 65000000, 'media/images/background.png', $sellerId],
        ['Monaco Penthouse', 'Waterfront penthouse in the heart of Monaco.', 'Luxury Homes', 8900000, 'media/images/house.png', $demoId],
    ];

    $stmt = $db->prepare('INSERT INTO offers (user_id, title, description, category, price, image) VALUES (?, ?, ?, ?, ?, ?)');

    foreach ($samples as $sample) {
        $stmt->execute([$sample[5], $sample[0], $sample[1], $sample[2], $sample[3], $sample[4]]);
    }
} else {
    $distinctOwners = (int) $db->query('SELECT COUNT(DISTINCT user_id) FROM offers')->fetchColumn();

    if ($distinctOwners < 2) {
        $offerIds = $db->query('SELECT id FROM offers ORDER BY id ASC')->fetchAll(PDO::FETCH_COLUMN);
        $half = (int) ceil(count($offerIds) / 2);
        $update = $db->prepare('UPDATE offers SET user_id = ? WHERE id = ?');

        for ($i = 0; $i < $half; $i++) {
            $update->execute([$sellerId, (int) $offerIds[$i]]);
        }
    }
}

function formatPrice(float $price): string
{
    return number_format($price, 0, '.', ',');
}

function escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
