<?php
$page_title = 'Kelola Akun';
require_once 'header.php';
requireLogin();

// Handle Add User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $username = clean($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = clean($_POST['role']);

    $stmt = $db->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $stmt->bindValue(':password', $password, SQLITE3_TEXT);
    $stmt->bindValue(':role', $role, SQLITE3_TEXT);

    if ($stmt->execute()) {
        flash('msg', 'User berhasil ditambahkan');
    } else {
        flash('msg', 'Gagal menambah user (Username mungkin sudah ada)', 'danger');
    }
    redirect('users.php');
}

// Handle Delete User
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id !== $_SESSION['user_id']) { // Prevent self-delete
        $stmt = $db->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $stmt->execute();
        flash('msg', 'User berhasil dihapus');
    } else {
        flash('msg', 'Tidak bisa menghapus akun sendiri', 'danger');
    }
    redirect('users.php');
}

$users = $db->query("SELECT * FROM users ORDER BY created_at DESC");
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Pengguna</h3>
    </div>
    
    <form method="POST" style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 4px;">
        <input type="hidden" name="action" value="add">
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 10px; align-items: end;">
            <div class="form-group" style="margin-bottom: 0;">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label>Role</label>
                <select name="role">
                    <option value="staff">Staff</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <button type="submit">Tambah</button>
        </div>
    </form>

    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Role</th>
                <th>Dibuat Pada</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $users->fetchArray(SQLITE3_ASSOC)): ?>
            <tr>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><span class="badge"><?= htmlspecialchars($row['role']) ?></span></td>
                <td><?= $row['created_at'] ?></td>
                <td>
                    <?php if ($row['id'] !== $_SESSION['user_id']): ?>
                    <a href="users.php?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm btn-delete">Hapus</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once 'footer.php'; ?>
