<?php
// Configuration
$upload_dir = 'uploads/';
$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
$max_size = 2 * 1024 * 1024; // 2MB

// Connexion BDD
$host = 'localhost';
$dbname = 'leboncoin';
$user = 'root';
$pass = 'root';

$conn = mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) die("Connection failed: " . mysqli_connect_error());

// Sécurisation des données
$titre = mysqli_real_escape_string($conn, $_POST['titre'] ?? '');
$description = mysqli_real_escape_string($conn, $_POST['description'] ?? '');
$prix = floatval($_POST['prix'] ?? 0);
$ville = mysqli_real_escape_string($conn, $_POST['ville'] ?? '');
$categorie = mysqli_real_escape_string($conn, $_POST['categorie'] ?? '');

// Validation des champs requis
$errors = [];
if(empty($titre)) $errors[] = 'Le titre est requis';
if(empty($description)) $errors[] = 'La description est requise';
if($prix <= 0) $errors[] = 'Le prix doit être supérieur à 0';
if(empty($ville)) $errors[] = 'La ville est requise';
if(empty($categorie)) $errors[] = 'La catégorie est requise';

// Gestion de l'upload d'image
$image_path = null;
if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['image'];
    
    // Vérification du type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if(!in_array($mime, $allowed_types)) {
        $errors[] = 'Type de fichier non autorisé';
    }
    
    // Vérification de la taille
    if($file['size'] > $max_size) {
        $errors[] = 'Fichier trop volumineux (max 2MB)';
    }
    
    if(empty($errors)) {
        // Génération d'un nom sécurisé
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $target_path = $upload_dir . $filename;
        
        if(move_uploaded_file($file['tmp_name'], $target_path)) {
            $image_path = mysqli_real_escape_string($conn, $target_path);
        } else {
            $errors[] = 'Erreur lors de l\'upload du fichier';
        }
    }
}

// Gestion des erreurs
if(!empty($errors)) {
    $error_query = http_build_query([
        'error' => implode(', ', $errors),
        'titre' => $_POST['titre'],
        'description' => $_POST['description'],
        'prix' => $_POST['prix'],
        'ville' => $_POST['ville']
    ]);
    header("Location: post_form.php?$error_query");
    exit;
}

// Insertion en BDD
$sql = "INSERT INTO annonces (titre, description, prix, ville, categorie, image)
        VALUES ('$titre', '$description', $prix, '$ville', '$categorie', " 
        . ($image_path ? "'$image_path'" : "NULL") . ")";

if(mysqli_query($conn, $sql)) {
    header("Location: index.php");
} else {
    header("Location: post_form.php?error=" . urlencode('Erreur base de données'));
}

mysqli_close($conn);