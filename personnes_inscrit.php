<?php
session_start();

// Vérifier si l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== "admin") {
    header("Location: login.php");
    exit();
}

// Informations de connexion à la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'projet_php');
define('DB_USER', 'root');
define('DB_PASS', '');

// Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}

$stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");

// Gestion de la suppression d'utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'supprimer') {
    if (isset($_POST['id']) && is_numeric($_POST['id'])) {
        $id = (int)$_POST['id'];

        try {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } catch (PDOException $e) {
            echo "<script>alert('Erreur lors de la suppression : " . $e->getMessage() . "');</script>";
        }
    }
}

// Vérifier si le bouton "Nommer Admin" a été cliqué
if (isset($_POST['make_admin'])) {
    $user_id = $_POST['user_id'];
    $stmt_admin = $pdo->prepare("UPDATE users SET role = 'admin' WHERE id = :id");
    $stmt_admin->bindParam(':id', $user_id, PDO::PARAM_INT);

    if ($stmt_admin->execute()) {
        echo "<script>alert('Utilisateur nommé administrateur avec succès');</script>";
        echo "<script>window.location.href = window.location.href;</script>";
        exit;
    } else {
        echo "<script>alert('Erreur lors de la nomination');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personnes Inscrites</title>
    <link rel="stylesheet" href="css/style1.css">
</head>
<body>
    <div class="container">
        <div class="navigation">
            <ul>
                <li>
                    <a href="index1.php">
                        <span class="icon">
                            <ion-icon name=""></ion-icon>
                        </span>
                        <span class="title">Nsos</span>
                    </a>
                </li>
                <li>
                    <a href="admin.php">
                        <span class="icon">
                            <ion-icon name="home-outline"></ion-icon>
                        </span>
                        <span class="title">Tableau de bord</span>
                    </a>
                </li>
                <li>
                    <a href="add_article.php">
                        <span class="icon">
                            <ion-icon name="add-outline"></ion-icon>
                        </span>
                        <span class="title">Ajouter un article</span>
                    </a>
                </li>
                <li>
                    <a href="logout.php">
                        <span class="icon">
                            <ion-icon name="log-out-outline"></ion-icon>
                        </span>
                        <span class="title">Déconnexion</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="main">
            <div class="topbar">
                <div class="toggle">
                    <ion-icon name="menu-outline"></ion-icon>
                </div>
                <div class="user">
                    <img src="images/Profil Admin.png" alt="">
                </div>
            </div>

            <div class="details">
                <div class="recentOrders">
                    <div class="cardHeader">
                        <h2>Liste des Personnes Inscrites</h2>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <td>Nom</td>
                                <td>Prénom</td>
                                <td>Sexe</td>
                                <td>Rôle</td>
                                <td>Action</td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['nom']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['prenom']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['sexe']) . "</td>";
                                echo "<td>";
                                if ($row['role'] !== 'admin') {
                                    echo "<form method='POST' onsubmit=\"return confirm('Êtes-vous sûr de vouloir nommer administrateur cet utilisateur ?');\" style='display:inline;'>
                                            <input type='hidden' name='user_id' value='" . $row['id'] . "'>
                                            <button type='submit' name='make_admin' style='color: green; border: none; background: none; cursor: pointer;'>
                                                Nommer Admin
                                            </button>
                                        </form>";
                                } else {
                                    echo "<span style='color: blue;'>Admin</span>";
                                }
                                echo "</td><td>
                                <form method='POST' onsubmit=\"return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');\" style='display:inline;'>
                                    <input type='hidden' name='action' value='supprimer'>
                                    <input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>
                                    <button type='submit' style='background-color:#ff4444; color:white; border:none; padding:6px 12px; cursor:pointer; border-radius:4px;'>Supprimer</button>
                                </form>
                                </td>";
                                echo "</tr>";
                            }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="js/mainadmin.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>
