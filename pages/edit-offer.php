<?php

require __DIR__ . '/../scripts/php/auth.php';
requireLogin();

$offerId = (int) ($_GET['id'] ?? 0);
$error = $_GET['error'] ?? '';
$base = '../';

$messages = [
    'invalid' => 'Please fill in all fields correctly.',
    'notfound' => 'This offer does not exist or does not belong to you.',
    'image' => 'Please select a valid image.',
    'upload' => 'Upload failed. Please try again.'
];

$stmt = $db->prepare('SELECT * FROM offers WHERE id = ? AND user_id = ?');
$stmt->execute([$offerId, currentUserId()]);
$offer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$offer) {
    header('Location: profile.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit offer | The Luxury Exchange</title>
    <link rel="stylesheet" href="../css/fonts.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/offers.css">
    <link rel="stylesheet" href="../css/create-offer.css">
</head>
<body>
    <div class="screen">
        <?php include __DIR__ . '/../includes/header.php'; ?>

        <div class="create-offer-page">
            <div class="create-offer-box">
                <h1>Edit offer</h1>
                <p class="create-offer-subtitle">Update the details of your offer.</p>

                <?php if ($error && isset($messages[$error])): ?>
                    <div class="notice error"><?= escape($messages[$error]) ?></div>
                <?php endif; ?>

                <form action="../scripts/php/update_offer.php" method="POST" enctype="multipart/form-data" class="create-offer-form">
                    <input type="hidden" name="offer_id" value="<?= (int) $offer['id'] ?>">

                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" id="title" name="title" value="<?= escape($offer['title']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" required><?= escape($offer['description']) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" name="category" required>
                            <?php
                            $categories = ['Luxury Homes', 'Private Yachts', 'Exotic Cars', 'Designer Watches', 'Private Jets'];
                            foreach ($categories as $category):
                            ?>
                                <option value="<?= escape($category) ?>" <?= $offer['category'] === $category ? 'selected' : '' ?>>
                                    <?= escape($category) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="price">Price (€)</label>
                        <input type="number" id="price" name="price" value="<?= (int) $offer['price'] ?>" min="1" step="1" required>
                    </div>

                    <div class="form-group">
                        <label for="image">Image (optional)</label>
                        <input type="file" id="image" name="image" accept="image/*">
                        <p class="create-offer-subtitle">Leave empty to keep the current image.</p>
                    </div>

                    <button type="submit" class="submit-btn">Save</button>
                    <a href="profile.php" class="logout-btn">Cancel</a>
                </form>
            </div>
        </div>

        <?php include __DIR__ . '/../includes/footer.php'; ?>
    </div>
</body>
</html>
