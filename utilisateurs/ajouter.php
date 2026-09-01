<?php
require_once '../config/init.php';
if (session_status() == PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') { 
    header('Location: index.php'); 
    exit; 
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $login = trim($_POST['login']);
    $role = $_POST['role'];
    $pass = $_POST['password']; 

    if (!empty($nom) && !empty($login) && !empty($pass)) {
        $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, login, password, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nom, $prenom, $login, $pass, $role]);
        header('Location: index.php?msg=Utilisateur ajouté ');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter Utilisateur</title>
</head>
<body>
    <h1>Ajouter un utilisateur</h1>
    <form method="POST" id="userForm">
        <input type="text" name="prenom" id="prenom" placeholder="Prénom" ><br><br>
        <input type="text" name="nom" id="nom" placeholder="Nom" ><br><br>
        <input type="text" name="login" id="login" placeholder="Identifiant " ><br><br>
        <input type="password" name="password" id="password" placeholder="Mot de passe" ><br><br>
        <select name="role">
            <option value="editeur">Editeur</option>
            <option value="admin">Administrateur</option>
        </select><br><br>
        <button type="submit">Créer le compte</button>
    </form>
    <br>
    <a href="index.php"><button>Annuler</button></a>

    <script>
        document.getElementById('userForm').addEventListener('submit', function(e) {
            const login = document.getElementById('login').value.trim();
            const nom = document.getElementById('nom').value.trim();
            const prenom = document.getElementById('prenom').value.trim();
            const pass = document.getElementById('password').value;
           
            if(login === "" || prenom === "" || nom === "" || pass === ""){
                e.preventDefault();
                alert("Tous les champs doivent être remplis !!");
            }
            else if (login.length < 3 ) {
                e.preventDefault();
                alert("Le login doit faire au moins 3 caractères.");
            } else if (pass.length < 4) {
                e.preventDefault();
                alert("Le mot de passe est trop court (minimum 4 caractères).");
            } 
        });
    </script>
</body>
</html>