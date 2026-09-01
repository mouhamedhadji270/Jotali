<?php
require_once '../config/init.php';
if (session_status() == PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../index.php?err=' . urlencode("Zone réservée à l'administration."));
    exit;
}

$stmt = $pdo->query("SELECT id, nom, prenom, login, role FROM utilisateurs ORDER BY nom ASC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Utilisateurs</title>
</head>
<body>
    <h1>Liste des Utilisateurs</h1>

    <a href="ajouter.php"><button>Ajouter un utilisateur</button></a>
    <br><br>

    <table border="1" id="userTable">
        <thead>
            <tr>
                <th>Prénom</th>
                <th>Nom</th>
                <th>Login</th>
                <th>Rôle</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
                <td><?= htmlspecialchars($u['prenom'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($u['nom'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><strong><?= htmlspecialchars($u['login'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                <td><span class="badge <?= $u['role'] ?>"><?= strtoupper($u['role']) ?></span></td>
                <td>
                    <a href="modifier.php?id=<?= $u['id'] ?>"><button>Modifier</button></a>
                    <?php if ($u['id'] != $_SESSION['user']['id']): ?>
                        | <a href="supprimer.php?id=<?= $u['id'] ?>" class="delete-btn" data-name="<?= htmlspecialchars($u['prenom'] . ' ' . $u['nom']) ?>">
                            <button>Supprimer</button>
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p><a href="../articles/index.php"><button>Retour</button></a></p>

    <script>

        document.querySelectorAll('.delete-btn').forEach(link => {
            link.addEventListener('click', function(e) {
                const userName = this.getAttribute('data-name');
                if (!confirm(`Êtes-vous sûr de vouloir supprimer l'utilisateur "${userName}" ?`)) {
                    e.preventDefault();
                }
            });
        });

     
        const alertBox = document.getElementById('msg-box');
        if (alertBox) {
            setTimeout(() => {
                alertBox.style.display = 'none';
            }, 3000);
        }
    </script>
    <script>
    
    let message = new URLSearchParams(window.location.search).get('msg');

    if (message) {
        alert(message);
    }
    
</script>
</body>
</html>