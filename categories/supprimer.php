<?php
require_once '../config/init.php';
if (session_status() == PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: index.php?err=' . urlencode("Action non autorisée."));
    exit;
}

$id = $_GET['id'] ?? null;

if ($id) {
    $check = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE categorie_id = ?");
    $check->execute([$id]);
    
    if ($check->fetchColumn() > 0) {
        header('Location: index.php?err=' . urlencode("Impossible : Des articles utilisent encore cette catégorie."));
    } else {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: index.php?msg=' . urlencode("Catégorie supprimée avec succès."));
    }
} else {
    header('Location: index.php');
}
exit;