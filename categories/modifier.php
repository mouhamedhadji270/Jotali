<?php
require_once '../config/init.php';
if (session_status() == PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) { header('Location: index.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$id]);
$cat = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    if (!empty($nom)) {
        $update = $pdo->prepare("UPDATE categories SET nom = ? WHERE id = ?");
        $update->execute([$nom, $id]);
        header('Location: index.php?msg=' . urlencode("Catégorie mise à jour !"));
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Catégorie</title>
</head>
<body>
    <h1>Modifier la catégorie</h1>
    
    <form method="POST" id="editCategoryForm">
        <label>Nom actuel :</label><br>
        <input type="text" name="nom" id="nomEdit" value="<?= htmlspecialchars($cat['nom'], ENT_QUOTES, 'UTF-8') ?>" required>
        <br><br>
        <button type="submit">Mettre à jour</button>
    </form>
      <a href="index.php"><button>Annuler</button></a>

    <script>
        document.getElementById('editCategoryForm').addEventListener('submit', function(e) {
            const nouveauNom = document.getElementById('nomEdit').value.trim();
            if (nouveauNom === "") {
                e.preventDefault();
                alert("Le nom ne peut pas être vide.");
            }
        });
    </script>
</body>
</html>