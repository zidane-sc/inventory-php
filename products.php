<?php
$page_title = 'Data Produk';
require_once 'header.php';
requireLogin();

// Handle Add/Edit/Delete logic here (same as before, just ensuring UI is updated)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = (int)$_POST['id'];
        // Check if used in transactions
        $check = $db->querySingle("SELECT COUNT(*) FROM transactions WHERE product_id = $id");
        if ($check > 0) {
            flash('msg', 'Gagal menghapus: Produk sudah memiliki riwayat transaksi!', 'danger');
        } else {
            $db->exec("DELETE FROM products WHERE id = $id");
            flash('msg', 'Produk berhasil dihapus!', 'success');
        }
        redirect('products.php');
    } else {
        // Add/Edit
        $name = clean($_POST['name']);
        $size = clean($_POST['size']);
        $brand = clean($_POST['brand']);
        $location = clean($_POST['location']);
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

        if ($id > 0) {
            $stmt = $db->prepare("UPDATE products SET name=?, size=?, brand=?, location=? WHERE id=?");
            $stmt->bindValue(1, $name, SQLITE3_TEXT);
            $stmt->bindValue(2, $size, SQLITE3_TEXT);
            $stmt->bindValue(3, $brand, SQLITE3_TEXT);
            $stmt->bindValue(4, $location, SQLITE3_TEXT);
            $stmt->bindValue(5, $id, SQLITE3_INTEGER);
            $stmt->execute();
            flash('msg', 'Produk berhasil diperbarui!', 'success');
        } else {
            $stmt = $db->prepare("INSERT INTO products (name, size, brand, location, stock) VALUES (?, ?, ?, ?, 0)");
            $stmt->bindValue(1, $name, SQLITE3_TEXT);
            $stmt->bindValue(2, $size, SQLITE3_TEXT);
            $stmt->bindValue(3, $brand, SQLITE3_TEXT);
            $stmt->bindValue(4, $location, SQLITE3_TEXT);
            $stmt->execute();
            flash('msg', 'Produk berhasil ditambahkan!', 'success');
        }
        redirect('products.php');
    }
}

$edit_product = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $edit_product = $db->query("SELECT * FROM products WHERE id = $id")->fetchArray(SQLITE3_ASSOC);
}

$products = $db->query("SELECT * FROM products ORDER BY name ASC");
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fa-solid fa-box"></i> <?= $edit_product ? 'Edit Produk' : 'Tambah Produk Baru' ?></h3>
    </div>
    
    <form method="POST">
        <?php if ($edit_product): ?>
            <input type="hidden" name="id" value="<?= $edit_product['id'] ?>">
        <?php endif; ?>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div class="form-group">
                <label>Nama Barang</label>
                <input type="text" name="name" required value="<?= $edit_product ? htmlspecialchars($edit_product['name']) : '' ?>">
            </div>
            <div class="form-group">
                <label>Ukuran</label>
                <input type="text" name="size" required value="<?= $edit_product ? htmlspecialchars($edit_product['size']) : '' ?>">
            </div>
            <div class="form-group">
                <label>Merk</label>
                <input type="text" name="brand" required value="<?= $edit_product ? htmlspecialchars($edit_product['brand']) : '' ?>">
            </div>
            <div class="form-group">
                <label>Lokasi</label>
                <input type="text" name="location" required value="<?= $edit_product ? htmlspecialchars($edit_product['location']) : '' ?>">
            </div>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 1rem;">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-save"></i> Simpan
            </button>
            <?php if ($edit_product): ?>
                <a href="products.php" class="btn" style="background-color: var(--secondary-color); color: white;">Batal</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fa-solid fa-list"></i> Daftar Produk</h3>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Ukuran</th>
                    <th>Merk</th>
                    <th>Lokasi</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $products->fetchArray(SQLITE3_ASSOC)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['size']) ?></td>
                    <td><?= htmlspecialchars($row['brand']) ?></td>
                    <td><?= htmlspecialchars($row['location']) ?></td>
                    <td>
                        <span class="badge" style="background-color: <?= $row['stock'] < 10 ? '#fef2f2' : '#ecfdf5' ?>; color: <?= $row['stock'] < 10 ? 'var(--danger-color)' : 'var(--success-color)' ?>">
                            <?= $row['stock'] ?>
                        </span>
                    </td>
                    <td>
                        <div style="display: flex; gap: 0.5rem;">
                            <a href="products.php?edit=<?= $row['id'] ?>" class="btn btn-sm" style="background-color: var(--warning-color); color: white;">
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
