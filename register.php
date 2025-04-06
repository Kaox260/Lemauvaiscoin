<?php
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $username = mysqli_real_escape_string($conn, $_POST['username']);

    // Vérification email unique
    $check = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    if (mysqli_num_rows($check) > 0) {
        header('Location: register.php?error=Email déjà utilisé');
        exit;
    }

    $sql = "INSERT INTO users (email, password, username) 
            VALUES ('$email', '$password', '$username')";
    
    if (mysqli_query($conn, $sql)) {
        header('Location: index.php?success=Compte créé');
    } else {
        header('Location: register.php?error=Erreur de création');
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Même style que index.php -->
    <title>Inscription</title>
</head>
<body>
    <div class="auth-container">
        <h2>Inscription</h2>
        <?php if (isset($_GET['error'])): ?>
        <div style="color: red; margin-bottom: 1rem;">
            <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="form-group">
                <input type="text" name="username" placeholder="Nom d'utilisateur" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Mot de passe" required>
            </div>
            <button type="submit">S'inscrire</button>
        </form>
        
        <p style="margin-top: 1rem;">
            Déjà inscrit ? <a href="index.php">Se connecter</a>
        </p>
    </div>
</body>
</html>