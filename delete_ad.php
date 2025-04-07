<?php
require_once __DIR__ . '/includes/db.php';

// Vérification connexion et session
if (!$conn) die("Erreur de connexion");
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Vérification que l'annonce appartient à l'utilisateur
    $stmt = $conn->prepare("DELETE FROM annonces WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $_SESSION['user_id']);
    
    if ($stmt->execute()) {
        header('Location: my_ads.php?deleted=1');
    } else {
        header('Location: my_ads.php?error=1');
    }
    exit();
}

header('Location: my_ads.php');
?>