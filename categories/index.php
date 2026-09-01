<?php
require_once '../config/init.php';
if (session_status() == PHP_SESSION_NONE) session_start();


if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../accueil.php?err=' . urlencode("Accès interdit."));
    exit;
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des catégories - Jotali</title>
</head>
<body>

<header>
    <h1>Jotali</h1>
    <nav>
        <a href="../accueil.php"><button>Accueil</button></a>
        <a href="../deconnexion.php"><button>Déconnexion</button></a>
    </nav>
</header>

<h1>Gestion des catégories</h1>

<a href="ajouter.php"><button> Ajouter une catégorie</button></a>



<table>
    <tr>
        <th>Nom de la catégorie</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($categories as $cat): ?>
    <tr>
        <td><?= htmlspecialchars($cat['nom'], ENT_QUOTES, 'UTF-8') ?></td>
        <td>
            <a href="modifier.php?id=<?= $cat['id'] ?>"><button>Modifier</button></a> |
            <a href="supprimer.php?id=<?= $cat['id'] ?>" class="delete-link">
                <button type="button" >Supprimer</button>
            </a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<p><a href="../articles/index.php"><button>Retour</button></a></p>

<script>
  
    const deleteLinks = document.querySelectorAll('.delete-link');
    deleteLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const confirmation = confirm("Êtes-vous sûr de vouloir supprimer cette catégorie ?\nCette action est irréversible.");
            if (!confirmation) {
                e.preventDefault(); 
            }
        });
    });
</script>
<script>
    
    let message = new URLSearchParams(window.location.search).get('msg');

    if (message) {
        alert(message);
    }
    
</script>

</body>
</html>