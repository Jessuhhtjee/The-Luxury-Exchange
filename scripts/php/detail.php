<?php
require __DIR__ . '/config/bootstrap.php'; 


$offerId = isset($_GET['offer']) ? (int) $_GET['offer'] : 0;

if ($offerId <= 0) {
    die("Invalid offer ID.");
}

try {

    $stmt = $db->prepare("SELECT * FROM offers WHERE id = :id");
    $stmt->bindValue(':id', $offerId, PDO::PARAM_INT);
    $stmt->execute();

    $offer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$offer) {
        die("Offer not found.");
    }

   
    include __DIR__ . '/../../pages/detail_view.php';

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}