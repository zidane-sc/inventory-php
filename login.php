<?php
require_once 'config.php';

if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean($_POST['username']);
    $password = $_POST['password'];

    $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $result = $stmt->execute();
    $user = $result->fetchArray(SQLITE3_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        redirect('index.php');
    } else {
        $error = 'Username atau password salah!';
    }
}

// Use header but without sidebar logic (handled in header.php)
require_once 'header.php'; 
?>

<div class="login-card">
    <div class="login-header">
        <div class="login-logo">
            <i class="fa-solid fa-boxes-stacked"></i>
        </div>
        <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary);">Inventory App</h2>
        <p style="color: var(--text-secondary); margin-top: 0.5rem;">Silakan login untuk melanjutkan</p>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-danger">
            <i class="fa-solid fa-circle-exclamation"></i> <?= $error ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required autofocus placeholder="Masukkan username">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required placeholder="Masukkan password">
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem;">
            Login <i class="fa-solid fa-arrow-right"></i>
        </button>
    </form>
    
    <div style="margin-top: 1.5rem; text-align: center; font-size: 0.875rem; color: var(--text-secondary); background: #f8fafc; padding: 1rem; border-radius: var(--border-radius);">
        <strong>Demo Account:</strong><br>
        admin / admin123
    </div>
</div>

<?php require_once 'footer.php'; ?>
