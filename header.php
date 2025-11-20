<?php
require_once 'config.php';
if (!isset($page_title)) $page_title = 'Inventory App';

// Get current page for active state
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - Inventory App</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php if (isLoggedIn()): ?>
<div class="app-container">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="index.php" class="brand">
                <i class="fa-solid fa-boxes-stacked"></i> Inventory
            </a>
        </div>

        <div class="sidebar-nav">
            <a href="index.php" class="nav-item <?= $current_page == 'index.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-chart-line"></i> Dashboard
            </a>
            
            <div style="margin-top: 1rem; padding-left: 1rem; font-size: 0.75rem; color: #64748b; text-transform: uppercase; font-weight: 600; margin-bottom: 0.5rem;">Master Data</div>
            <a href="products.php" class="nav-item <?= $current_page == 'products.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-box"></i> Produk
            </a>
            <a href="suppliers.php" class="nav-item <?= $current_page == 'suppliers.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-truck"></i> Supplier
            </a>

            <div style="margin-top: 1rem; padding-left: 1rem; font-size: 0.75rem; color: #64748b; text-transform: uppercase; font-weight: 600; margin-bottom: 0.5rem;">Transaksi</div>
            <a href="incoming.php" class="nav-item <?= $current_page == 'incoming.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-arrow-right-to-bracket"></i> Barang Masuk
            </a>
            <a href="outgoing.php" class="nav-item <?= $current_page == 'outgoing.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Barang Keluar
            </a>
            <a href="stock_opname.php" class="nav-item <?= $current_page == 'stock_opname.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-clipboard-check"></i> Stock Opname
            </a>

            <div style="margin-top: 1rem; padding-left: 1rem; font-size: 0.75rem; color: #64748b; text-transform: uppercase; font-weight: 600; margin-bottom: 0.5rem;">Laporan</div>
            <a href="history.php" class="nav-item <?= $current_page == 'history.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-clock-rotate-left"></i> Riwayat
            </a>
            
            <div style="margin-top: 1rem; padding-left: 1rem; font-size: 0.75rem; color: #64748b; text-transform: uppercase; font-weight: 600; margin-bottom: 0.5rem;">Sistem</div>
            <a href="users.php" class="nav-item <?= $current_page == 'users.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-users-gear"></i> Kelola Akun
            </a>
        </div>

        <div class="user-profile">
            <div class="user-avatar">
                <?= strtoupper(substr($_SESSION['username'], 0, 1)) ?>
            </div>
            <div class="user-info">
                <div class="user-name"><?= htmlspecialchars($_SESSION['username']) ?></div>
                <div class="user-role"><?= htmlspecialchars($_SESSION['role']) ?></div>
            </div>
            <a href="logout.php" style="color: #ef4444;" title="Logout">
                <i class="fa-solid fa-power-off"></i>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Mobile Header -->
        <div class="top-bar">
            <button class="btn btn-sm" id="sidebarToggle" style="background: transparent; color: var(--text-primary); font-size: 1.25rem;">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="brand" style="color: var(--primary-color);">Inventory</div>
            <div style="width: 24px;"></div> <!-- Spacer -->
        </div>

        <?= flash('msg') ?>
<?php else: ?>
    <!-- Login Layout (No Sidebar) -->
    <div class="login-page">
<?php endif; ?>
