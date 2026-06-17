<?php

require __DIR__ . '/../scripts/php/auth.php';
requireLogin();

$user = currentUser();

$stmt = $db->prepare('
    SELECT o.*, u.username
    FROM offers o
    JOIN users u ON o.user_id = u.id
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC
');
$stmt->execute([currentUserId()]);
$userOffers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$updated = isset($_GET['updated']);
$deleted = isset($_GET['deleted']);

$base = '../';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | The Luxury Exchange</title>
    <link rel="stylesheet" href="../css/fonts.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/offers.css">
    <link rel="stylesheet" href="../css/profile.css">
    <link rel="stylesheet" href="../css/create-offer.css">
</head>
<body>
    <div class="screen profile-screen">
        <?php include __DIR__ . '/../includes/header.php'; ?>

        <div class="profile-page">
            <div class="profile-header">
                <h1>Welcome, <?= escape($user['username']) ?></h1>
                <div class="profile-actions">
                    <a href="create-offer.php" class="post-btn">New offer</a>
                    <a href="messages.php" class="post-btn">Messages</a>
                    <a href="../scripts/php/logout.php" class="logout-btn">Log out</a>
                </div>
            </div>

            <?php if ($updated): ?>
                <div class="notice success">Your offer has been updated.</div>
            <?php endif; ?>

            <?php if ($deleted): ?>
                <div class="notice success">Your offer has been deleted.</div>
            <?php endif; ?>

            <div class="create-offer-box">
                <h2 class="section-title">My account</h2>

                <div class="form-group">
                    <label>Username</label>
                    <input type="text" value="<?= escape($user['username']) ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Email address</label>
                    <input type="email" value="<?= escape($user['email']) ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Avatar</label>
                    <input type="text" value="<?= escape($user['avatar']) ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Member since</label>
                    <input type="text" value="<?= escape(date('M j, Y', strtotime($user['created_at']))) ?>" readonly>
                </div>

                <div class="profile-actions">
                    <button type="button" class="post-btn" disabled title="Coming soon">Change email</button>
                    <button type="button" class="post-btn" disabled title="Coming soon">Change password</button>
                    <button type="button" class="logout-btn" disabled title="Coming soon">Change avatar</button>
                </div>
            </div>

            <h2 class="section-title">My offers</h2>

            <?php if (count($userOffers) === 0): ?>
                <p class="empty-message">You have not posted any offers yet.</p>
            <?php else: ?>
                <div class="offers-grid">
                    <?php foreach ($userOffers as $offer): ?>
                        <div class="offer-card">
                            <div class="offer-image">
                                <img src="../<?= escape($offer['image']) ?>" alt="<?= escape($offer['title']) ?>">
                            </div>
                            <div class="offer-info">
                                <h3><?= escape($offer['title']) ?></h3>
                                <p class="offer-category"><?= escape($offer['category']) ?></p>
                                <p class="offer-description"><?= escape($offer['description']) ?></p>
                                <p class="offer-price">€<?= formatPrice((float) $offer['price']) ?></p>
                                <div class="profile-actions">
                                    <a href="edit-offer.php?id=<?= (int) $offer['id'] ?>" class="post-btn">Edit</a>
                                    <form action="../scripts/php/delete_offer.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this offer?');">
                                        <input type="hidden" name="offer_id" value="<?= (int) $offer['id'] ?>">
                                        <button type="submit" class="logout-btn">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php include __DIR__ . '/../includes/footer.php'; ?>
    </div>
</body>
</html>
