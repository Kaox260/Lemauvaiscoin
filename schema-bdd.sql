-- Création de la base de données
CREATE DATABASE IF NOT EXISTS leboncoin;
USE leboncoin;

-- Table utilisateurs
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table annonces
CREATE TABLE annonces (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titre VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    prix DECIMAL(10,2) NOT NULL,
    ville VARCHAR(80) NOT NULL,
    categorie VARCHAR(50) NOT NULL,
    date_publication DATETIME DEFAULT CURRENT_TIMESTAMP,
    image VARCHAR(255)
) ENGINE=InnoDB;

-- Table de liaison des favoris
CREATE TABLE user_favoris (
    user_id INT NOT NULL,
    annonce_id INT NOT NULL,
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, annonce_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (annonce_id) REFERENCES annonces(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Données de test
INSERT INTO users (username, password, email) VALUES
('john', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'john@example.com'),
('emma', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'emma@example.com');

INSERT INTO annonces (titre, description, prix, ville, categorie, image) VALUES
('Vélo de course', 'Vélo de course en bon état, taille 54cm', 250.00, 'Paris', 'Sport', 'velo.jpg'),
('Canapé d''angle', 'Canapé cuir beige 3 places', 450.00, 'Lyon', 'Maison', 'canape.jpg');

INSERT INTO user_favoris (user_id, annonce_id) VALUES
(1, 1),
(1, 2),
(2, 1);