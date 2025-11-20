<?php
require_once 'config.php';
if (!isset($page_title)) $page_title = 'Inventory App';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - Inventory App</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php if (isLoggedIn()): ?>
<header>
    <nav>
        <a href="index.php" class="logo">Inventory App</a>
        <div class="nav-links">
            <a href="index.php">Dashboard</a>
            <a href="products.php">Produk</a>
            <a href="suppliers.php">Supplier</a>
            <a href="incoming.php">Masuk</a>
            <a href="outgoing.php">Keluar</a>
            <a href="history.php">Riwayat</a>
            <a href="stock_opname.php">Stock Opname</a>
            <a href="users.php">Akun</a>
            <a href="logout.php" style="color: var(--danger-color);">Logout</a>
        </div>
    </nav>
</header>
<?php endif; ?>

<div class="container">
    <?= flash('msg') ?>
