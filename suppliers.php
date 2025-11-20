<?php
$page_title = 'Data Supplier';
require_once 'header.php';
requireLogin();

// Handle Add/Edit Supplier
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $name = clean($_POST['name']);
    $phone = clean($_POST['phone']);
    $address = clean($_POST['address']);

    if ($action === 'add') {
        $stmt = $db->prepare("INSERT INTO suppliers (name, phone, address) VALUES (:name, :phone, :address)");
        $stmt->bindValue(':name', $name, SQLITE3_TEXT);
        $stmt->bindValue(':phone', $phone, SQLITE3_TEXT);
        $stmt->bindValue(':address', $address, SQLITE3_TEXT);
        
        if ($stmt->execute()) {
            flash('msg', 'Supplier berhasil ditambahkan');
        } else {
            flash('msg', 'Gagal menambah supplier', 'danger');
        }
    } elseif ($action === 'edit') {
        $id = (int)$_POST['id'];
        $stmt = $db->prepare("UPDATE suppliers SET name = :name, phone = :phone, address = :address WHERE id = :id");
        $stmt->bindValue(':name', $name, SQLITE3_TEXT);
        $stmt->bindValue(':phone', $phone, SQLITE3_TEXT);
        $stmt->bindValue(':address', $address, SQLITE3_TEXT);
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);

        if ($stmt->execute()) {
            flash('msg', 'Supplier berhasil diperbarui');
        } else {
            flash('msg', 'Gagal memperbarui supplier', 'danger');
        }
    }
    redirect('suppliers.php');
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    // Check if used in transactions (optional, but good practice)
    // For now, just delete
    $stmt = $db->prepare("DELETE FROM suppliers WHERE id = :id");
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    if ($stmt->execute()) {
        flash('msg', 'Supplier berhasil dihapus');
    } else {
        flash('msg', 'Gagal menghapus supplier', 'danger');
    }
    redirect('suppliers.php');
}

// Fetch for Edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $db->prepare("SELECT * FROM suppliers WHERE id = :id");
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $edit_data = $result->fetchArray(SQLITE3_ASSOC);
}

$suppliers = $db->query("SELECT * FROM suppliers ORDER BY name ASC");
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= $edit_data ? 'Edit Supplier' : 'Tambah Supplier' ?></h3>
    </div>
    
    <form method="POST">
        <input type="hidden" name="action" value="<?= $edit_data ? 'edit' : 'add' ?>">
        <?php if ($edit_data): ?>
            <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 1fr 1fr 2fr auto; gap: 10px; align-items: end;">
            <div class="form-group" style="margin-bottom: 0;">
                <label>Nama Supplier</label>
                <input type="text" name="name" value="<?= $edit_data ? htmlspecialchars($edit_data['name']) : '' ?>" required>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label>No HP</label>
                <input type="text" name="phone" value="<?= $edit_data ? htmlspecialchars($edit_data['phone']) : '' ?>">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label>Alamat</label>
                <input type="text" name="address" value="<?= $edit_data ? htmlspecialchars($edit_data['address']) : '' ?>">
            </div>
            <div>
                <button type="submit"><?= $edit_data ? 'Simpan Perubahan' : 'Tambah' ?></button>
                <?php if ($edit_data): ?>
                    <a href="suppliers.php" class="btn" style="background-color: #95a5a6;">Batal</a>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Supplier</h3>
    </div>
    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>No HP</th>
                <th>Alamat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $suppliers->fetchArray(SQLITE3_ASSOC)): ?>
            <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['phone']) ?></td>
                <td><?= htmlspecialchars($row['address']) ?></td>
                <td>
                    <a href="suppliers.php?edit=<?= $row['id'] ?>" class="btn btn-sm" style="background-color: #f39c12;">Edit</a>
                    <a href="suppliers.php?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm btn-delete">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once 'footer.php'; ?>
