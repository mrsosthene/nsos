<?php
require_once 'config/connexion.php';

// Démarrer la session
session_start();

// Vérifier si l'utilisateur est connecté
$is_logged_in = isset($_SESSION['user_id']);

// Récupérer l'ID de l'article depuis l'URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Gérer la vue uniquement si l'utilisateur est connecté
if ($is_logged_in) {
    try {
        // Vérifier si l'utilisateur a déjà vu cet article
        $stmt = $pdo->prepare("SELECT id FROM article_views WHERE article_id = ? AND user_id = ?");
        $stmt->execute([$id, $_SESSION['user_id']]);
        
        if (!$stmt->fetch()) {
            // Si l'utilisateur n'a pas encore vu l'article, ajouter une vue
            $stmt = $pdo->prepare("INSERT INTO article_views (article_id, user_id) VALUES (?, ?)");
            $stmt->execute([$id, $_SESSION['user_id']]);
            
            // Incrémenter le compteur de vues dans la table articles
            $stmt = $pdo->prepare("UPDATE articles SET vues = vues + 1 WHERE id = ?");
            $stmt->execute([$id]);
        }
    } catch(PDOException $e) {
        error_log("Erreur lors de l'enregistrement de la vue : " . $e->getMessage());
    }
}

// Récupérer le nombre total de vues uniques
$stmt = $pdo->prepare("SELECT COUNT(*) as total_views FROM article_views WHERE article_id = ?");
$stmt->execute([$id]);
$views_data = $stmt->fetch();
$total_views = $views_data['total_views'];

