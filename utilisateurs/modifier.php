<?php
require_once '../config/init.php';
if (session_status() == PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') { header('Location: index.php'); exit; }

$id = $_GET['id'] ?? null;
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$id]);
$u = $stmt->fetch();

if (!$u) { header('Location: index.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $login = trim($_POST['login']);
    $role = $_POST['role'];
    $new_pass = $_POST['password'];

    if (!empty($new_pass)) {
        $sql = "UPDATE utilisateurs SET nom=?, prenom=?, login=?, role=?, password=? WHERE id=?";
        $params = [$nom, $prenom, $login, $role, password_hash($new_pass, PASSWORD_DEFAULT), $id];
    } else {
        $sql = "UPDATE utilisateurs SET nom=?, prenom=?, login=?, role=? WHERE id=?";
        $params = [$nom, $prenom, $login, $role, $id];
    }

    $pdo->prepare($sql)->execute($params);
    header('Location: index.php?msg=Mis à jour');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Utilisateur</title>
</head>
<body>
    <h1>Modifier l'utilisateur</h1>
    <form method="POST" id="editForm">
        <input type="text" name="prenom" id="prenom" value="<?= htmlspecialchars($u['prenom']) ?>" required><br><br>
        <input type="text" name="nom" id="nom" value="<?= htmlspecialchars($u['nom']) ?>" required><br><br>
        <input type="text" name="login" id="login" value="<?= htmlspecialchars($u['login']) ?>" required><br><br>
        <select name="role" id="role">
            <option value="editeur" <?= $u['role'] == 'editeur' ? 'selected' : '' ?>>Editeur</option>
            <option value="admin" <?= $u['role'] == 'admin' ? 'selected' : '' ?>>Administrateur</option>
        </select><br><br>
        <input type="password" name="password" id="password" placeholder="Nouveau mot de passe (optionnel)"><br><br>
        <button type="submit">Enregistrer</button>
    </form>
    <a href="index.php"><button>Annuler</button></a>

    <script>
        document.getElementById('editForm').addEventListener('submit', function(e) {
            const currentRole = "<?= $u['role'] ?>";
            const newRole = document.getElementById('role').value;

            if (currentRole === 'admin' && newRole === 'editeur') {
                if (!confirm("Attention : Vous allez retirer les droits administrateur à cet utilisateur. Continuer ?")) {
                    e.preventDefault();
                }
            }
        });
    </script>
</body>
</html>