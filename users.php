<?php
$page_title = 'Kelola Akun';
require_once 'header.php';
requireLogin();

// Only admin can access
if ($_SESSION['role'] !== 'admin') {
    flash('msg', 'Akses ditolak!', 'danger');
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = (int)$_POST['id'];
        if ($id == $_SESSION['user_id']) {
            flash('msg', 'Tidak bisa menghapus akun sendiri!', 'danger');
        } else {
            $db->exec("DELETE FROM users WHERE id = $id");
            flash('msg', 'User berhasil dihapus!', 'success');
        }
        redirect('users.php');
    } else {
        $username = clean($_POST['username']);
        $password = $_POST['password'];
        $role = clean($_POST['role']);
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

        if ($id > 0) {
            // Update
            if (!empty($password)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("UPDATE users SET username=?, password=?, role=? WHERE id=?");
                $stmt->bindValue(1, $username, SQLITE3_TEXT);
                $stmt->bindValue(2, $hash, SQLITE3_TEXT);
                $stmt->bindValue(3, $role, SQLITE3_TEXT);
                $stmt->bindValue(4, $id, SQLITE3_INTEGER);
            } else {
                $stmt = $db->prepare("UPDATE users SET username=?, role=? WHERE id=?");
                $stmt->bindValue(1, $username, SQLITE3_TEXT);
                $stmt->bindValue(2, $role, SQLITE3_TEXT);
                $stmt->bindValue(3, $id, SQLITE3_INTEGER);
            }
            $stmt->execute();
            flash('msg', 'User berhasil diperbarui!', 'success');
        } else {
            // Insert
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->bindValue(1, $username, SQLITE3_TEXT);
            $stmt->bindValue(2, $hash, SQLITE3_TEXT);
            $stmt->bindValue(3, $role, SQLITE3_TEXT);
            $stmt->execute();
            flash('msg', 'User berhasil ditambahkan!', 'success');
        }
        redirect('users.php');
    }
}

$edit_user = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $edit_user = $db->query("SELECT * FROM users WHERE id = $id")->fetchArray(SQLITE3_ASSOC);
}

$users = $db->query("SELECT * FROM users ORDER BY username ASC");
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fa-solid fa-users-gear"></i> <?= $edit_user ? 'Edit User' : 'Tambah User Baru' ?></h3>
    </div>
    
    <form method="POST">
        <?php if ($edit_user): ?>
            <input type="hidden" name="id" value="<?= $edit_user['id'] ?>">
        <?php endif; ?>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required value="<?= $edit_user ? htmlspecialchars($edit_user['username']) : '' ?>">
            </div>
            <div class="form-group">
                <label>Password <?= $edit_user ? '(Kosongkan jika tidak diubah)' : '' ?></label>
                <input type="password" name="password" <?= $edit_user ? '' : 'required' ?>>
            </div>
            <div class="form-group">
                <label>Role</label>
                <select name="role" required>
                    <option value="admin" <?= ($edit_user && $edit_user['role'] == 'admin') ? 'selected' : '' ?>>Admin</option>
                    <option value="staff" <?= ($edit_user && $edit_user['role'] == 'staff') ? 'selected' : '' ?>>Staff</option>
                </select>
            </div>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 1rem;">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-save"></i> Simpan
            </button>
            <?php if ($edit_user): ?>
                <a href="users.php" class="btn" style="background-color: var(--secondary-color); color: white;">Batal</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fa-solid fa-list"></i> Daftar User</h3>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $users->fetchArray(SQLITE3_ASSOC)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td>
                        <span class="badge" style="background-color: <?= $row['role'] == 'admin' ? '#eff6ff' : '#f8fafc' ?>; color: <?= $row['role'] == 'admin' ? 'var(--primary-color)' : 'var(--text-secondary)' ?>">
                            <?= ucfirst($row['role']) ?>
                        </span>
                    </td>
                    <td>
                        <div style="display: flex; gap: 0.5rem;">
                            <a href="users.php?edit=<?= $row['id'] ?>" class="btn btn-sm" style="background-color: var(--warning-color); color: white;">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <?php if ($row['id'] != $_SESSION['user_id']): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-delete" style="background-color: var(--danger-color); color: white;">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'footer.php'; ?>
