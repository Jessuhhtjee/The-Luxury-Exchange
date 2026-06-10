<?php
if (!isset($offer)) {
    die("No offer data received");
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Detail | The Luxury Exchange</title>

    <!-- Zorg dat de paden hier absoluut zijn vanaf de root -->
    <link rel="stylesheet" href="/beroeps/The-Luxury-Exchange/The-Luxury-Exchange/css/header.css">
    <link rel="stylesheet" href="/beroeps/The-Luxury-Exchange/The-Luxury-Exchange/css/footer.css">
    <link rel="stylesheet" href="/beroeps/The-Luxury-Exchange/The-Luxury-Exchange/css/index.css">
    <link rel="stylesheet" href="/beroeps/The-Luxury-Exchange/The-Luxury-Exchange/css/detail.css">
</head>
<body>

<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="detail-container">

    <h1><?= htmlspecialchars($offer['title']) ?></h1>

    <!-- Het correcte pad naar de afbeelding -->
    <img src="/beroeps/The-Luxury-Exchange/The-Luxury-Exchange/<?= htmlspecialchars($offer['image']) ?>"
         alt="<?= htmlspecialchars($offer['title']) ?>"
         style="max-width: 500px;">

    <p><strong>Category:</strong> <?= htmlspecialchars($offer['category']) ?></p>

    <p><strong>Price:</strong> €<?= number_format((float)$offer['price']) ?></p>

    <p><?= nl2br(htmlspecialchars($offer['description'])) ?></p>

</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

</body>
</html>