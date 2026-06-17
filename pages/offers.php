<?php

require __DIR__ . '/../scripts/php/config/bootstrap.php';

$initialCategory = trim($_GET['category'] ?? 'All Categories');
$created = isset($_GET['created']);
$loggedIn = isset($_SESSION['user_id']);
$currentUserId = (int) ($_SESSION['user_id'] ?? 0);

$base = '../';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offers | The Luxury Exchange</title>
    <link rel="stylesheet" href="../css/fonts.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/offers.css">
</head>
<body>
    <div class="screen">
        <?php include __DIR__ . '/../includes/header.php'; ?>

        <div class="offers-hero">
            <div class="hero-overlay">
                <h1>Discover Luxury Offers</h1>
                <p>
                    Browse exclusive villas, yachts, supercars,
                    designer watches and more.
                </p>
            </div>
        </div>

        <?php if ($created): ?>
            <div class="notice success page-notice">Your offer has been posted successfully.</div>
        <?php endif; ?>

        <div class="offers-page">
            <aside class="filters">
                <h2>Filters</h2>

                <div class="filter-group">
                    <label for="search-input">Search</label>
                    <input type="text" id="search-input" placeholder="Search offers">
                </div>

                <div class="filter-group">
                    <label for="category-select">Category</label>
                    <select id="category-select">
                        <option>All Categories</option>
                        <option>Luxury Homes</option>
                        <option>Private Yachts</option>
                        <option>Exotic Cars</option>
                        <option>Designer Watches</option>
                        <option>Private Jets</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="price-select">Price Range</label>
                    <select id="price-select">
                        <option value="">All Prices</option>
                        <option value="1">€10,000 - €50,000</option>
                        <option value="2">€50,000 - €250,000</option>
                        <option value="3">€250,000 - €1,000,000</option>
                        <option value="4">€1.000.000+</option>
                    </select>
                </div>

                <button class="filter-btn" id="apply-filters">Apply Filters</button>
            </aside>

            <main class="offers-content">
                <div class="offers-topbar">
                    <div class="categories" id="category-buttons">
                        <button class="category-btn active" data-category="All Categories">All</button>
                        <button class="category-btn" data-category="Luxury Homes">Homes</button>
                        <button class="category-btn" data-category="Private Yachts">Yachts</button>
                        <button class="category-btn" data-category="Exotic Cars">Cars</button>
                        <button class="category-btn" data-category="Designer Watches">Watches</button>
                        <button class="category-btn" data-category="Private Jets">Jets</button>
                    </div>
                </div>

                <div class="offers-grid" id="offers-container"></div>
            </main>
        </div>

        <?php include __DIR__ . '/../includes/footer.php'; ?>
    </div>

    <script>
        const initialCategory = <?= json_encode($initialCategory) ?>;
        const loggedIn = <?= json_encode($loggedIn) ?>;
        const currentUserId = <?= json_encode($currentUserId) ?>;
        const categorySelect = document.getElementById('category-select');
        const priceSelect = document.getElementById('price-select');
        const searchInput = document.getElementById('search-input');
        const offersContainer = document.getElementById('offers-container');
        const categoryButtons = document.querySelectorAll('.category-btn');

        function formatPrice(price) {
            return new Intl.NumberFormat('en-US').format(price);
        }

        function renderOffers(offers) {
            if (!offers.length) {
                offersContainer.innerHTML = '<p class="empty-message">No offers found.</p>';
                return;
            }

            offersContainer.innerHTML = offers.map(offer => `
                <div class="offer-card">
                    <div class="offer-image">
                        <img src="../${offer.image}" alt="${offer.title}">
                    </div>
                    <div class="offer-info">
                        <h3>${offer.title}</h3>
                        <p class="offer-category">${offer.category}</p>
                        <p class="offer-description">${offer.description}</p>
                        <p class="offer-price">€${formatPrice(offer.price)}</p>
                        <p class="offer-seller">By ${offer.username}</p><br>
                        <a href="../scripts/php/detail.php?offer=${offer.id}" class="post-btn">View Offer</a>
                    </div>
                </div>
            `).join('');
        }

        function loadOffers() {
            const category = categorySelect.value;
            const price = priceSelect.value;
            const search = searchInput.value.trim();
            const params = new URLSearchParams();

            if (category && category !== 'All Categories') {
                params.set('category', category);
            }

            if (price) {
                params.set('price', price);
            }

            if (search) {
                params.set('search', search);
            }

            const query = params.toString();
            const url = query
                ? `../scripts/php/filter_offers.php?${query}`
                : '../scripts/php/get_offers.php';

            fetch(url)
                .then(response => response.json())
                .then(renderOffers);
        }

        document.getElementById('apply-filters').addEventListener('click', loadOffers);

        categoryButtons.forEach(button => {
            button.addEventListener('click', () => {
                categoryButtons.forEach(item => item.classList.remove('active'));
                button.classList.add('active');
                categorySelect.value = button.dataset.category;
                loadOffers();
            });
        });

        if (initialCategory && initialCategory !== 'All Categories') {
            categorySelect.value = initialCategory;
            categoryButtons.forEach(button => {
                button.classList.toggle('active', button.dataset.category === initialCategory);
            });
        }

        loadOffers();
    </script>
</body>
</html>
