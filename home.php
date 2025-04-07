<?php
require_once __DIR__ . '/includes/db.php';

// Vérification connexion et session
if (!$conn) die("Erreur de connexion");
if (!isset($_SESSION['user_id'])) header('Location: login.php');

// Gestion des favoris
$favoris_ids = [];
$favoris_stmt = $conn->prepare("SELECT annonce_id FROM user_favoris WHERE user_id = ?");
$favoris_stmt->bind_param("i", $_SESSION['user_id']);
$favoris_stmt->execute();
$favoris_ids = array_column($favoris_stmt->get_result()->fetch_all(MYSQLI_ASSOC), 'annonce_id');
$favoris_stmt->close();

// Requête principale avec jointure
$sql = "SELECT 
            *
        FROM annonces a
        INNER JOIN users u ON a.user_id = u.id
        ORDER BY a.date_publication DESC";
$result = $conn->query($sql);

// Gestion édition
$annonce_to_edit = null;
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'edit') {
    $edit_stmt = $conn->prepare("SELECT * FROM annonces WHERE id = ? AND user_id = ?");
    $edit_stmt->bind_param("ii", $_GET['id'], $_SESSION['user_id']);
    $edit_stmt->execute();
    $annonce_to_edit = $edit_stmt->get_result()->fetch_assoc();
    $edit_stmt->close();
}

// Récupération des annonces
$sql = "SELECT * FROM annonces ORDER BY date_publication DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>LeBonCoin Clone</title>
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
    </style>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php
require_once __DIR__ . '/includes/db.php';

// Vérification connexion et session
if (!$conn) die("Erreur de connexion");
if (!isset($_SESSION['user_id'])) header('Location: login.php');

// Gestion des favoris
$favoris_ids = [];
$favoris_stmt = $conn->prepare("SELECT annonce_id FROM user_favoris WHERE user_id = ?");
$favoris_stmt->bind_param("i", $_SESSION['user_id']);
$favoris_stmt->execute();
$favoris_ids = array_column($favoris_stmt->get_result()->fetch_all(MYSQLI_ASSOC), 'annonce_id');
$favoris_stmt->close();

// Requête principale avec jointure
$sql = "SELECT 
            *
        FROM annonces a
        INNER JOIN users u ON a.user_id = u.id
        ORDER BY a.date_publication DESC";
$result = $conn->query($sql);

// Gestion édition
$annonce_to_edit = null;
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'edit') {
    $edit_stmt = $conn->prepare("SELECT * FROM annonces WHERE id = ? AND user_id = ?");
    $edit_stmt->bind_param("ii", $_GET['id'], $_SESSION['user_id']);
    $edit_stmt->execute();
    $annonce_to_edit = $edit_stmt->get_result()->fetch_assoc();
    $edit_stmt->close();
}

// Récupération des annonces
$sql = "SELECT * FROM annonces ORDER BY date_publication DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>LeBonCoin Clone</title>
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
        <a href="favorites.php" style="color: white; text-decoration: none;">Favoris</a>
        <a href="my_ads.php" style="color: white; text-decoration: none;">Mes annonces </a>
        <a href="logout.php" style="color: white; text-decoration: none;">Déconnexion</a>
    </nav>
</header>

<a href="home.php?action=create" class="new-post-btn">+ Nouvelle annonce</a>

<!-- Popup de création -->
<?php if (isset($_GET['action']) && $_GET['action'] === 'create'): ?>
<div class="modal-overlay">
    <div class="modal-content">
        <form action="post_handler.php" method="post" enctype="multipart/form-data">
            <h2>Nouvelle annonce</h2>
            <a href="home.php" style="float:right; font-size:24px;">×</a>
            
            <input type="hidden" name="action" value="create">
            
            <div class="form-group">
                <label for="titre">Titre *</label>
                <input type="text" id="titre" name="titre" required 
                       value="<?php echo isset($_GET['titre']) ? htmlspecialchars($_GET['titre']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="description">Description *</label>
                <textarea id="description" name="description" rows="5" required><?php echo isset($_GET['description']) ? htmlspecialchars($_GET['description']) : ''; ?></textarea>
            </div>

            <div class="form-group">
                <label for="prix">Prix (€) *</label>
                <input type="number" id="prix" name="prix" step="0.01" min="0" required 
                       value="<?php echo isset($_GET['prix']) ? htmlspecialchars($_GET['prix']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="ville">Ville *</label>
                <input type="text" id="ville" name="ville" required 
                       value="<?php echo isset($_GET['ville']) ? htmlspecialchars($_GET['ville']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="categorie">Catégorie *</label>
                <select id="categorie" name="categorie" required>
                    <option value="">Choisir...</option>
                    <option value="Maison">Maison</option>
                    <option value="Sport">Sport</option>
                    <option value="Vehicules">Véhicules</option>
                    <option value="Multimedia">Multimédia</option>
                </select>
            </div>

            <div class="form-group">
                <label for="image">Photo (max 2MB)</label>
                <input type="file" id="image" name="image" accept="image/*">
            </div>

            <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">

            <button type="submit">Publier l'annonce</button>
            <a href="home.php">Annuler</a>
        </form>
    </div>
</div>
<?php endif; ?>

