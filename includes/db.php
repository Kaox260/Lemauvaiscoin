<?php
session_start();

$host = 'localhost';
$user = 'root';
$pass = 'root';
$dbname = 'leboncoin';

// Établir la connexion
$conn = mysqli_connect($host, $user, $pass, $dbname);

// Vérifier la connexion
if (!$conn) {
    die("Échec de la connexion : " . mysqli_connect_error());
}

// Définir l'encodage
mysqli_set_charset($conn, 'utf8mb4');