// Préparer et exécuter la requête pour obtenir l'article
$stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ? AND statut = 'publié'");
$stmt->execute([$id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

// Si l'article n'existe pas, rediriger vers la page blog
if (!$article) {
    header('Location: blog.php');
    exit();
}

// Formater la date
$date = new DateTime($article['date_publication']);
setlocale(LC_TIME, 'fr_FR.UTF8');
$date_formatee = strftime('%d %B %Y', $date->getTimestamp());
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['titre']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="lire_article.css">
    <style>
        /* Styles pour l'image de fond */
        .background-image {
            background-image: url('<?php echo htmlspecialchars($article['image'] ?: "default-image.jpg"); ?>');
            background-size: cover;
            background-position: center;
            height: 100vh;
            position: relative;
            opacity: 1;
            transition: opacity 1s ease-out;
        }

        /* Superposition de l'overlay */
        .gradient-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.8));
        }

        /* Animation du titre */
        .title-container {
            position: absolute;
            bottom: 10%;
            left: 10%;
            right: 10%;
            text-align: left;
            color: white;
            opacity: 0;
            animation: fadeInTitle 2s forwards 0.5s;
        }

        @keyframes fadeInTitle {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Animation du contenu de l'article */
        .article-content {
            opacity: 0;
            transform: translateY(20px) scale(0.95);
            animation: fadeInContent 1s forwards 1s;
        }

        /* Classes pour les images */
        .img-fluid { max-width: 100%; height: auto; }
        .img-small { max-width: 300px; height: auto; }
        .img-medium { max-width: 500px; height: auto; }
        .img-large { max-width: 800px; height: auto; }

        /* Préserver les dimensions des images dans le contenu */
        .article-content img {
            max-width: 100%;
            height: auto;
        }

        /* Ne pas forcer la largeur des images qui ont des dimensions spécifiques */
        .article-content img[width] {
            width: auto !important;
            max-width: 100% !important;
        }

        .article-content > div {
            max-width: none !important;
        }

        .article-content p, 
        .article-content div, 
        .article-content span {
            max-width: 100% !important;
        }

        @keyframes fadeInContent {
            0% {
                opacity: 0;
                transform: translateY(20px) scale(0.95);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Effet de défilement fluide */
        .fade-in-on-scroll {
            opacity: 0;
            transform: translateY(50px);
            transition: opacity 1s ease, transform 1s ease;
        }

        .fade-in-on-scroll.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Effet de zoom sur les images */
        .zoom-on-scroll {
            opacity: 0;
            transform: scale(0.95);
            transition: opacity 1s ease, transform 1s ease;
        }

        .zoom-on-scroll.visible {
            opacity: 1;
            transform: scale(1);
        }

        .article-text {
            color: black !important;
        }
        .article-text p {
            color: black !important;
        }
        .article-text strong,
        .article-text b,
        .article-text span,
        .article-text div,
        .article-text * {
            color: black !important;
        }
        /* Force tous les éléments de texte en noir */
        .article-content * {
            color: black !important;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans">
    <!-- Barre de Navigation -->
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="blog1.php" class="text-xl font-bold">Nsos</a>
            <nav class="flex space-x-8">
                <a href="blog1.php" class="text-gray-600 hover:text-gray-900">Accueil</a>
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
            </nav>
        </div>
    </header>

    <!-- Image en arrière-plan avec titre -->
    <div class="background-image">
        <div class="gradient-overlay"></div>
        <div class="title-container">
            <h1 class="text-4xl md:text-6xl font-bold drop-shadow-lg">
                <?php echo htmlspecialchars($article['titre']); ?>
            </h1>
            <p class="text-xl mt-4 text-gray-200">
                Publié le <?php echo $date_formatee; ?>
            </p>
            <p class="text-gray-200">
                Vue<?php echo $total_views > 1 ? 's' : ''; ?> : <?php echo $total_views; ?>
            </p>
        </div>
    </div>

    <!-- Contenu de l'article -->
    <main class="py-8">
        <div class="container mx-auto px-6 md:px-12 lg:px-20">
            <h1 class="text-4xl font-bold mb-6"><?php echo html_entity_decode(htmlspecialchars_decode($article['titre'])); ?></h1>
            <div class="article-content bg-white p-8 w-full">
                <div class="w-full">
                    <?php echo $article['contenu']; ?>
                </div>
            </div>

            <!-- Section commentaires -->
            <div class="mt-8 bg-white p-6">
                <h2 class="text-2xl font-bold mb-4">Laissez un commentaire</h2>
                <form action="ajouter_commentaire.php" method="POST" class="space-y-4">
                    <input type="hidden" name="article_id" value="<?php echo $id; ?>">
                    
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nom</label>
                        <input type="text" id="name" name="name" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="email" name="email" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
                    </div>

                    <div>
                        <label for="comment" class="block text-sm font-medium text-gray-700">Votre commentaire</label>
                        <textarea id="comment" name="comment" rows="4" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500"
                        ></textarea>
                    </div>

                    <button type="submit" 
                        class="bg-gray-800 text-white px-6 py-2 rounded-md hover:bg-gray-700 transition-colors duration-300">
                        Publier le commentaire
                    </button>
                </form>

                <!-- Affichage des commentaires existants -->
                <div class="mt-8">
                    <h3 class="text-xl font-semibold mb-4">Commentaires</h3>
                    <?php
                    // Récupérer les commentaires de l'article
                    $stmt = $pdo->prepare("SELECT * FROM commentaires WHERE article_id = ? ORDER BY date_creation DESC");
                    $stmt->execute([$id]);
                    $commentaires = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (count($commentaires) > 0):
                        foreach ($commentaires as $commentaire):
                            $date_commentaire = new DateTime($commentaire['date_creation']);
                            $date_commentaire_formatee = strftime('%d %B %Y à %H:%M', $date_commentaire->getTimestamp());
                    ?>
                        <div class="bg-gray-50 p-4 rounded-lg mb-4">
                            <div class="flex justify-between items-start">
                                <div class="font-medium text-gray-900">
                                    <?php echo htmlspecialchars($commentaire['nom']); ?>
                                </div>
                                <div class="text-sm text-gray-500">
                                    <?php echo $date_commentaire_formatee; ?>
                                </div>
                            </div>
                            <div class="mt-2 text-gray-700">
                                <?php echo nl2br(htmlspecialchars($commentaire['contenu'])); ?>
                            </div>
                        </div>
                    <?php
                        endforeach;
                    else:
                    ?>
                        <p class="text-gray-500 italic">Aucun commentaire pour le moment. Soyez le premier à commenter !</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Section Premium -->
    <div class="contents-payant py-12 bg-gray-50">
        <section class="premium-section container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center mb-8">Articles Premium Recommandés</h2>
            <div class="card-container grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php
                // Récupérer 3 articles aléatoires
                $stmt = $pdo->prepare("SELECT * FROM articles WHERE id != ? AND statut = 'publié' ORDER BY RAND() LIMIT 3");
                $stmt->execute([$id]);
                $articles_recommandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($articles_recommandes as $article_recommande):
                ?>
                <div class="card bg-white rounded-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    <div class="card-image h-48 relative">
                        <img src="<?php echo htmlspecialchars($article_recommande['image']); ?>" 
                             alt="<?php echo htmlspecialchars($article_recommande['titre']); ?>"
                             class="w-full h-full object-cover">
                        <?php if ($article_recommande === reset($articles_recommandes)): ?>
                            <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                                <button class="bg-gray-800 text-white px-6 py-2 rounded-full hover:bg-gray-700 transition-colors duration-300 transform hover:scale-105">
                                    Débloquer le contenu
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-content p-6">
                        <p class="category text-gray-600 text-sm font-semibold mb-2">ARTICLE PREMIUM</p>
                        <h3 class="text-xl font-bold mb-2"><?php echo htmlspecialchars($article_recommande['titre']); ?></h3>
                        <p class="text-gray-600 text-sm">
                            <?php echo substr(strip_tags($article_recommande['contenu']), 0, 100) . '...'; ?>
                        </p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script>
        // Animation au défilement
        document.addEventListener('DOMContentLoaded', function() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, {
                threshold: 0.1
            });

            document.querySelectorAll('.fade-in-on-scroll, .zoom-on-scroll').forEach((el) => {
                observer.observe(el);
            });
        });
    </script>
</body>
</html>
