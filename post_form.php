<?php
// Connexion à la base de données
$host = 'localhost';
$dbname = 'leboncoin';
$user = 'root';
$pass = 'root';

$conn = mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) die("Connection failed: " . mysqli_connect_error());
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Déposer une annonce</title>
    <style>
        .form-container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        input, textarea, select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        button[type="submit"] {
            background: #ff6600;
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
            transition: opacity 0.2s;
        }
        button[type="submit"]:hover {
            opacity: 0.9;
        }
        .error {
            color: red;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <header>
        <h1>🛍️ LeBonCoin Clone</h1>
        <nav style="display: flex; justify-content: center; gap: 2rem; margin-top: 1rem;">
            <a href="index.php" style="color: white; text-decoration: none;">Accueil</a>
            <a href="post_form.php" style="color: white; text-decoration: none;">Déposer une annonce</a>
        </nav>
    </header>

    <div class="form-container">
        <h2>Déposer une annonce</h2>
        
        <?php if(isset($_GET['error'])): ?>
        <div class="error"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

        <form action="post_handler.php" method="post" enctype="multipart/form-data">
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

            <button type="submit">Publier l'annonce</button>
        </form>
    </div>
</body>
</html>