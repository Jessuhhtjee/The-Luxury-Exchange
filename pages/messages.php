<?php

require __DIR__ . '/../scripts/php/auth.php';
requireLogin();

$userId = currentUserId();
$activeUserId = (int) ($_GET['to'] ?? 0);
$offerId = (int) ($_GET['offer'] ?? 0);
$sent = isset($_GET['sent']);
$error = $_GET['error'] ?? '';
$base = '../';

$errorMessages = [
    'empty' => 'Write a message before sending.',
    'invalid' => 'This user does not exist.',
    'self' => 'You cannot send a message to yourself.',
    'failed' => 'Could not send your message. Please try again.'
];

$relatedOffer = null;

if ($offerId > 0) {
    $stmt = $db->prepare('
        SELECT o.id, o.title, o.user_id, u.username
        FROM offers o
        JOIN users u ON o.user_id = u.id
        WHERE o.id = ?
    ');
    $stmt->execute([$offerId]);
    $relatedOffer = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($relatedOffer && $activeUserId === 0 && (int) $relatedOffer['user_id'] !== $userId) {
        $activeUserId = (int) $relatedOffer['user_id'];
    }
}

$stmt = $db->prepare('
    SELECT DISTINCT u.id, u.username
    FROM offers o
    JOIN users u ON o.user_id = u.id
    WHERE u.id != ?
    ORDER BY u.username ASC
');
$stmt->execute([$userId]);
$contactableSellers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $db->prepare('
    SELECT
        CASE WHEN m.sender_id = ? THEN m.receiver_id ELSE m.sender_id END AS other_user_id,
        MAX(m.created_at) AS last_message_at
    FROM messages m
    WHERE m.sender_id = ? OR m.receiver_id = ?
    GROUP BY other_user_id
    ORDER BY last_message_at DESC
');
$stmt->execute([$userId, $userId, $userId]);
$conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);

$conversationUsers = [];

if (count($conversations) > 0) {
    $otherIds = array_column($conversations, 'other_user_id');
    $placeholders = implode(',', array_fill(0, count($otherIds), '?'));
    $stmt = $db->prepare("SELECT id, username FROM users WHERE id IN ($placeholders)");
    $stmt->execute($otherIds);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $conversationUsers[(int) $row['id']] = $row['username'];
    }
}

$activeUser = null;
$thread = [];

if ($activeUserId > 0) {
    $stmt = $db->prepare('SELECT id, username FROM users WHERE id = ?');
    $stmt->execute([$activeUserId]);
    $activeUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($activeUser) {
        $stmt = $db->prepare('
            SELECT m.*, s.username AS sender_username
            FROM messages m
            JOIN users s ON m.sender_id = s.id
            WHERE (m.sender_id = ? AND m.receiver_id = ?)
               OR (m.sender_id = ? AND m.receiver_id = ?)
            ORDER BY m.created_at ASC
        ');
        $stmt->execute([$userId, $activeUserId, $activeUserId, $userId]);
        $thread = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages | The Luxury Exchange</title>
    <link rel="stylesheet" href="../css/fonts.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/offers.css">
    <link rel="stylesheet" href="../css/profile.css">
    <link rel="stylesheet" href="../css/create-offer.css">
</head>
<body>
    <div class="screen">
        <?php include __DIR__ . '/../includes/header.php'; ?>

        <div class="offers-page">
            <aside class="filters">
                <h2>Messages</h2>

                <?php if (count($conversations) === 0): ?>
                    <p class="empty-message">No conversations yet.</p>
                <?php else: ?>
                    <?php foreach ($conversations as $conversation): ?>
                        <?php
                        $otherId = (int) $conversation['other_user_id'];
                        $otherName = $conversationUsers[$otherId] ?? 'User';
                        ?>
                        <p class="offer-seller">
                            <a href="messages.php?to=<?= $otherId ?>"><?= escape($otherName) ?></a>
                        </p>
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php if (count($contactableSellers) > 0): ?>
                    <h2>Contact sellers</h2>
                    <?php foreach ($contactableSellers as $seller): ?>
                        <p class="offer-seller">
                            <a href="messages.php?to=<?= (int) $seller['id'] ?>"><?= escape($seller['username']) ?></a>
                        </p>
                    <?php endforeach; ?>
                <?php endif; ?>
            </aside>

            <main class="offers-content">
                <?php if ($sent): ?>
                    <div class="notice success">Your message has been sent.</div>
                <?php endif; ?>

                <?php if ($error && isset($errorMessages[$error])): ?>
                    <div class="notice error"><?= escape($errorMessages[$error]) ?></div>
                <?php endif; ?>

                <?php if ($activeUser && (int) $activeUser['id'] !== $userId): ?>
                    <h2 class="section-title">Conversation with <?= escape($activeUser['username']) ?></h2>

                    <?php if ($relatedOffer && (int) $relatedOffer['user_id'] === (int) $activeUser['id']): ?>
                        <p class="offer-description">
                            About offer: <?= escape($relatedOffer['title']) ?>
                        </p>
                    <?php endif; ?>

                    <?php if (count($thread) === 0): ?>
                        <p class="empty-message">No messages in this conversation yet.</p>
                    <?php else: ?>
                        <?php foreach ($thread as $message): ?>
                            <div class="offer-card">
                                <div class="offer-info">
                                    <p class="offer-seller">
                                        <?= escape($message['sender_username']) ?>
                                        · <?= escape(date('M j, Y g:i A', strtotime($message['created_at']))) ?>
                                    </p>
                                    <p class="offer-description"><?= escape($message['body']) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <form action="../scripts/php/send_message.php" method="POST" class="create-offer-form">
                        <input type="hidden" name="receiver_id" value="<?= (int) $activeUser['id'] ?>">
                        <?php if ($relatedOffer && (int) $relatedOffer['user_id'] === (int) $activeUser['id']): ?>
                            <input type="hidden" name="offer_id" value="<?= (int) $relatedOffer['id'] ?>">
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="body">Message</label>
                            <textarea id="body" name="body" placeholder="Write your message..." required></textarea>
                        </div>

                        <button type="submit" class="submit-btn">Send</button>
                    </form>
                <?php elseif ($activeUser): ?>
                    <p class="empty-message">You cannot send a message to yourself.</p>
                <?php else: ?>
                    <h2 class="section-title">Messages</h2>
                    <p class="empty-message">
                        Select a conversation or send a message from an offer on the offers page.
                    </p>
                <?php endif; ?>
            </main>
        </div>

        <?php include __DIR__ . '/../includes/footer.php'; ?>
    </div>
</body>
</html>
