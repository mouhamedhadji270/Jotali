<?php
require_once '../config/init.php';
session_start();


if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = intval($_GET['id']);

$stmt = $pdo->prepare("DELETE FROM articles WHERE id = :id");
$stmt->execute([':id' => $id]);

header('Location: index.php?msg=Article supprimé avec succès');
exit;