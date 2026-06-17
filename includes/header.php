<?php

$base = '/beroeps/The-Luxury-Exchange/The-Luxury-Exchange/';
$loggedIn = isset($_SESSION['user_id']);
$username = $_SESSION['username'] ?? '';

$loggedIn = isset($_SESSION['user_id']);
$username = $_SESSION['username'] ?? '';

?>
<div class="header">
    <header class="site-header">
        <div class="header-inner">
            <a id="home-btn" href="<?= $base ?>index.php"><h1>The Luxury Exchange</h1></a>

            <nav class="desktop-nav">
                <a href="<?= $base ?>index.php">Home</a>
                <a href="<?= $base ?>pages/offers.php">Offers</a>
                <a href="<?= $base ?>pages/messages.php">Messages</a>
            </nav>

            <div class="desktop-actions">
                <?php if ($loggedIn): ?>
                    <a href="<?= $base ?>pages/create-offer.php" class="post-btn">Make an offer yourself</a>
                    <button class="icon-btn" aria-label="Account">
                        <a href="<?= $base ?>pages/profile.php"><?= escape($username) ?></a>
                    </button>
                <?php else: ?>
                    <a href="<?= $base ?>pages/login_register.php" class="post-btn">Make an offer yourself</a>
                    <button class="icon-btn" aria-label="Account">
                        <a href="<?= $base ?>pages/login_register.php">Login</a>
                    </button>
                <?php endif; ?>
            </div>

            <div class="mobile-actions">
                <button class="icon-btn" aria-label="Account">
                    <?php if ($loggedIn): ?>
                        <a href="<?= $base ?>pages/profile.php"><?= escape($username) ?></a>
                    <?php else: ?>
                        <a href="<?= $base ?>pages/login_register.php">Login</a>
                    <?php endif; ?>
                </button>

                <button class="menu-btn" aria-label="Menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </header>
</div>
