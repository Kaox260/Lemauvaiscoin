<?php
session_start();
require_once __DIR__ . '/includes/db.php';

// Vérifier la connexion
if (!$conn) {
    die("La connexion à la base de données a échoué");
}

// Récupération des annonces de l'utilisateur courant
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM annonces WHERE user_id = ? ORDER BY date_publication DESC";
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Récupération des données de l'annonce à modifier si ID fourni
$annonce_to_edit = null;
if(isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $edit_stmt = $conn->prepare("SELECT * FROM annonces WHERE id = ? AND user_id = ?");
    $edit_stmt->bind_param("ii", $_GET['id'], $user_id);
    $edit_stmt->execute();
    $annonce_to_edit = $edit_stmt->get_result()->fetch_assoc();
    $edit_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Le reste du head reste inchangé -->
</head>
<body>
<header>
    <!-- Le header reste inchangé -->
</header>

<!-- Ajout du bouton d'édition dans chaque annonce -->
<div class="annonces-container">
    <?php while($row = $result->fetch_assoc()): ?>
    <div class="annonce">
        <!-- ... contenu existant de l'annonce ... -->
        
        <!-- Bouton d'édition -->
        <a href="home.php?action=edit&id=<?= $row['id'] ?>" class="edit-btn">✏️ Modifier</a>
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
                <textarea id="description" name="description" rows="5" required>
                    <?= htmlspecialchars($annonce_to_edit['description']) ?>
                </textarea>
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