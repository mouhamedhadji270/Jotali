<?php
require_once '../config/init.php';
include('../entete.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Article invalide");
}

$id = (int) $_GET['id'];

$stmt = $pdo->prepare("
    SELECT a.*, 
           u.nom AS auteur_nom, 
           u.prenom AS auteur_prenom, 
           c.nom AS categorie
    FROM articles a
    LEFT JOIN utilisateurs u ON a.utilisateur_id = u.id
    LEFT JOIN categories c ON a.categorie_id = c.id
    WHERE a.id = :id
");

$stmt->execute([':id' => $id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    die("Article introuvable");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($article['titre']) ?></title>



</head>
<body>

<article>

<h1><?= htmlspecialchars($article['titre']) ?></h1>

<p>
    <strong>Catégorie :</strong> <?= htmlspecialchars($article['categorie']) ?> |
    <strong>Date :</strong> <?= date("d/m/Y H:i", strtotime($article['date_publication'])) ?> |
    <strong>Auteur :</strong> <?= htmlspecialchars($article['auteur_nom'] . ' ' . $article['auteur_prenom']) ?>
</p>

<hr>

<p>
    <?= nl2br(htmlspecialchars($article['contenu'])) ?>
</p>

<p>
    <a href="../accueil.php"><button>← Retour à l'accueil</button></a>
</p>

</article>

</body>
</html>