<?php
require_once 'config.php';

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

// Récupérer l'article le plus récent
$stmt = $pdo->query("SELECT * FROM articles WHERE statut = 'publié' ORDER BY date_publication DESC LIMIT 1");
$article_principal = $stmt->fetch(PDO::FETCH_ASSOC);

// Récupérer les autres articles
$stmt = $pdo->query("SELECT * FROM articles WHERE statut = 'publié' AND id != " . $article_principal['id'] . " ORDER BY date_publication DESC LIMIT 3");
$articles_secondaires = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les articles populaires
$stmt = $pdo->query("SELECT * FROM articles WHERE statut = 'publié' ORDER BY vues DESC LIMIT 3");
$articles_populaires = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les articles récents
$stmt = $pdo->query("SELECT * FROM articles WHERE statut = 'publié' ORDER BY date_publication DESC LIMIT 3");
$articles_recents = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Formater la date
function formaterDate($date) {
    setlocale(LC_TIME, 'fr_FR.UTF8');
    return strftime('%B %d, %Y', strtotime($date));
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Leadership Féminin</title>
    <script src="https://kit.fontawesome.com/c6b8a9f677.js" crossorigin="anonymous"></script>
    <link href="https://unpkg.com/aos@next/dist/aos.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="blog.css" />
    <script src="blog.js" defer></script>
  </head>
  <body class="bg-gray-100 font-sans">
    <!-- Header -->
    <header class="bg-white shadow-md py-4">
      <div class="container mx-auto px-4 flex justify-between items-center">
        <a href="#" class="text-xl font-bold">Nsos</a>
        <nav class="space-x-4">
          <a href="#" class="text-gray-600 hover:text-blue-500">Accueil</a>
          <a href="actualites.php" class="text-gray-600 hover:text-blue-500">Blog</a>
          <a href="#" class="text-gray-600 hover:text-blue-500">Contact</a>
          <a href="inscription.php" class="text-gray-600 hover:text-blue-500"
            >S'inscrire</a
          >
          <a href="login.php" class="text-gray-600 hover:text-blue-500"
          >Se connecter</a
        >
        </nav>
      </div>
    </header>

    <!-- Hero Section -->
    <!-- Main Content -->
    <main class="container mx-40 p-27 flex justify-items-center">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Main Blog Card -->
        <div
          class="shadow-lg rounded-lg overflow-hidden hover-scale transition-all duration-300"
        >
          <div class="relative">
            <img
              src="images blog/first.jpg"
              alt="Leadership féminin"
              class="w-full h-100 object-cover transition-transform duration-300"
            />
            <div
              class="absolute  top-0 bg-gray-400 text-white px-4 py-2 text-sm font-semibold"
            >
              Guides
            </div>
          </div>
          <div class="p-6">
            <h2
              class="text-lg font-bold mb-2 title-text transition-transform duration-300"
            >
              Les femmes qui changent le monde : Portraits de leaders
              inspirantes
            </h2>
            <p class="text-gray-600 text-sm">June 16, 2022</p>
          </div>
        </div>

        <!-- Side Cards -->
        <div class="space-y-4 mt-32">
          <div
            class="grid p-4 drop-shadow-lg hover-scale transition-all duration-300"
          >
            <h3
              class="font-semibold text-gray-800 text-md mb-1 line-hover-up transition-transform duration-300"
            >
              Le mentorat féminin : Une clé pour un leadership fort et durable
            </h3>
            <p class="text-gray-600 text-sm">Conseils</p>
          </div>

          <div
            class="grid p-4 drop-shadow-lg hover-scale transition-all duration-300"
          >
            <h3
              class="font-semibold text-gray-800 text-md mb-1 line-hover-down transition-transform duration-300"
            >
              L'impact des femmes leaders dans les entreprises africaines
            </h3>
            <p class="text-gray-600 text-sm">Conseils</p>
          </div>

          <div
            class="grid p-4 drop-shadow-lg hover-scale transition-all duration-300"
          >
            <h3
              class="font-semibold text-gray-800 text-md mb-1 line-hover-up transition-transform duration-300"
            >
              Les mythes autour du leadership féminin : Décryptage et vérité
            </h3>
            <p class="text-gray-600 text-sm">Ressources</p>
          </div>
        </div>
      </div>
    </main>
    <section class="first">
      <div class="container">
        <div class="articles">
          <div class="article">
            <img src="images blog/1.jpeg" alt="Article image" />
            <div class="article-content">
              <div class="article-category">Conseils</div>
              <div class="article-title">
                Le mentorat féminin : Une clé pour un leadership fort et durable
              </div>
              <div class="article-excerpt">
                Viverra tristique gravida dolor vel aenean egestas libero enim
                consequat arcu augue euismod est.
              </div>
              <div class="article-meta">
                <img src="images blog/Ndickou.jpeg" alt="Author" />
                <span>Ndickou</span>
                <span>•</span>
                <span>Juin 18, 2022</span>
              </div>
            </div>
          </div>
          <div class="article">
            <img src="images blog/2150275664.jpg" alt="Article image" />
            <div class="article-content">
              <div class="article-category">Ressources</div>
              <div class="article-title">
                10 outils de productivité qui valent la peine d'être vérifiés
              </div>
              <div class="article-excerpt">
                Viverra tristique gravida dolor vel aenean egestas libero enim
                consequat arcu augue euismod est.
              </div>
              <div class="article-meta">
                <img src="images blog/Ndickou.jpeg" alt="Author" />
                <span>Ndickou</span>
                <span>•</span>
                <span>Juin 16, 2022</span>
              </div>
            </div>
          </div>
          <div class="article">
            <img
              src="images blog/téléchargement (1).jpeg"
              alt="Article image"
            />
            <div class="article-content">
              <div class="article-category">Ressources</div>
              <div class="article-title">
                Les mythes autour du leadership féminin : Décryptage et vérité
              </div>
              <div class="article-excerpt">
                Viverra tristique gravida dolor vel aenean egestas libero enim
                consequat arcu augue euismod est.
              </div>
              <div class="article-meta">
                <img src="images blog/Ndickou.jpeg" alt="Author" />
                <span>Ndickou</span>
                <span>•</span>
                <span>Juin 16, 2022</span>
              </div>
            </div>
          </div>
          <div class="article">
            <img src="images blog/5.jpeg" alt="Article image" />
            <div class="article-content">
              <div class="article-category">Ressources</div>
              <div class="article-title">
                De la vision à l’action : Histoires de succès de femmes
                entrepreneures
              </div>
              <div class="article-excerpt">
                Viverra tristique gravida dolor vel aenean egestas libero enim
                consequat arcu augue euismod est.
              </div>
              <div class="article-meta">
                <img src="images blog/Ndickou.jpeg" alt="Author" />
                <span>Ndickou</span>
                <span>•</span>
                <span>Juin 16, 2022</span>
              </div>
            </div>
          </div>
        </div>
        <div class="sidebar">
                <h2>Nsos</h2>
                <p>
                    Tellus id nisl blandit vitae quam magna nisl aliquet aliquam arcu
                    ultricies commodo felisoler massa ipsum erat non sit amet.
                </p>
                
                <!-- Barre de recherche moderne -->
                <div class="relative mt-6 mb-8">
                    <input type="text" 
                           id="searchInput"
                           placeholder="Rechercher un article..." 
                           class="w-full px-4 py-3 pl-12 pr-10 text-gray-700 bg-white border border-gray-300 rounded-t-full focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all duration-300">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-purple-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <!-- Liste des résultats -->
                    <div id="searchResults" class="absolute w-full bg-white border border-gray-200 rounded-b-lg shadow-lg mt-[-1px] hidden z-50 max-h-[300px] overflow-y-auto">
                        <!-- Les résultats seront injectés ici par JavaScript -->
                    </div>
                </div>

                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const searchInput = document.getElementById('searchInput');
                    const searchResults = document.getElementById('searchResults');
                    let timeoutId;

                    // Fonction pour afficher les résultats
                    function displayResults(results) {
                        searchResults.innerHTML = '';
                        results.forEach(result => {
                            const div = document.createElement('div');
                            div.className = 'flex items-center p-3 hover:bg-gray-50 cursor-pointer transition-colors group';
                            div.innerHTML = `
                                <div class="w-12 h-12 rounded overflow-hidden flex-shrink-0">
                                    <img src="${result.image}" alt="${result.titre}" class="w-full h-full object-cover">
                                </div>
                                <div class="ml-3 flex-grow">
                                    <div class="text-sm font-medium text-gray-800 group-hover:text-purple-600 transition-colors line-clamp-1">${result.titre}</div>
                                    <div class="text-xs text-gray-500">
                                        <i class="fas fa-eye mr-1"></i>${result.vues} vues
                                    </div>
                                </div>
                            `;
                            div.addEventListener('click', () => {
                                window.location.href = `lire_article.php?id=${result.id}`;
                            });
                            searchResults.appendChild(div);
                        });
                        searchResults.classList.remove('hidden');
                    }

                    // Fonction pour faire la recherche
                    function performSearch(query = '') {
                        fetch(`search_articles.php${query ? '?query=' + encodeURIComponent(query) : ''}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    displayResults(data.results);
                                }
                            });
                    }

                    // Événement focus sur l'input
                    searchInput.addEventListener('focus', () => {
                        performSearch(); // Affiche les articles populaires
                    });

                    // Événement input pour la recherche
                    searchInput.addEventListener('input', (e) => {
                        clearTimeout(timeoutId);
                        timeoutId = setTimeout(() => {
                            const query = e.target.value.trim();
                            if (query) {
                                performSearch(query);
                            } else {
                                performSearch(); // Affiche les articles populaires si vide
                            }
                        }, 300);
                    });

                    // Fermer les résultats si on clique ailleurs
                    document.addEventListener('click', (e) => {
                        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                            searchResults.classList.add('hidden');
                        }
                    });
                });
                </script>
                <h2 class="text-xl font-bold mb-6 text-gray-800 mt-8">Articles populaires</h2>

                <div class="sidebar-articles">
                    <?php foreach ($articles_recents as $article): ?>
                        <div class="sidebar-article flex bg-white rounded-lg shadow-lg overflow-hidden mb-4 h-[150px] transform transition-all duration-300 hover:scale-102 hover:shadow-xl opacity-0 translate-x-4" data-aos="fade-left">
                            <!-- Image -->
                            <div class="w-1/3 relative overflow-hidden">
                                <img src="<?php echo htmlspecialchars($article['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($article['titre']); ?>"
                                     class="w-full h-full object-cover transition-transform duration-300 hover:scale-110">
                            </div>
                            
                            <!-- Contenu -->
                            <div class="w-2/3 flex flex-col justify-center p-4 group">
                                <h3 class="text-lg font-semibold text-gray-800 mb-2 line-clamp-2 group-hover:text-purple-600 transition-colors duration-300">
                                    <?php echo htmlspecialchars($article['titre']); ?>
                                </h3>
                                <div class="flex items-center text-sm text-gray-600">
                                    <span class="flex items-center mr-3 group-hover:text-purple-500 transition-colors duration-300">
                                        <i class="fas fa-eye mr-1"></i>
                                        <?php echo $article['vues']; ?>
                                    </span>
                                    <span class="flex items-center group-hover:text-purple-500 transition-colors duration-300">
                                        <i class="fas fa-calendar mr-1"></i>
                                        <?php echo date('d/m/Y', strtotime($article['date_publication'])); ?>
                                    </span>
                                </div>
                                <a href="lire_article.php?id=<?php echo $article['id']; ?>" 
                                   class="mt-2 text-sm text-gray-600 hover:text-purple-600 transition-all duration-300 transform group-hover:translate-x-2">
                                    Lire l'article <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
    
    <div class="contents-payant">
      <section class="premium-section">
        <h1>S'abonner pour débloquer le contenu premium</h1>
        <p>
          Sed at tellus, pharetra lacus, aenean risus non nisl ultricies commodo
          diam aliquet arcu enim eu leo porttitor habitasse adipiscing porttitor
          varius ultricies facilisis viverra lacus neque.
        </p>
        <div class="card-container">
          <!-- Première Carte -->
          <div class="card">
            <div
              class="card-image"
              style="background-image: url('Bg.jpg')"
            ></div>
            <div class="card-content">
              <p class="category">CONSEILS</p>
              <h3>A comprehensive guide on Agile development</h3>
            </div>
          </div>

          <!-- Deuxième Carte -->
          <div class="card central-card">
            <div
              class="card-image"
              style="background-image: url('imagesBlog/leader.jpeg')"
            ></div>
            <button class="unlock-button">Débloquer le contenu</button>
            <div class="card-content">
              <p class="category">CONSEILS</p>
              <h3>10 Productivity tools that are worth checking out</h3>
            </div>
          </div>

          <!-- Troisième Carte -->
          <div class="card">
            <div
              class="card-image"
              style="background-image: url('imagesBlog/lead.jpeg')"
            ></div>
            <div class="card-content">
              <p class="category">RESOURCES</p>
              <h3>Top 7 Must have management tools for productivity</h3>
            </div>
          </div>
        </div>
      </section>
    </div>
    <footer class="footer">
      <div class="footer-container">
        <!-- Colonne Ressources -->
        <div class="footer-column">
          <h4>Ressources</h4>
          <ul>
            <li><a href="#">Cartes cadeaux</a></li>
            <li><a href="#">Trouver un magasin</a></li>
            <li><a href="#">Journal Nsos</a></li>
            <li><a href="#">Devenir membre</a></li>
            <li><a href="#">Réduction pour étudiants</a></li>
            <li><a href="#">Articles</a></li>
            <li><a href="#">Conseils</a></li>
            <li><a href="#">Commentaires</a></li>
          </ul>
        </div>

        <!-- Colonne Aide -->
        <div class="footer-column">
          <h4>Aide</h4>
          <ul>
            <li><a href="#">Aide</a></li>
            <li><a href="#">Retours</a></li>
            <li><a href="#">Nous contacter</a></li>
            <li><a href="#">Avis</a></li>
            <li><a href="#">Accompagnement</a></li>
            <li><a href="#">S'abonner</a></li>
            <li><a href="#">Groupe d'entraide</a></li>
            <li><a href="#">Réseautage</a></li>
          </ul>
        </div>

        <!-- Colonne Entreprise -->
        <div class="footer-column">
          <h4>Entreprise</h4>
          <ul>
            <li><a href="#">À propos de Nsos</a></li>
            <li><a href="#">Actualités</a></li>
            <li><a href="#">Carrières</a></li>
            <li><a href="#">Investisseurs</a></li>
            <li><a href="#">Développement durable</a></li>
            <li><a href="#">Accessibilité: partiellement conforme</a></li>
            <li><a href="#">Mission</a></li>
            <li><a href="#">Signaler un problème</a></li>
          </ul>
        </div>
      </div>

      <!-- Bas de page -->
      <div class="footer-bottom">
        <p> 2024 Nike, Inc. Tous droits réservés</p>
        <ul class="footer-links">
          <li><a href="#">Guides</a></li>
          <li><a href="#">Conditions d'utilisation</a></li>
          <li><a href="#">Conditions générales de vente</a></li>
          <li><a href="#">Informations sur l'entreprise</a></li>
          <li>
            <a href="#"
              >Politique de confidentialité et de gestion des cookies</a
            >
          </li>
        </ul>
        <p>Paramètres de confidentialité et de cookies</p>
      </div>
    </footer>
  </body>
  <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
  <script>
      // Initialisation des animations AOS
      AOS.init({
          duration: 800,
          offset: 100,
          once: true
      });

      // Animation des articles de la sidebar au scroll
      document.addEventListener('DOMContentLoaded', function() {
          const sidebarArticles = document.querySelectorAll('.sidebar-article');
          
          // Fonction pour vérifier si un élément est visible
          function isElementInViewport(el) {
              const rect = el.getBoundingClientRect();
              return (
                  rect.top >= 0 &&
                  rect.left >= 0 &&
                  rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
                  rect.right <= (window.innerWidth || document.documentElement.clientWidth)
              );
          }

          // Fonction pour animer les articles
          function animateArticles() {
              sidebarArticles.forEach((article, index) => {
                  if (isElementInViewport(article)) {
                      setTimeout(() => {
                          article.style.opacity = '1';
                          article.style.transform = 'translateX(0)';
                      }, index * 200);
                  }
              });
          }

          // Lancer l'animation au chargement et au scroll
          animateArticles();
          window.addEventListener('scroll', animateArticles);
      });
  </script>
</html>
