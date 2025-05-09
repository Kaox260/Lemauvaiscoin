<?php
// Configuration
@session_start(); // Conserver uniquement ici

if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté pour créer une annonce");
}
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


$check_user = $conn->prepare("SELECT id FROM users WHERE id = ?");
$check_user->bind_param("i", $_SESSION['user_id']);
$check_user->execute();
if (!$check_user->get_result()->num_rows) {
    die("Utilisateur invalide");
}


if ($_POST['action'] === 'create') {
    $stmt = $conn->prepare("INSERT INTO annonces (
        titre, description, prix, ville, 
        categorie, image, user_id
    ) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param("ssdsssi",
        $_POST['titre'],
        $_POST['description'],
        $_POST['prix'],
        $_POST['ville'],
        $_POST['categorie'],
        $imagePath, // Gérer l'upload séparément
        $_SESSION['user_id']
    );
}

if ($_POST['action'] === 'update') {
    $stmt = $conn->prepare("UPDATE annonces SET
        titre = ?, description = ?, prix = ?,
        ville = ?, categorie = ?, image = COALESCE(?, image)
        WHERE id = ? AND user_id = ?");
    
    $stmt->bind_param("ssdsssii",
        $_POST['titre'],
        $_POST['description'],
        $_POST['prix'],
        $_POST['ville'],
        $_POST['categorie'],
        $newImage,
        $_POST['id'],
        $_SESSION['user_id']
    );
}

if($stmt->execute()) {
    header("Location: home.php?update=success");
} else {
    header("Location: home.php?update=error");
}
exit();

if(mysqli_query($conn, $sql)) {
header("Location: index.php");
} else {
header("Location: post_form.php?error=" . urlencode('Erreur base de données'));
}
mysqli_close($conn);