<?php
require_once '../config/init.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = intval($_GET['id']);

$stmt = $pdo->prepare("SELECT * FROM articles WHERE id = :id");
$stmt->execute([':id' => $id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$article) header('Location: index.php');

$categories = $pdo->query("SELECT * FROM categories ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre']);
    $contenu = trim($_POST['contenu']);
    $categorie_id = $_POST['categorie'];

    if (empty($titre)) $errors[] = "Le titre est obligatoire.";
    if (empty($contenu)) $errors[] = "Le contenu est obligatoire.";
    if (empty($categorie_id)) $errors[] = "Veuillez choisir une catégorie.";

    if (!$errors) {
        $stmt = $pdo->prepare("UPDATE articles SET titre = :titre, contenu = :contenu, categorie_id = :categorie WHERE id = :id");
        $stmt->execute([
            ':titre' => $titre,
            ':contenu' => $contenu,
            ':categorie' => $categorie_id,
            ':id' => $id
        ]);
        header('Location: index.php?msg=Article modifié avec succès');
        exit;
    }
}
?>

<h1>Modifier un article</h1>

<?php if ($errors): ?>
    <ul style="color:red;">
        <?php foreach ($errors as $e) echo "<li>$e</li>"; ?>
    </ul>
<?php endif; ?>

<form method="POST">
    <label>Titre :</label><br>
    <input type="text" name="titre" value="<?= htmlspecialchars($article['titre']) ?>"><br><br>

    <label>Catégorie :</label><br>
    <select name="categorie">
        <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= ($article['categorie_id'] == $cat['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['nom']) ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Contenu :</label><br>
    <textarea name="contenu" rows="10" cols="50"><?= htmlspecialchars($article['contenu']) ?></textarea><br><br>

    <button type="submit">Modifier</button>
</form>
      <a href="index.php"><button>Annuler</button></a>
