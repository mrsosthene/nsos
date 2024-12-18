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


$stmt_count_inscrit = $pdo->query("SELECT COUNT(id) as total FROM users ");
$total_inscrit = $stmt_count_inscrit->fetch(PDO::FETCH_ASSOC)['total'];


$stmt_count_inscrit = $pdo->query("SELECT COUNT(id) as total FROM users WHERE sexe = 'femme'");
$total_femmes = $stmt_count_inscrit->fetch(PDO::FETCH_ASSOC)['total'];

$stmt_count_inscrit = $pdo->query("SELECT COUNT(id) as total FROM users WHERE sexe = 'homme'");
$total_hommes = $stmt_count_inscrit->fetch(PDO::FETCH_ASSOC)['total'];

// Compter les utilisateurs connectés (basé sur la dernière activité)
try {
    $stmt_count_connected = $pdo->query("SELECT COUNT(id) as total FROM users WHERE last_login > DATE_SUB(NOW(), INTERVAL 30 MINUTE)");
    $total_connected = $stmt_count_connected->fetch(PDO::FETCH_ASSOC)['total'];
} catch (PDOException $e) {
    $total_connected = 0; // Valeur par défaut si erreur
}

// Compter les commentaires
try {
    $stmt_count_comments = $pdo->query("SELECT COUNT(id) as total FROM commentaires");
    $total_comments = $stmt_count_comments->fetch(PDO::FETCH_ASSOC)['total'];
} catch (PDOException $e) {
    $total_comments = 0; // Valeur par défaut si la table n'existe pas
}


$stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");

// Gestion de la suppression d'utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'supprimer') {
    if (isset($_POST['id']) && is_numeric($_POST['id'])) {
        $id = (int)$_POST['id'];

        try {
            // Requête sécurisée de suppression
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            // Rediriger pour éviter la re-soumission du formulaire
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } catch (PDOException $e) {
            echo "<script>alert('Erreur lors de la suppression : " . $e->getMessage() . "');</script>";
        }
    } else {
        echo "<script>alert('ID utilisateur invalide.');</script>";
    }
}

// Vérifier si le bouton "Nommer Admin" a été cliqué
if (isset($_POST['make_admin'])) {
    $user_id = $_POST['user_id'];

    // Mettre à jour le rôle de l'utilisateur dans la base de données
    $stmt_admin = $pdo->prepare("UPDATE users SET role = 'admin' WHERE id = :id");
    $stmt_admin->bindParam(':id', $user_id, PDO::PARAM_INT);

    if ($stmt_admin->execute()) {
        echo "<script>alert('Utilisateur nommé administrateur avec succès');</script>";
        echo "<script>window.location.href = window.location.href;</script>"; // Rafraîchir la page
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
    <title>Admin</title>
    <!-- ======= Styles ====== -->
    <link rel="stylesheet" href="css/style1.css">
</head>

<body>
    <!-- =============== Navigation ================ -->
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
                    <a href="add_article.php">
                        <span class="icon">
                            <ion-icon name="log-out-outline"></ion-icon>
                        </span>
                        <span class="title" href="index1.php">Ajouter un nouvel article</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <span class="icon">
                            <ion-icon name="settings-outline"></ion-icon>
                        </span>
                        <span class="title">Parametre</span>
                    </a>
                </li>

                <li>
                    <a href="blog.html">
                        <span class="icon">
                            <ion-icon name="log-out-outline"></ion-icon>
                        </span>
                        <span class="title" href="index1.php">Deconnexion</span>
                    </a>
                </li>
            </ul>
        </div>
        <!-- ========================= Main ==================== -->
        <div class="main">
            <div class="topbar">
                <div class="toggle">
                    <ion-icon name="menu-outline"></ion-icon>
                </div>

                <div class="search">
                    <label>
                        <input type="text" placeholder="Recherchez ici">
                        <ion-icon name="search-outline"></ion-icon>
                    </label>
                </div>

                <div class="user">
                    <img src="images\Profil Admin.png" alt="">
                </div>
            </div>

            <!-- ======================= Cards ================== -->
            <div class="cardBox">
                <a href="personnes_inscrit.php" style="text-decoration: none; color: inherit;">
                    <div class="card">
                        <div>
                            <div class="numbers">
                                <?php echo "$total_inscrit"; ?>
                            </div>
                            <div class="cardName">Inscrits</div>
                        </div>
                        <div class="iconBx">
                            <ion-icon name="people-outline"></ion-icon>
                        </div>
                    </div>
                </a>

                <div class="card">
                    <div>
                        <div class="numbers">
                            <?php echo "$total_connected"; ?>
                        </div>
                        <div class="cardName">Connectés</div>
                    </div>
                    <div class="iconBx">
                        <ion-icon name="people-outline"></ion-icon>
                    </div>
                </div>

                <div class="card">
                    <div>
                        <div class="numbers">
                            <?php echo "$total_comments"; ?>
                        </div>
                        <div class="cardName">Commentaires</div>
                    </div>
                    <div class="iconBx">
                        <ion-icon name="chatbubbles-outline"></ion-icon>
                    </div>
                </div>
            </div>
            <!-- ================ Add Charts JS ================= -->
            <div class="chartsBx">
            <script>
            const totalHommes = <?php echo $total_hommes; ?>; // PHP injecte la valeur
            const totalFemmes = <?php echo $total_femmes; ?>; // PHP injecte la valeur
            </script>
                <div class="chart"> <canvas id="chart-1"></canvas> </div>
                <div class="chart"> <canvas id="chart-2"></canvas> </div>
            </div>
        </div>
    </div>

    <!-- =========== Scripts =========  -->
    <script src="js/mainadmin.js"></script>

    <!-- ======= Charts JS ====== -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    <script src="js/chartsJS.js"></script>

    <!-- ====== ionicons ======= -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>