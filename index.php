<?php

require __DIR__ . '/scripts/php/config/bootstrap.php';

$stmt = $db->query('
    SELECT o.*, u.username
    FROM offers o
    JOIN users u ON o.user_id = u.id
    ORDER BY RANDOM()
    LIMIT 4
');
$featuredOffers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$base = './';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Luxury Exchange</title>
    <link rel="stylesheet" href="css/fonts.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
</head>
<body>
    <div class="screen">
        <?php include __DIR__ . '/includes/header.php'; ?>

        <div class="top-photo">
            <p id="top-text">
                The Marketplace <br>
                for special <br>
                products
            </p>

            <a href="pages/offers.php" class="aanbod-button">Discover the offers</a>
        </div>

        <div class="uitgelicht-aanbod">
            <h2 class="section-title">Luxury Categories</h2>

            <div class="categories">
                <a href="pages/offers.php?category=Luxury+Homes" class="category-card">
                    <img src="./media/images/house.png" alt="">
                    <div class="category-overlay">
                        <h3>Luxury Homes</h3>
                    </div>
                </a>

                <a href="pages/offers.php?category=Private+Yachts" class="category-card">
                    <img src="./media/images/yachts.png" alt="">
                    <div class="category-overlay">
                        <h3>Private Yachts</h3>
                    </div>
                </a>

                <a href="pages/offers.php?category=Exotic+Cars" class="category-card">
                    <img src="./media/images/car.png" alt="">
                    <div class="category-overlay">
                        <h3>Exotic Cars</h3>
                    </div>
                </a>

                <a href="pages/offers.php?category=Designer+Watches" class="category-card">
                    <img src="./media/images/rolex.png" alt="">
                    <div class="category-overlay">
                        <h3>Designer Watches</h3>
                    </div>
                </a>
            </div>

            <h2 class="section-title">Featured Listings</h2>

            <div class="products-grid">
                <?php if (count($featuredOffers) === 0): ?>
                    <p class="empty-message">No offers available yet. Be the first to post one!</p>
                <?php else: ?>
                    <?php foreach ($featuredOffers as $offer): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <img src="<?= escape($offer['image']) ?>" alt="<?= escape($offer['title']) ?>">
                            </div>

                            <div class="product-info">
                                <h3 class="product-title"><?= escape($offer['title']) ?></h3>
                                <p class="product-category"><?= escape($offer['category']) ?></p>
                                <p class="product-price">€<?= formatPrice((float) $offer['price']) ?></p>

                                <div class="product-buttons">
                                    <a href="pages/offers.php" class="product-btn">View</a>
                                    <a href="pages/offers.php" class="product-btn">Offer</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <?php include __DIR__ . '/includes/footer.php'; ?>
    </div>
</body>
</html>
