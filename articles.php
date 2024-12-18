<?php
// Informations de connexion à la base de données

define('DB_HOST', 'localhost');

define('DB_NAME', 'projet_php');

define('DB_USER', 'root');

define('DB_PASS', '');



try {

    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {

    die("Erreur de connexion à la base de données: " . $e->getMessage());

}



// Variable pour stocker l'ID de l'article publié

$article_publie_id = null;

// Fonction pour nettoyer le HTML et limiter le texte
function cleanAndLimitText($html, $limit = 150) {
    // Supprime les balises HTML
    $text = strip_tags($html);
    // Limite la longueur du texte
    if (strlen($text) > $limit) {
        $text = substr($text, 0, $limit) . '...';
    }
    return $text;
}

// Vérification de la suppression d'un article

if (isset($_GET['supprimer']) && is_numeric($_GET['supprimer'])) {
    $id_article = $_GET['supprimer'];
    
    try {
        // Démarrer une transaction
        $pdo->beginTransaction();
        
        // D'abord supprimer les vues associées
        $stmt = $pdo->prepare("DELETE FROM article_views WHERE article_id = ?");
        $stmt->execute([$id_article]);
        
        // Ensuite supprimer l'article
        $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
        $stmt->execute([$id_article]);
        
        // Valider la transaction
        $pdo->commit();
        
        // Rediriger vers la même page
        header('Location: articles.php');
        exit();
    } catch(PDOException $e) {
        // En cas d'erreur, annuler la transaction
        $pdo->rollBack();
        die("Erreur lors de la suppression de l'article : " . $e->getMessage());
    }
}



// Vérification de la publication d'un article

if (isset($_GET['publier']) && is_numeric($_GET['publier'])) {

    $id_article = $_GET['publier'];



    // Mettre à jour le statut de l'article en 'publie'

    $stmt = $pdo->prepare("UPDATE articles SET statut = 'publie' WHERE id = ?");

    $stmt->execute([$id_article]);



    // Enregistrer l'ID de l'article publié pour afficher le dialogue

    $article_publie_id = $id_article;

}



// Récupération des articles

$stmt = $pdo->prepare("SELECT * FROM articles ORDER BY date_publication DESC");

$stmt->execute();

$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);



// Inverser l'ordre des articles pour afficher les plus récents à la fin

$articles = array_reverse($articles);

?>



<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Liste des articles</title>

    <link rel="stylesheet" href="articles.css">

</head>

<body>

    <h1>Liste des articles</h1>



    <div style="text-align: center; margin-bottom: 20px;">

        <a href="add_article.php"><button>Ajouter un nouvel article</button></a>

    </div>



    <div class="articles-container">

        <?php foreach ($articles as $article): ?>
            <div class="article-card">

                <?php if (!empty($article['image'])): ?>
                    <img src="<?php echo htmlspecialchars($article['image']); ?>" alt="<?php echo html_entity_decode(htmlspecialchars_decode($article['titre'])); ?>">
                <?php else: ?>
                    <img src="default-image.jpg" alt="Image par défaut">
                <?php endif; ?>



                <div class="article-content">

                    <h2><?php echo html_entity_decode(htmlspecialchars_decode($article['titre'])); ?></h2>

                    <p>
                        <?php 
                        // Utilise la fonction pour afficher un aperçu propre
                        echo cleanAndLimitText($article['contenu']);
                        ?>
                    </p>

                    <p><strong>Publié le :</strong> <?php echo htmlspecialchars($article['date_publication']); ?></p>

                </div>





                <div class="article-buttons">

                    <?php if ($article['statut'] === 'brouillon'): ?>
                        <a href="articles.php?publier=<?php echo $article['id']; ?>">
                            <button>Publier</button>
                        </a>
                    <?php else: ?>
                        <span style="color: green;">Publié</span>
                    <?php endif; ?>





                    <a href="articles.php?supprimer=<?php echo $article['id']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?');">
                        <button class="delete">Supprimer</button>
                    </a>

                    <a href="modifier_article.php?id=<?php echo $article['id']; ?>">
                        <button>Modifier</button>
                    </a>

                </div>

            </div>

        <?php endforeach; ?>
    </div>





    <!-- Boîte de dialogue pour voir la publication
    <?php if ($article_publie_id): ?>
        <div class="overlay" id="overlay"></div>

        <div class="dialog-box" id="dialogBox">

            <h3>L'article a été publié avec succès !</h3>

            <a href="actualites.php?id=<?php echo $article_publie_id; ?>">
                <button>Voir la publication</button>
            </a>

        </div>





        <script>

            // Afficher la boîte de dialogue

            document.getElementById('overlay').style.display = 'block';

            document.getElementById('dialogBox').style.display = 'block';

        </script>

    <?php endif; ?>





    <!-- Bouton pour accéder aux actualités
    <div style="text-align: center; margin-bottom: 20px;">

        <a href="actualites.php"><button>Actualités</button></a>

    </div>





    <script>

        document.addEventListener("DOMContentLoaded", function() {

            const container = document.querySelector('.articles-container');

            const cards = document.querySelectorAll('.article-card');

            const containerRect = container.getBoundingClientRect();

            const containerCenterX = containerRect.left + containerRect.width / 2;





            // Fonction qui ajuste la taille des cartes en fonction de leur position

            function adjustCardSize() {

                cards.forEach(card => {

                    const cardRect = card.getBoundingClientRect();

                    const cardCenterX = cardRect.left + cardRect.width / 2;

                    const distance = Math.abs(containerCenterX - cardCenterX);

                    const scale = Math.max(0.7, 1 - (distance / containerRect.width));

                    card.style.transform = `scale(${scale})`;

                    card.style.opacity = scale; 

                });

            };





            // Applique l'ajustement lors du défilement

            container.addEventListener('scroll', adjustCardSize);


            // Appliquer l'ajustement au chargement initial

            adjustCardSize();

        });

    </script>
        <!-- Boîte de dialogue pour voir la publication -->
        <?php if ($article_publie_id): ?>
            <div class="overlay" id="overlay"></div>
            <div class="dialog-box" id="dialogBox">
                <h3>L'article a été publié avec succès !</h3>
                <a href="actualites.php?id=<?php echo $article_publie_id; ?>">
                    <button>Voir la publication</button>
                </a>
            </div>
        
            <script>
                document.getElementById('overlay').style.display = 'block';
                document.getElementById('dialogBox').style.display = 'block';
            </script>
        <?php endif; ?>
        
        <!-- Bouton pour accéder aux actualités -->
        <div style="text-align: center; margin-bottom: 20px;">
            <a href="actualites.php" class="text-gray-600 hover:text-gray-900">Actualités</a>
            <a href="#" class="text-gray-600 hover:text-gray-900">Catégories</a>
            <a href="#" class="text-gray-600 hover:text-gray-900">Contact</a>
            <?php if ($is_logged_in): ?>
                <a href="profil.php" class="text-gray-600 hover:text-gray-900">Mon profil</a>
                <a href="logout.php" class="text-gray-600 hover:text-gray-900">Déconnexion</a>
            <?php else: ?>
                <a href="contact.php" class="text-gray-600 hover:text-gray-900">Contact</a>
                <a href="inscription.php" class="text-gray-600 hover:text-gray-900">S'inscrire</a>
            <?php endif; ?>
        </div>
</body>

</html>
