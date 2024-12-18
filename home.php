<?php
// Démarrer la session
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Afficher les informations de l'utilisateur
$prenom = $_SESSION['prenom'];
$nom = $_SESSION['nom'];
?>

<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Page d'accueil</title>
    <link rel="stylesheet" href="inscription.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
      /* Animation d'apparition pour le texte de bienvenue */
      @keyframes fadeIn {
        0% {
          opacity: 0;
          transform: translateY(50px);
        }
        100% {
          opacity: 1;
          transform: translateY(0);
        }
      }

      .fade-in {
        animation: fadeIn 1s ease-out;
      }
    </style>
  </head>

  <body class="bg-gray-50">

    <!-- Header/Nav -->
    <!-- Header -->
    <header class="bg-white shadow-md py-4">
      <div class="container mx-auto px-4 flex justify-between items-center">
        <a href="blog.html" class="text-xl font-bold">Nsos</a>
        <nav class="space-x-4">
          <a href="blog.html" class="text-gray-600 hover:text-blue-500">Accueil</a>
          <a href="#" class="text-gray-600 hover:text-blue-500">Categories</a>
          <a href="#" class="text-gray-600 hover:text-blue-500">Contact</a>
          <a href="inscription.php" class="text-gray-600 hover:text-blue-500"
            >S'inscrire</a
          >
        </nav>
      </div>
    </header>

    <div class=" bg-gray-100 flex items-center justify-center min-h-screen"
    style="background-image: url('imagesBlog/bg.jpg'); background-size: cover; background-position: center;">
      <div class="bg-white p-8 rounded-xl shadow-lg text-center w-full max-w-lg">

        <!-- Animation et texte de bienvenue -->
        <h1 class="text-3xl font-bold text-black mb-4 fade-in">
          Bienvenue, <?php echo $prenom . " " . $nom; ?> !
        </h1>

        <p class="text-lg text-gray-500 mb-8 fade-in" style="animation-delay: 0.5s;">
          Nous sommes heureux de vous voir parmi nous.
        </p>

        <div class="space-x-4">
          <!-- Bouton "Voir les articles" -->
          <a href="actualites.php" class="text-white bg-gray-500 hover:bg-blue-600 px-6 py-3 rounded-lg shadow-md transition duration-300">Voir les articles</a>
          <!-- Bouton "Se déconnecter" -->
          <a href="logout.php" class="text-white bg-red-500 hover:bg-red-600 px-6 py-3 rounded-lg shadow-md transition duration-300">Se déconnecter</a>
        </div>

      </div>
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
        <p>© 2024 Nsos, Inc. Tous droits réservés</p>
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
</html>
