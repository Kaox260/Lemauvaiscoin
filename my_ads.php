<?php
require_once __DIR__ . '/includes/db.php';

// Vérification connexion et session
if (!$conn) die("Erreur de connexion");
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Récupération des annonces de l'utilisateur
$sql = "SELECT * FROM annonces WHERE user_id = ? ORDER BY date_publication DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes annonces - LeBonCoin Clone</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: <?= isset($_GET['action']) ? 'block' : 'none' ?>;
            z-index: 1000;
        }
        
        .modal-content {
            background: white;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            border-radius: 10px;
        }
        
        .new-post-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #ff6600;
            color: white;
            border: none;
            padding: 15px 25px;
            border-radius: 30px;
            cursor: pointer;
            font-size: 16px;
        }
        
        .edit-btn {
            background: #4CAF50;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
        }
        
        .delete-btn {
            background: #f44336;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
            margin-left: 10px;
        }
        
        .annonce {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .annonce img {
            max-width: 200px;
            max-height: 200px;
        }
    </style>
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
        <a href="favorites.php" style="color: white; text-decoration: none;">Favoris</a>
        <a href="my_ads.php" style="color: white; text-decoration: underline;">Mes annonces </a>
        <a href="logout.php" style="color: white; text-decoration: none;">Déconnexion</a>
    </nav>
</header>

<a href="home.php?action=create" class="new-post-btn">+ Nouvelle annonce</a>

<div class="annonces-container">
    <h2>Mes annonces publiées</h2>
    
    <?php if ($result->num_rows === 0): ?>
        <p>Vous n'avez pas encore publié d'annonce.</p>
    <?php else: ?>
        <?php while($row = $result->fetch_assoc()): ?>
        <div class="annonce">
            <?php if($row['image']): ?>
            <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="Image annonce">
            <?php endif; ?>
            <h3><?php echo htmlspecialchars($row['titre']); ?></h3>
            <p><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
            <p class="prix"><strong><?php echo number_format($row['prix'], 2); ?> €</strong></p>
            <p>Ville : <?php echo htmlspecialchars($row['ville']); ?></p>
            <p>Catégorie : <?php echo htmlspecialchars($row['categorie']); ?></p>
            <small>Publié le <?php echo date('d/m/Y H:i', strtotime($row['date_publication'])); ?></small>
            
            <div class="actions">
                <a href="?action=edit&id=<?= $row['id'] ?>" class="edit-btn">✏️ Modifier</a>
                <a href="delete_ad.php?id=<?= $row['id'] ?>" class="delete-btn" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette annonce ?')">🗑️ Supprimer</a>
            </div>
        </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>

<!-- Modal de modification -->
<?php if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])): ?>
    <?php
    // Vérification que l'annonce appartient bien à l'utilisateur
    $edit_stmt = $conn->prepare("SELECT * FROM annonces WHERE id = ? AND user_id = ?");
    $edit_stmt->bind_param("ii", $_GET['id'], $_SESSION['user_id']);
    $edit_stmt->execute();
    $annonce_to_edit = $edit_stmt->get_result()->fetch_assoc();
    $edit_stmt->close();
    
    if ($annonce_to_edit):
    ?>
    <div class="modal-overlay">
        <div class="modal-content">
            <h2>Modifier l'annonce</h2>
            <a href="my_ads.php" style="float:right; font-size:24px;">×</a>
            
            <form action="post_handler.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?= $annonce_to_edit['id'] ?>">
                
                <div class="form-group">
                    <label for="titre">Titre *</label>
                    <input type="text" id="titre" name="titre" required 
                           value="<?= htmlspecialchars($annonce_to_edit['titre']) ?>">
                </div>

                <div class="form-group">
                    <label for="description">Description *</label>
                    <textarea id="description" name="description" rows="5" required><?= htmlspecialchars($annonce_to_edit['description']) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="prix">Prix (€) *</label>
                    <input type="number" id="prix" name="prix" step="0.01" min="0" required 
                           value="<?= htmlspecialchars($annonce_to_edit['prix']) ?>">
                </div>

                <div class="form-group">
                    <label for="ville">Ville *</label>
                    <input type="text" id="ville" name="ville" required 
                           value="<?= htmlspecialchars($annonce_to_edit['ville']) ?>">
                </div>

                <div class="form-group">
                    <label for="categorie">Catégorie *</label>
                    <select id="categorie" name="categorie" required>
                        <option value="Maison" <?= $annonce_to_edit['categorie'] === 'Maison' ? 'selected' : '' ?>>Maison</option>
                        <option value="Sport" <?= $annonce_to_edit['categorie'] === 'Sport' ? 'selected' : '' ?>>Sport</option>
                        <option value="Vehicules" <?= $annonce_to_edit['categorie'] === 'Vehicules' ? 'selected' : '' ?>>Véhicules</option>
                        <option value="Multimedia" <?= $annonce_to_edit['categorie'] === 'Multimedia' ? 'selected' : '' ?>>Multimédia</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Image actuelle :</label>
                    <?php if($annonce_to_edit['image']): ?>
                    <img src="<?= htmlspecialchars($annonce_to_edit['image']) ?>" style="max-width: 200px;">
                    <?php endif; ?>
                    <input type="file" id="image" name="image" accept="image/*">
                </div>

                <button type="submit">Enregistrer les modifications</button>
                <a href="my_ads.php">Annuler</a>
            </form>
        </div>
    </div>
    <?php endif; ?>
<?php endif; ?>

<?php mysqli_close($conn); ?>
</body>
</html>