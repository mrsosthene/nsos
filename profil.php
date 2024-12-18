<?php
session_start();
require_once 'config.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}

// Récupérer les informations de l'utilisateur
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Traitement du formulaire de mise à jour
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $nouveau_mot_de_passe = trim($_POST['nouveau_mot_de_passe']);
    
    $errors = [];
    
    // Validation des champs
    if (empty($nom)) {
        $errors[] = "Le nom est requis";
    }
    if (empty($email)) {
        $errors[] = "L'email est requis";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email n'est pas valide";
    }
    
    // Vérifier si l'email existe déjà (sauf pour l'utilisateur actuel)
    if (!empty($email)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $user_id]);
        if ($stmt->fetch()) {
            $errors[] = "Cet email est déjà utilisé";
        }
    }
    
    // Si pas d'erreurs, mettre à jour le profil
    if (empty($errors)) {
        try {
            if (!empty($nouveau_mot_de_passe)) {
                // Mise à jour avec nouveau mot de passe
                $hashed_password = password_hash($nouveau_mot_de_passe, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET nom = ?, email = ?, password = ? WHERE id = ?");
                $stmt->execute([$nom, $email, $hashed_password, $user_id]);
            } else {
                // Mise à jour sans mot de passe
                $stmt = $pdo->prepare("UPDATE users SET nom = ?, email = ? WHERE id = ?");
                $stmt->execute([$nom, $email, $user_id]);
            }
            
            // Mettre à jour les informations de session
            $_SESSION['nom'] = $nom;
            
            $success = "Profil mis à jour avec succès";
            
            // Recharger les informations de l'utilisateur
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
        } catch(PDOException $e) {
            $errors[] = "Erreur lors de la mise à jour du profil";
        }
    }
}

// Initialiser les compteurs à 0 pour le moment
$comments_count = 0;
$likes_count = 0;
$recent_articles = [];

// Formater la date d'inscription
$date_inscription = isset($user['date_inscription']) ? date('d/m/Y', strtotime($user['date_inscription'])) : date('d/m/Y');

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - <?php echo htmlspecialchars($user['nom']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="blog.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f3f4f6;
        }
        .profile-header {
            background: linear-gradient(135deg, #374151 0%, #1f2937 100%);
        }
        .form-input {
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }
        .form-input:focus {
            transform: translateY(-1px);
            border-color: #9ca3af;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            background: linear-gradient(135deg, #4b5563 0%, #374151 100%);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(75, 85, 99, 0.4);
        }
        .nav-container {
            background-color: #1f2937;
            padding: 1rem;
        }
        .nav-links a {
            color: #e5e7eb;
            transition: color 0.3s ease;
        }
        .nav-links a:hover {
            color: #ffffff;
        }
        .stat-card {
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <?php include 'includes/nav.php'; ?>
    
    <main class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            <!-- En-tête du profil -->
            <div class="profile-header rounded-t-2xl p-8 text-white mb-6">
                <div class="flex items-center space-x-4">
                    <div class="w-20 h-20 rounded-full bg-white/20 flex items-center justify-center">
                        <span class="text-3xl"><?php echo strtoupper(substr($user['nom'], 0, 1)); ?></span>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold"><?php echo htmlspecialchars($user['nom']); ?></h1>
                        <p class="text-gray-200 mt-1">Membre depuis le <?php echo $date_inscription; ?></p>
                    </div>
                </div>
            </div>

            <!-- Carte principale -->
            <div class="bg-white rounded-2xl shadow-sm p-8 border border-gray-200">
                <?php if (!empty($errors)): ?>
                    <div class="mb-6 p-4 rounded-lg bg-red-50 border-l-4 border-red-500">
                        <?php foreach ($errors as $error): ?>
                            <p class="text-red-700"><?php echo htmlspecialchars($error); ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($success)): ?>
                    <div class="mb-6 p-4 rounded-lg bg-green-50 border-l-4 border-green-500">
                        <p class="text-green-700"><?php echo htmlspecialchars($success); ?></p>
                    </div>
                <?php endif; ?>

                <!-- Formulaire de modification -->
                <form action="" method="POST" class="space-y-6">
                    <div>
                        <label for="nom" class="block text-sm font-medium text-gray-700">Nom</label>
                        <input type="text" id="nom" name="nom" 
                               value="<?php echo htmlspecialchars($user['nom']); ?>"
                               class="form-input mt-1 block w-full rounded-lg">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="email" name="email" 
                               value="<?php echo htmlspecialchars($user['email']); ?>"
                               class="form-input mt-1 block w-full rounded-lg">
                    </div>

                    <div>
                        <label for="nouveau_mot_de_passe" class="block text-sm font-medium text-gray-700">
                            Nouveau mot de passe
                        </label>
                        <input type="password" id="nouveau_mot_de_passe" name="nouveau_mot_de_passe"
                               placeholder="Laisser vide pour ne pas changer"
                               class="form-input mt-1 block w-full rounded-lg">
                    </div>

                    <div class="flex justify-end space-x-4 pt-4">
                        <a href="blog.php" 
                           class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-all duration-200">
                            Retour
                        </a>
                        <button type="submit" 
                                class="btn-primary px-6 py-3 text-white rounded-lg">
                            Mettre à jour le profil
                        </button>
                    </div>
                </form>
            </div>

            <!-- Statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                <div class="stat-card p-6 rounded-xl">
                    <div class="text-gray-600 text-sm">Commentaires</div>
                    <div class="mt-2 text-3xl font-semibold text-gray-800"><?php echo $comments_count; ?></div>
                </div>
                <div class="stat-card p-6 rounded-xl">
                    <div class="text-gray-600 text-sm">J'aime</div>
                    <div class="mt-2 text-3xl font-semibold text-gray-800"><?php echo $likes_count; ?></div>
                </div>
                <div class="stat-card p-6 rounded-xl">
                    <div class="text-gray-600 text-sm">Statut</div>
                    <div class="mt-2 text-xl font-semibold text-gray-800"><?php echo ucfirst($user['role'] ?? 'Membre'); ?></div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
