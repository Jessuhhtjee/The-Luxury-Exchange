<?php

if (!isset($base)) {
    $base = str_contains($_SERVER['SCRIPT_NAME'], '/pages/') ? '../' : './';
}
?>
<div class="footer">
    <div class="footer-container">
        <div class="footer-logo">
            <h2>The Luxury Exchange</h2>
            <p>
                The marketplace for exclusive luxury products,
                exotic vehicles, yachts, villas and more.
            </p>
        </div>

        <div class="footer-links">
            <h3>Navigation</h3>
            <a href="<?= $base ?>index.php">Home</a>
            <a href="<?= $base ?>pages/offers.php">Offers</a>
            <a href="<?= $base ?>pages/messages.php">Messages</a>
            <a href="<?= $base ?>pages/profile.php">Profile</a>
        </div>

        <div class="footer-contact">
            <h3>Contact</h3>
            <p>Email: hans@theluxuryexchange.com</p>
            <p>Phone: +31 6 15416907</p>
            <p>Rotterdam, Netherlands</p>
        </div>

        <div class="footer-socials">
            <h3>Follow Us</h3>
            <a href="#">Instagram</a>
            <a href="#">Youtube</a>
            <a href="#">TikTok</a>
            <a href="#">LinkedIn</a>
        </div>
    </div>
</div>
