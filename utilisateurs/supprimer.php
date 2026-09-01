<?php
require_once '../config/init.php';
if (session_status() == PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') { exit; }

$id = $_GET['id'] ?? null;

if ($id && $id != $_SESSION['user']['id']) {
    $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?")->execute([$id]);
    header('Location: index.php?msg=Utilisateur supprimé');
} else {
    header('Location: index.php?err=Action impossible');
}
exit;