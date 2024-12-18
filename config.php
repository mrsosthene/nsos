<?php
$servername = "localhost"; // change cela selon ton serveur
$username = "root";        // change cela selon ton nom d'utilisateur
$password = "";            // change cela selon ton mot de passe
$dbname = "projet_php";    // change cela selon ton nom de base de données

// Création de la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Définir l'encodage UTF-8
$conn->set_charset("utf8mb4");

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Configuration PDO pour les autres fichiers
try {
    $pdo = new PDO(
        "mysql:host=$servername;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
            PDO::ATTR_EMULATE_PREPARES => false
        )
    );
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
