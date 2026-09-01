<?php
require_once '../config/init.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);

$errors = [];
$success_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre']);
    $contenu = trim($_POST['contenu']);
    $categorie_id = $_POST['categorie'];

    if (empty($titre)) $errors[] = "Le titre est obligatoire.";
    if (empty($contenu)) $errors[] = "Le contenu est obligatoire.";
    if (empty($categorie_id)) $errors[] = "Veuillez choisir une catégorie.";

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO articles (titre, contenu, categorie_id, utilisateur_id) VALUES (:titre, :contenu, :categorie, :auteur)");
            $stmt->execute([
                ':titre'     => $titre, 
                ':contenu'   => $contenu,
                ':categorie' => $categorie_id,
                ':auteur'    => $_SESSION['user']['id']
            ]);

            header('Location: index.php?msg=' . urlencode('Article ajouté avec succès !'));           
             exit;
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de l'enregistrement : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un article</title>
</head>
<body>

    <h1>Ajouter un article</h1>

    <?php if (!empty($errors)): ?>
        <ul class="error-list">
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST" id="loginform">
        <div>
            <label for="titre">Titre :</label><br>
            <input type="text" name="titre" id="titre" value="<?= isset($titre) ? htmlspecialchars($titre, ENT_QUOTES, 'UTF-8') : '' ?>">
        </div><br>

        <div>
            <label for="categorie">Catégorie :</label><br>
            <select name="categorie" id="categorie">
                <option value="">-- Choisir une catégorie --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= (isset($categorie_id) && $categorie_id == $cat['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['nom'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div><br>

        <div>
            <label for="contenu">Contenu :</label><br>
            <textarea name="contenu" id="contenu" rows="10"><?= isset($contenu) ? htmlspecialchars($contenu, ENT_QUOTES, 'UTF-8') : '' ?></textarea>
        </div><br>

        <button type="submit">Publier l'article</button>
    </form>
        <a href="index.php"><button>Annuler</button></a>


    <script>
        document.getElementById("loginform").addEventListener('submit', function(event) {          
            const titre = document.getElementById('titre').value.trim();
            const contenu = document.getElementById('contenu').value.trim();
            const categorie = document.getElementById('categorie').value;
            
            let errorMessages = [];

            if (titre === "") {
                errorMessages.push("Le titre ne peut pas être vide.");
            }
            if (contenu === "") {
                errorMessages.push("Le contenu ne peut pas être vide.");
            }
            if (categorie === "") {
                errorMessages.push("Veuillez sélectionner une catégorie.");
            }

            if (errorMessages.length > 0) {
                event.preventDefault();
                
                alert("Attention :\n\n" + errorMessages.join("\n"));
            } else {
                alert("Validation réussie ! Envoi de votre article...");
            }
        });
    </script>

</body>
</html>