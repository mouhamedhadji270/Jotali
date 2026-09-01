<?php
require_once 'config/init.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


if (isset($_SESSION['user'])) {
    header("Location: ./articles/index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']); 
    $password = trim($_POST['password']);

    if (empty($login) || empty($password)) {
        $error = "Veuillez remplir tous les champs.";
    } else {
 
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE login = :login LIMIT 1");
        $stmt->execute([':login' => $login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);


        if ($user && $password === $user['password']) {
            
            $_SESSION['user'] = [
                'id' => $user['id'],
                'nom' => $user['nom'],
                'prenom' => $user['prenom'],
                'role' => $user['role'],
                'login' => $user['login']
            ];
            
            header("Location: ./articles/index.php");
            exit;
        } else {
            $error = "Identifiant ou mot de passe incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>CONNEXION</title>
</head>
<body>

<div class="container">
    <form id="loginForm" method="POST" action="">
        <h1>Connexion</h1>

        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <input type="text" id="login" name="login" 
               value="<?php echo isset($login) ? htmlspecialchars($login) : ''; ?>" 
               placeholder="Votre login">
        
        <input type="password" id="password" name="password" placeholder="Mot de passe">
        
        <button type="submit">Se connecter</button>
    </form>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', function(event) {
    const login = document.getElementById('login').value.trim();
    const password = document.getElementById('password').value.trim();
    
    if (login === "" || password === "") {
        event.preventDefault(); 
        alert("Tous les champs doivent être remplis !");
    }
});
</script>

</body>
</html>