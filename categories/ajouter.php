<?php
require_once '../config/init.php';
if (session_status() == PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: index.php?err=' . urlencode("Accès interdit."));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    if (!empty($nom)) {
        $stmt = $pdo->prepare("INSERT INTO categories (nom) VALUES (?)");
        $stmt->execute([$nom]);
        header('Location: index.php?msg=' . urlencode("Catégorie ajoutée avec succès !"));
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une catégorie</title>
</head>
<body>
    <h1>Nouvelle Catégorie</h1>
    
    <form method="POST" id="addCategoryForm">
        <label>Nom de la catégorie :</label><br>
        <input type="text" name="nom" id="nomInput" placeholder="Ex: Informatique" >
        <br><br>
        <button type="submit" >Enregistrer</button>
    </form>
       <a href="index.php"><button>Annuler</button></a>
    <script>
        document.getElementById('addCategoryForm').addEventListener('submit', function(e) {
            const nom = document.getElementById('nomInput').value.trim();
            if (nom.length < 2) {
                e.preventDefault();
                alert("Le nom de la catégorie doit contenir au moins 2 caractères.");
            }
        });
    </script>
</body>
</html>