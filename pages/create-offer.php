<?php

require __DIR__ . '/../scripts/php/auth.php';
requireLogin();

$error = $_GET['error'] ?? '';
$base = '../';

$messages = [
    'invalid' => 'Please fill in all fields correctly.',
    'image' => 'Please select an image.',
    'upload' => 'Upload failed. Please try again.'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create offer | The Luxury Exchange</title>
    <link rel="stylesheet" href="../css/fonts.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/create-offer.css">
</head>
<body>
    <div class="screen">
        <?php include __DIR__ . '/../includes/header.php'; ?>

        <div class="create-offer-page">
            <div class="create-offer-box">
                <h1>Create an offer</h1>
                <p class="create-offer-subtitle">Share your exclusive product with our community.</p>

                <?php if ($error && isset($messages[$error])): ?>
                    <div class="notice error"><?= escape($messages[$error]) ?></div>
                <?php endif; ?>

                <form action="../scripts/php/create_offer.php" method="POST" enctype="multipart/form-data" class="create-offer-form">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" id="title" name="title" placeholder="e.g. Villa Amalfi Coast" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" placeholder="Describe your product..." required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" name="category" required>
                            <option value="Luxury Homes">Luxury Homes</option>
                            <option value="Private Yachts">Private Yachts</option>
                            <option value="Exotic Cars">Exotic Cars</option>
                            <option value="Designer Watches">Designer Watches</option>
                            <option value="Private Jets">Private Jets</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="price">Price (€)</label>
                        <input type="number" id="price" name="price" placeholder="250000" min="1" step="1" required>
                    </div>

                    <div class="form-group">
                        <label for="image">Image</label>
                        <input type="file" id="image" name="image" accept="image/*" required>
                    </div>

                    <button type="submit" class="submit-btn">Publish offer</button>
                </form>
            </div>
        </div>

        <?php include __DIR__ . '/../includes/footer.php'; ?>
    </div>
</body>
</html>
