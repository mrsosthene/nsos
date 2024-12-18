<?php
require_once 'config/connexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $article_id = filter_input(INPUT_POST, 'article_id', FILTER_VALIDATE_INT);
    $nom = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $commentaire = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING);

    // Validation des données
    $errors = [];
    if (!$article_id) {
        $errors[] = "ID d'article invalide";
    }
    if (!$nom) {
        $errors[] = "Nom invalide";
    }
    if (!$email) {
        $errors[] = "Email invalide";
    }
    if (!$commentaire) {
        $errors[] = "Commentaire invalide";
    }

    // Si pas d'erreurs, on insère le commentaire
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO commentaires (article_id, nom, email, contenu, date_creation) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            
            if ($stmt->execute([$article_id, $nom, $email, $commentaire])) {
                // Redirection vers l'article avec un message de succès
                header("Location: lire_article.php?id=" . $article_id . "&success=1");
                exit();
            } else {
                $errors[] = "Erreur lors de l'ajout du commentaire";
            }
        } catch (PDOException $e) {
            $errors[] = "Erreur technique lors de l'ajout du commentaire";
        }
    }

    // S'il y a des erreurs, redirection avec les erreurs
    if (!empty($errors)) {
        $error_string = implode(",", $errors);
        header("Location: lire_article.php?id=" . $article_id . "&error=" . urlencode($error_string));
        exit();
    }
} else {
    // Si ce n'est pas une requête POST, redirection vers la page d'accueil
    header("Location: blog.php");
    exit();
}
