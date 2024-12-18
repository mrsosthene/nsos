<?php

// Informations de connexion à la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'projet_php');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4")
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}

// Fonction pour gérer l'upload d'images
if (isset($_FILES['file'])) {
    $response = array();
    $file = $_FILES['file'];
    
    // Vérifier les erreurs
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $response = array('error' => 'Erreur lors du téléchargement');
        echo json_encode($response);
        exit;
    }

    // Vérifier le type de fichier
    $allowed_types = array('image/jpeg', 'image/png', 'image/gif');
    if (!in_array($file['type'], $allowed_types)) {
        $response = array('error' => 'Type de fichier non autorisé');
        echo json_encode($response);
        exit;
    }

    // Créer le dossier uploads s'il n'existe pas
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Générer un nom de fichier unique
    $filename = uniqid() . '_' . basename($file['name']);
    $filepath = $upload_dir . $filename;

    // Déplacer le fichier
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        $response = array(
            'location' => $filepath // TinyMCE attend une clé 'location'
        );
    } else {
        $response = array('error' => 'Erreur lors de l\'enregistrement du fichier');
    }

    echo json_encode($response);
    exit;
}

// Vérification de la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_FILES['file'])) {
    // Récupération du titre sans encodage
    $titre = trim($_POST['titre']);
    $contenu = $_POST['contenu'];
    $premium = isset($_POST['premium']) ? intval($_POST['premium']) : 0;
    $date_publication = date('Y-m-d H:i:s'); // Date actuelle
    $statut = 'publie';

    try {
        $stmt = $pdo->prepare("INSERT INTO articles (titre, contenu, date_publication, statut, premium) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$titre, $contenu, $date_publication, $statut, $premium]);
        
        $article_id = $pdo->lastInsertId();
        
        header('Location: actualites.php');
        exit();
    } catch(PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un article</title>
    <script src="https://cdn.tiny.cloud/1/ih6f834scrcsq2g32n8gm97fxig2p505hsdjg77tdcl1r7bc/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Ajouter un article</h1>
            <a href="articles.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Voir tous les articles
            </a>
        </div>
        
        <form action="add_article.php" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4" enctype="multipart/form-data">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="titre">
                    Titre de l'article
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="titre" type="text" name="titre" required>
            </div>
            
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="contenu">
                    Contenu de l'article
                </label>
                <textarea id="contenu" name="contenu"></textarea>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Type d'article
                </label>
                <div class="mt-2">
                    <label class="inline-flex items-center">
                        <input type="radio" class="form-radio" name="premium" value="0" checked>
                        <span class="ml-2">Standard</span>
                    </label>
                    <label class="inline-flex items-center ml-6">
                        <input type="radio" class="form-radio" name="premium" value="1">
                        <span class="ml-2">Premium</span>
                    </label>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="image">
                    Image
                </label>
                <input type="file" name="image" id="image" accept="image/jpeg, image/png, image/gif">
            </div>
            
            <div class="flex items-center justify-between">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                    type="submit">
                    Publier l'article
                </button>
                <div class="flex gap-4">
                    <a href="articles.php" class="text-gray-600 hover:text-gray-900">Annuler</a>
                    <?php if (isset($article_id)): ?>
                        <a href="article.php?id=<?php echo $article_id; ?>" class="text-blue-600 hover:text-blue-800">Voir l'article</a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>

    <script>
        tinymce.init({
            selector: '#contenu',
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
            images_upload_url: 'add_article.php',
            automatic_uploads: true,
            images_reuse_filename: true,
            file_picker_types: 'image',
            image_dimensions: true,
            image_class_list: [
                {title: 'Responsive', value: 'img-fluid'},
                {title: 'Petite', value: 'img-small'},
                {title: 'Moyenne', value: 'img-medium'},
                {title: 'Grande', value: 'img-large'}
            ],
            content_style: `
                body { font-family:Helvetica,Arial,sans-serif; font-size:14px }
                .img-fluid { max-width: 100%; height: auto; }
                .img-small { max-width: 300px; height: auto; }
                .img-medium { max-width: 500px; height: auto; }
                .img-large { max-width: 800px; height: auto; }
            `,
            image_advtab: true,
            file_picker_callback: function(callback, value, meta) {
                var input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');

                input.onchange = function() {
                    var file = this.files[0];
                    var formData = new FormData();
                    formData.append('file', file);

                    fetch('add_article.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.location) {
                            // Créer une image temporaire pour obtenir les dimensions
                            var img = new Image();
                            img.onload = function() {
                                callback(result.location, {
                                    alt: file.name,
                                    width: this.width,
                                    height: this.height
                                });
                            };
                            img.src = result.location;
                        } else {
                            throw new Error(result.error || 'Upload failed');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Upload failed');
                    });
                };

                input.click();
            }
        });
    </script>
</body>
</html>
