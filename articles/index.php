<?php
require_once '../config/init.php';

if(session_status() == PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION['user'])){
    header('Location: ../connexion.php');
    exit;
}

$id_connecte = $_SESSION['user']['id'];
$role_connecte = $_SESSION['user']['role'];


if ($role_connecte === 'admin') {
    $sql = "SELECT a.*, c.nom AS categorie 
            FROM articles a 
            LEFT JOIN categories c ON a.categorie_id = c.id 
            ORDER BY a.date_publication DESC";
    $stmt = $pdo->query($sql);
} else {
    $sql = "SELECT a.*, c.nom AS categorie 
            FROM articles a 
            LEFT JOIN categories c ON a.categorie_id = c.id 
            WHERE a.utilisateur_id = ? 
            ORDER BY a.date_publication DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_connecte]);
}

$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des articles - Jotali</title>
</head>
<body>

<header>
    <h1>Jotali</h1>
    <nav>
        <ul>
            <li><a href="../accueil.php"><button>Accueil</button></a></li>
            <li><a href="../deconnexion.php"><button>Déconnexion</button></a></li>
        </ul>
    </nav> 
</header>

<h1>Gestion des articles</h1>

<div style="margin-bottom: 20px;">
    <a href="ajouter.php" class="btn btn-add"><button>Ajouter un article</button></a>
    <a href="../categories/index.php" class="btn btn-cat"><button>Gérer les catégories</button></a>
    
    <?php if ($role_connecte === 'admin'): ?>
        <a href="../utilisateurs/index.php"><button>Gestion des utilisateurs</button></a>
    <?php endif; ?>
</div>

<?php if (count($articles) > 0): ?>
    <table border="1" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>Titre</th>
                <th>Catégorie</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($articles as $article): ?>
                <tr>
                    <td><?= ($article['titre']) ?></td>
                    <td><?= ($article['categorie'] ?? 'Sans catégorie') ?></td>
                    <td><?= date("d/m/Y", strtotime($article['date_publication'])) ?></td>
                    <td>
                        <a href="modifier.php?id=<?= $article['id'] ?>"><button class="btn btn-mod">Modifier</button></a>
                        
                        <a href="supprimer.php?id=<?= $article['id'] ?>" class="confirm-action" data-type="article">
                            <button class="btn btn-sup">Supprimer</button>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Aucun article trouvé.</p>
<?php endif; ?>

<p><br><a href="../accueil.php"><button>← Retour à l'accueil</button></a></p>

<script>
    const urlParams = new URLSearchParams(window.location.search);
    const msg = urlParams.get('msg');
    const err = urlParams.get('err');
    
    if (msg) alert(msg);
    if (err) alert("Erreur : " + err);

    const deleteLinks = document.querySelectorAll('.confirm-action');
    deleteLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const type = this.getAttribute('data-type');
            const confirmation = confirm(`Voulez-vous vraiment supprimer cet ${type} ?`);
            
            if (!confirmation) {
                e.preventDefault();
            }
        });
    });
</script>

</body>
</html>