<?php
$page_title = 'Data Supplier';
require_once 'header.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = (int)$_POST['id'];
        $db->exec("DELETE FROM suppliers WHERE id = $id");
        flash('msg', 'Supplier berhasil dihapus!', 'success');
        redirect('suppliers.php');
    } else {
        $name = clean($_POST['name']);
        $phone = clean($_POST['phone']);
        $address = clean($_POST['address']);
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

        if ($id > 0) {
            $stmt = $db->prepare("UPDATE suppliers SET name=?, phone=?, address=? WHERE id=?");
            $stmt->bindValue(1, $name, SQLITE3_TEXT);
            $stmt->bindValue(2, $phone, SQLITE3_TEXT);
            $stmt->bindValue(3, $address, SQLITE3_TEXT);
            $stmt->bindValue(4, $id, SQLITE3_INTEGER);
            $stmt->execute();
            flash('msg', 'Supplier berhasil diperbarui!', 'success');
        } else {
            $stmt = $db->prepare("INSERT INTO suppliers (name, phone, address) VALUES (?, ?, ?)");
            $stmt->bindValue(1, $name, SQLITE3_TEXT);
            $stmt->bindValue(2, $phone, SQLITE3_TEXT);
            $stmt->bindValue(3, $address, SQLITE3_TEXT);
            $stmt->execute();
            flash('msg', 'Supplier berhasil ditambahkan!', 'success');
        }
        redirect('suppliers.php');
    }
}

$edit_supplier = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $edit_supplier = $db->query("SELECT * FROM suppliers WHERE id = $id")->fetchArray(SQLITE3_ASSOC);
}

$suppliers = $db->query("SELECT * FROM suppliers ORDER BY name ASC");
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fa-solid fa-truck"></i> <?= $edit_supplier ? 'Edit Supplier' : 'Tambah Supplier Baru' ?></h3>
    </div>
    
    <form method="POST">
        <?php if ($edit_supplier): ?>
            <input type="hidden" name="id" value="<?= $edit_supplier['id'] ?>">
        <?php endif; ?>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div class="form-group">
                <label>Nama Supplier</label>
                <input type="text" name="name" required value="<?= $edit_supplier ? htmlspecialchars($edit_supplier['name']) : '' ?>">
            </div>
            <div class="form-group">
                <label>No. Telepon</label>
                <input type="text" name="phone" required value="<?= $edit_supplier ? htmlspecialchars($edit_supplier['phone']) : '' ?>">
            </div>
            <div class="form-group" style="grid-column: 1 / -1;">
                <label>Alamat</label>
                <textarea name="address" rows="2" required><?= $edit_supplier ? htmlspecialchars($edit_supplier['address']) : '' ?></textarea>
            </div>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 1rem;">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-save"></i> Simpan
            </button>
            <?php if ($edit_supplier): ?>
                <a href="suppliers.php" class="btn" style="background-color: var(--secondary-color); color: white;">Batal</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fa-solid fa-list"></i> Daftar Supplier</h3>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Telepon</th>
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
                        <div style="display: flex; gap: 0.5rem;">
                            <a href="suppliers.php?edit=<?= $row['id'] ?>" class="btn btn-sm" style="background-color: var(--warning-color); color: white;">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-delete" style="background-color: var(--danger-color); color: white;">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'footer.php'; ?>
