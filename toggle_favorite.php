<?php
session_start();
require_once __DIR__ . '/includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Vérifier si l'utilisateur existe dans la base
$checkUser = $conn->prepare("SELECT id FROM users WHERE id = ?");
$checkUser->bind_param("i", $user_id);
$checkUser->execute();
$checkUserResult = $checkUser->get_result();

if ($checkUserResult->num_rows === 0) {
    session_destroy(); // Détruit la session si l'utilisateur n'existe plus
    header('Location: index.php');
    exit;
}

if (isset($_POST['annonce_id'])) {
    $annonce_id = (int)$_POST['annonce_id'];
    
    // Vérifier si l'annonce existe
    $checkAnnonce = $conn->prepare("SELECT id FROM annonces WHERE id = ?");
    $checkAnnonce->bind_param("i", $annonce_id);
    $checkAnnonce->execute();
    
    if ($checkAnnonce->get_result()->num_rows > 0) {
        // Toggle favori
        $existing = $conn->prepare("SELECT * FROM user_favoris WHERE user_id = ? AND annonce_id = ?");
        $existing->bind_param("ii", $user_id, $annonce_id);
        $existing->execute();
        
        if ($existing->get_result()->num_rows > 0) {
            // Supprimer
            $delete = $conn->prepare("DELETE FROM user_favoris WHERE user_id = ? AND annonce_id = ?");
            $delete->bind_param("ii", $user_id, $annonce_id);
            $delete->execute();
        } else {
            // Ajouter
            $insert = $conn->prepare("INSERT INTO user_favoris (user_id, annonce_id) VALUES (?, ?)");
            $insert->bind_param("ii", $user_id, $annonce_id);
            $insert->execute();
        }
    }
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit;