<?php
$page_title = 'Data Produk';
require_once 'header.php';
requireLogin();

// Handle Add/Edit Product
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $name = clean($_POST['name']);
    $size = clean($_POST['size']);
    $brand = clean($_POST['brand']);
    $location = clean($_POST['location']);

    if ($action === 'add') {
        $stmt = $db->prepare("INSERT INTO products (name, size, brand, location) VALUES (:name, :size, :brand, :location)");
        $stmt->bindValue(':name', $name, SQLITE3_TEXT);
        $stmt->bindValue(':size', $size, SQLITE3_TEXT);
        $stmt->bindValue(':brand', $brand, SQLITE3_TEXT);
        $stmt->bindValue(':location', $location, SQLITE3_TEXT);
        
        if ($stmt->execute()) {
            flash('msg', 'Produk berhasil ditambahkan');
        } else {
            flash('msg', 'Gagal menambah produk', 'danger');
        }
    } elseif ($action === 'edit') {
        $id = (int)$_POST['id'];
        $stmt = $db->prepare("UPDATE products SET name = :name, size = :size, brand = :brand, location = :location WHERE id = :id");
        $stmt->bindValue(':name', $name, SQLITE3_TEXT);
        $stmt->bindValue(':size', $size, SQLITE3_TEXT);
        $stmt->bindValue(':brand', $brand, SQLITE3_TEXT);
        $stmt->bindValue(':location', $location, SQLITE3_TEXT);
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);

        if ($stmt->execute()) {
            flash('msg', 'Produk berhasil diperbarui');
        } else {
            flash('msg', 'Gagal memperbarui produk', 'danger');
        }
    }
    redirect('products.php');
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    // Check if has transactions
    $check = $db->querySingle("SELECT COUNT(*) FROM transactions WHERE product_id = $id");
    if ($check > 0) {
        flash('msg', 'Produk tidak bisa dihapus karena sudah ada transaksi', 'danger');
    } else {
        $stmt = $db->prepare("DELETE FROM products WHERE id = :id");
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        if ($stmt->execute()) {
            flash('msg', 'Produk berhasil dihapus');
        } else {
            flash('msg', 'Gagal menghapus produk', 'danger');
        }
    }
    redirect('products.php');
}

// Fetch for Edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $db->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $edit_data = $result->fetchArray(SQLITE3_ASSOC);
}

$products = $db->query("SELECT * FROM products ORDER BY name ASC");
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= $edit_data ? 'Edit Produk' : 'Tambah Produk' ?></h3>
    </div>
    
    <form method="POST">
        <input type="hidden" name="action" value="<?= $edit_data ? 'edit' : 'add' ?>">
        <?php if ($edit_data): ?>
            <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr auto; gap: 10px; align-items: end;">
            <div class="form-group" style="margin-bottom: 0;">
                <label>Nama Barang</label>
                <input type="text" name="name" value="<?= $edit_data ? htmlspecialchars($edit_data['name']) : '' ?>" required>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label>Ukuran</label>
                <input type="text" name="size" value="<?= $edit_data ? htmlspecialchars($edit_data['size']) : '' ?>">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label>Merk</label>
                <input type="text" name="brand" value="<?= $edit_data ? htmlspecialchars($edit_data['brand']) : '' ?>">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label>Lokasi</label>
                <input type="text" name="location" value="<?= $edit_data ? htmlspecialchars($edit_data['location']) : '' ?>">
            </div>
            <div>
                <button type="submit"><?= $edit_data ? 'Simpan' : 'Tambah' ?></button>
                <?php if ($edit_data): ?>
                    <a href="products.php" class="btn" style="background-color: #95a5a6;">Batal</a>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Produk</h3>
    </div>
    <table>
        <thead>
            <tr>
                <th>Nama Barang</th>
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
                <td style="font-weight: bold; color: <?= $row['stock'] > 0 ? 'var(--success-color)' : 'var(--danger-color)' ?>">
                    <?= $row['stock'] ?>
                </td>
                <td>
                    <a href="products.php?edit=<?= $row['id'] ?>" class="btn btn-sm" style="background-color: #f39c12;">Edit</a>
                    <a href="products.php?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm btn-delete">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once 'footer.php'; ?>
