<?php
require_once __DIR__ . '/includes/db.php';
@session_start(); // Conserver uniquement ici

// Vérifier la connexion utilisateur
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Récupérer les favoris de l'utilisateur
$user_id = $_SESSION['user_id'];
$favoris = [];
$error = null;

try {
    // Requête avec jointure SQL
    $stmt = $conn->prepare("
        SELECT a.* 
        FROM annonces a
        INNER JOIN user_favoris uf ON a.id = uf.annonce_id
        WHERE uf.user_id = ?
        ORDER BY uf.date_ajout DESC
    ");
    
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $favoris[] = $row;
    }
    
} catch (mysqli_sql_exception $e) {
    $error = "Erreur lors du chargement des favoris";
    error_log("Erreur favorites: " . $e->getMessage());
}

// Fermer la connexion
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes favoris</title>
    <style>
        
        .annonces-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .annonce {
            background: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .annonce img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 4px;
        }
        
        .prix {
            color: #00a650;
            font-size: 1.4em;
            margin: 10px 0;
        }
        
        .error-message {
            color: #dc3545;
            padding: 15px;
            text-align: center;
        }
    </style>
        <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
    <h1>🛍️ LeBonCoin Clone</h1>
    <div style="text-align: center; margin-top: 1rem;">
        <input type="text" placeholder="Rechercher..." style="padding: 0.5rem; width: 300px; border-radius: 20px; border: 1px solid #ddd;">
        <button style="padding: 0.5rem 1.5rem; background: white; border: none; border-radius: 20px; margin-left: 0.5rem;">🔍</button>
    </div>
    <nav style="display: flex; justify-content: center; gap: 2rem; margin-top: 1rem;">
        <a href="home.php" style="color: white; text-decoration: none;">Accueil</a>
        <a href="favorites.php" style="color: white; text-decoration: underline;">Favoris</a>
        <a href="my_ads.php" style="color: white; text-decoration: none;">Mes annonces</a>
        <a href="logout.php" style="color: white; text-decoration: none;">Déconnexion</a>
    </nav>
</header>

    <?php if ($error): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="annonces-container">
        <?php if (!empty($favoris)): ?>
            <?php foreach ($favoris as $annonce): ?>
            <div class="annonce">
                <?php if (!empty($annonce['image'])): ?>
                    <img src="<?= htmlspecialchars($annonce['image']) ?>" alt="<?= htmlspecialchars($annonce['titre']) ?>">
                <?php endif; ?>
                
                <h2><?= htmlspecialchars($annonce['titre']) ?></h2>
                <p><?= nl2br(htmlspecialchars($annonce['description'])) ?></p>
                <p class="prix"><?= number_format($annonce['prix'], 2) ?> €</p>
                <p>📍 <?= htmlspecialchars($annonce['ville']) ?></p>
                <p>🏷️ <?= htmlspecialchars($annonce['categorie']) ?></p>
                <small>Publié le <?= date('d/m/Y H:i', strtotime($annonce['date_publication'])) ?></small>
                
                <form method="post" action="toggle_favorite.php">
                    <input type="hidden" name="annonce_id" value="<?= $annonce['id'] ?>">
                    <button type="submit" class="favori-btn active" style="margin-top:10px;">
                        ❤ Retirer des favoris
                    </button>
                </form>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <p>Aucune annonce dans vos favoris pour le moment</p>
                <p>👉 Parcourez les <a href="home.php">annonces</a> pour en ajouter !</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>