<div class="annonces-container">
    <h1>Annonces récentes</h1>
    <?php while($row = mysqli_fetch_assoc($result)): ?>
    <div class="annonce">
        <?php if($row['image']): ?>
        <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="Image annonce">
        <?php endif; ?>
        <h2><?php echo htmlspecialchars($row['titre']); ?></h2>
        <p><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
        <p class="prix"><?php echo number_format($row['prix'], 2); ?> €</p>
        <p>Ville : <?php echo htmlspecialchars($row['ville']); ?></p>
        <p>Catégorie : <?php echo htmlspecialchars($row['categorie']); ?></p>
        <small>Publié le <?php echo date('d/m/Y H:i', strtotime($row['date_publication'])); ?></small>
        
        <form method="post" action="toggle_favorite.php" class="favori-form">
            <input type="hidden" name="annonce_id" value="<?= htmlspecialchars($row['id']) ?>">
            <button type="submit" class="favori-btn <?= in_array($row['id'], $favoris_ids) ? 'active' : '' ?>">
                <?= in_array($row['id'], $favoris_ids) ? '❤' : '🤍' ?>
            </button>
        </form>
        
        <?php if ($row['user_id'] === $_SESSION['user_id']): ?>
            <a href="my_ads.php?action=edit&id=<?= $row['id'] ?>" class="edit-btn">✏️ Modifier</a>
        <?php endif; ?>
    </div>
    <?php endwhile; ?>
</div>


<?php mysqli_close($conn); ?>
</body>
</html>

<a href="home.php?action=create" class="new-post-btn">+ Nouvelle annonce</a>

<!-- Popup de création -->
<?php if (isset($_GET['action']) && $_GET['action'] === 'create'): ?>
<div class="modal-overlay">
    <div class="modal-content">
        <form action="post_handler.php" method="post" enctype="multipart/form-data">
            <h2>Nouvelle annonce</h2>
            <a href="home.php" style="float:right; font-size:24px;">×</a>
            
            <input type="hidden" name="action" value="create">
            
            <div class="form-group">
                <label for="titre">Titre *</label>
                <input type="text" id="titre" name="titre" required 
                       value="<?php echo isset($_GET['titre']) ? htmlspecialchars($_GET['titre']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="description">Description *</label>
                <textarea id="description" name="description" rows="5" required><?php echo isset($_GET['description']) ? htmlspecialchars($_GET['description']) : ''; ?></textarea>
            </div>

            <div class="form-group">
                <label for="prix">Prix (€) *</label>
                <input type="number" id="prix" name="prix" step="0.01" min="0" required 
                       value="<?php echo isset($_GET['prix']) ? htmlspecialchars($_GET['prix']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="ville">Ville *</label>
                <input type="text" id="ville" name="ville" required 
                       value="<?php echo isset($_GET['ville']) ? htmlspecialchars($_GET['ville']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="categorie">Catégorie *</label>
                <select id="categorie" name="categorie" required>
                    <option value="">Choisir...</option>
                    <option value="Maison">Maison</option>
                    <option value="Sport">Sport</option>
                    <option value="Vehicules">Véhicules</option>
                    <option value="Multimedia">Multimédia</option>
                </select>
            </div>

            <div class="form-group">
                <label for="image">Photo (max 2MB)</label>
                <input type="file" id="image" name="image" accept="image/*">
            </div>

            <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">

            <button type="submit">Publier l'annonce</button>
            <a href="home.php">Annuler</a>
        </form>
    </div>
</div>
<?php endif; ?>

<div class="annonces-container">
    <?php while($row = mysqli_fetch_assoc($result)): ?>
    <div class="annonce">
        <?php if($row['image']): ?>
        <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="Image annonce">
        <?php endif; ?>
        <h2><?php echo htmlspecialchars($row['titre']); ?></h2>
        <p><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
        <p class="prix"><?php echo number_format($row['prix'], 2); ?> €</p>
        <p>Ville : <?php echo htmlspecialchars($row['ville']); ?></p>
        <p>Catégorie : <?php echo htmlspecialchars($row['categorie']); ?></p>
        <small>Publié le <?php echo date('d/m/Y H:i', strtotime($row['date_publication'])); ?></small>
        
        <form method="post" action="toggle_favorite.php" class="favori-form">
            <input type="hidden" name="annonce_id" value="<?= htmlspecialchars($row['id']) ?>">
            <button type="submit" class="favori-btn <?= in_array($row['id'], $favoris_ids) ? 'active' : '' ?>">
                <?= in_array($row['id'], $favoris_ids) ? '❤' : '🤍' ?>
            </button>
        </form>
        
        <?php if ($row['user_id'] === $_SESSION['user_id']): ?>
            <a href="?action=edit&id=<?= $row['id'] ?>" class="edit-btn">✏️ Modifier</a>
        <?php endif; ?>
    </div>
    <?php endwhile; ?>
</div>

<!-- Modal de modification -->
<?php if (isset($_GET['action']) && $_GET['action'] === 'edit' && $annonce_to_edit): ?>
<div class="modal-overlay">
    <div class="modal-content">
        <h2>Modifier l'annonce</h2>
        <a href="home.php" style="float:right; font-size:24px;">×</a>
        
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
            <a href="home.php">Annuler</a>
        </form>
    </div>
</div>
<?php endif; ?>
</body>
</html>