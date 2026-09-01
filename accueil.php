<?php
require_once 'config/init.php';
// L'entête contient déjà le début du HTML (DOCTYPE, head, navbar)
include("entete.php");
// Le menu contient généralement les catégories ou liens latéraux
include("menu.php");

// --- LOGIQUE PHP ---
$articlesParPage = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $articlesParPage;

// Préparation du paramètre de catégorie pour les liens de pagination
$cat_id = isset($_GET['categorie']) && is_numeric($_GET['categorie']) ? (int)$_GET['categorie'] : null;
$cat_param = $cat_id ? '&categorie=' . $cat_id : '';

// 1. Construction de la requête principale
$sql = "
    SELECT a.*, 
           u.nom AS auteur_nom, 
           u.prenom AS auteur_prenom, 
           c.nom AS categorie
    FROM articles a
    LEFT JOIN utilisateurs u ON a.utilisateur_id = u.id
    LEFT JOIN categories c ON a.categorie_id = c.id
";

if ($cat_id) {
    $sql .= " WHERE a.categorie_id = :categorie ";
}

$sql .= " ORDER BY a.date_publication DESC LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limit', $articlesParPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

if ($cat_id) {
    $stmt->bindValue(':categorie', $cat_id, PDO::PARAM_INT);
}

$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2. Calcul du total pour la pagination
if ($cat_id) {
    $stmtTotal = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE categorie_id = :categorie");
    $stmtTotal->execute([':categorie' => $cat_id]);
    $totalArticles = $stmtTotal->fetchColumn();
} else {
    $totalArticles = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
}

$totalPages = ceil($totalArticles / $articlesParPage);
?>

<style>
    :root {
        --bordeaux: #630d16;
        --bordeaux-light: #800020;
        --bg-gray: #f4f4f4;
        --text-color: #333;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: var(--bg-gray);
        color: var(--text-color);
        margin: 0;
        line-height: 1.6;
    }

    .container {
        max-width: 1000px;
        margin: 40px auto;
        padding: 0 20px;
    }

    h1.main-title {
        color: var(--bordeaux);
        text-align: center;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin-bottom: 40px;
        border-bottom: 3px solid var(--bordeaux);
        display: inline-block;
        padding-bottom: 5px;
    }

    article {
        background: #fff;
        padding: 25px;
        margin-bottom: 25px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        border-left: 6px solid var(--bordeaux);
        transition: transform 0.2s ease;
    }

    article:hover {
        transform: scale(1.01);
    }

    article h2 {
        margin-top: 0;
        color: var(--bordeaux-light);
    }

    .meta {
        font-size: 0.9rem;
        color: #777;
        margin-bottom: 15px;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }

    .meta strong { color: var(--bordeaux); }

    .read-more {
        display: inline-block;
        margin-top: 15px;
        background-color: var(--bordeaux);
        color: #fff !important;
        padding: 8px 20px;
        text-decoration: none;
        border-radius: 4px;
        font-weight: bold;
        transition: background 0.3s;
    }

    .read-more:hover {
        background-color: var(--bordeaux-light);
    }

    /* Pagination */
    .pagination {
        text-align: center;
        margin: 50px 0;
    }

    .pagination a, .pagination strong {
        padding: 10px 15px;
        margin: 0 5px;
        border: 1px solid var(--bordeaux);
        text-decoration: none;
        color: var(--bordeaux);
        border-radius: 4px;
        transition: all 0.3s;
    }

    .pagination a:hover {
        background-color: var(--bordeaux);
        color: #fff;
    }

    .pagination strong {
        background-color: var(--bordeaux);
        color: #fff;
    }
</style>

<main class="container">
    <h1 class="main-title">Derniers articles</h1>

    <?php if (empty($articles)): ?>
        <div style="text-align:center; padding: 50px; background: white; border-radius: 8px;">
            <p>Aucun article ne correspond à votre recherche.</p>
            <a href="accueil.php" class="read-more">Voir tous les articles</a>
        </div>
    <?php else: ?>

        <?php foreach ($articles as $article): ?>
        <article>
            <h2><?= htmlspecialchars($article['titre']) ?></h2>

            <div class="meta">
                <strong>Catégorie :</strong> <?= htmlspecialchars($article['categorie'] ?? 'Général') ?> |
                <strong>Date :</strong> <?= date("d/m/Y", strtotime($article['date_publication'])) ?> |
                <strong>Auteur :</strong> <?= htmlspecialchars(($article['auteur_nom'] ?? '') . ' ' . ($article['auteur_prenom'] ?? '')) ?>
            </div>

            <p>
                <?php
                $excerpt = substr(strip_tags($article['contenu']), 0, 200);
                $lastSpace = strrpos($excerpt, ' ');
                if ($lastSpace !== false) $excerpt = substr($excerpt, 0, $lastSpace);
                echo nl2br($excerpt) . '...';
                ?>
            </p>

            <a href="articles/article.php?id=<?= $article['id'] ?>" class="read-more">Lire la suite</a>
        </article>
        <?php endforeach; ?>

        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 . $cat_param ?>">« Précédent</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i == $page): ?>
                    <strong><?= $i ?></strong>
                <?php else: ?>
                    <a href="?page=<?= $i . $cat_param ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 . $cat_param ?>">Suivant »</a>
            <?php endif; ?>
        </div>

    <?php endif; ?>
</main>

</body>
</html>