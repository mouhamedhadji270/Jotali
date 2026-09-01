<?php

if (!isset($categories)) {
    $stmtMenu = $pdo->query("SELECT * FROM categories ORDER BY nom ASC");
    $categoriesMenu = $stmtMenu->fetchAll(PDO::FETCH_ASSOC);
} else {
    $categoriesMenu = $categories;
}
?>

<div class="sidebar-menu">
    <h3>Catégories</h3>
    <ul class="nav-list">
        <li class="nav-item">
            <a href="accueil.php" class="<?= !isset($_GET['categorie']) ? 'active' : '' ?>">
                Toutes
            </a>
        </li>


        <?php foreach ($categoriesMenu as $cat): ?>
            <?php 
                $isActive = (isset($_GET['categorie']) && $_GET['categorie'] == $cat['id']) ? 'active' : '';
            ?>
            <li class="nav-item">
                <a href="accueil.php?categorie=<?= $cat['id'] ?>" class="<?= $isActive ?>">
                     <?= htmlspecialchars($cat['nom']) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

  
</div>

