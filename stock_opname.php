<?php
$page_title = 'Stock Opname';
require_once 'header.php';
requireLogin();

// Handle Form Submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)$_POST['product_id'];
    $actual_qty = (int)$_POST['actual_qty'];
    $notes = clean($_POST['notes']);
    $date = clean($_POST['date']);

    // Get System Stock
    $system_qty = $db->querySingle("SELECT stock FROM products WHERE id = $product_id");
    
    // Calculate Difference
    $difference = $actual_qty - $system_qty;

    if ($difference == 0) {
        flash('msg', 'Stok fisik sama dengan sistem, tidak ada perubahan.', 'warning');
    } else {
        // Begin Transaction
        $db->exec('BEGIN');
        
        try {
            // Insert Stock Opname Record
            $stmt = $db->prepare("INSERT INTO stock_opname (product_id, actual_qty, system_qty, difference, notes, date) VALUES (:pid, :actual, :system, :diff, :notes, :date)");
            $stmt->bindValue(':pid', $product_id, SQLITE3_INTEGER);
            $stmt->bindValue(':actual', $actual_qty, SQLITE3_INTEGER);
            $stmt->bindValue(':system', $system_qty, SQLITE3_INTEGER);
            $stmt->bindValue(':diff', $difference, SQLITE3_INTEGER);
            $stmt->bindValue(':notes', $notes, SQLITE3_TEXT);
            $stmt->bindValue(':date', $date, SQLITE3_TEXT);
            $stmt->execute();

            // Update Product Stock
            // If difference is positive, we add. If negative, we subtract (add negative).
            // Actually we can just set the stock to actual_qty
            $stmt = $db->prepare("UPDATE products SET stock = :actual WHERE id = :pid");
            $stmt->bindValue(':actual', $actual_qty, SQLITE3_INTEGER);
            $stmt->bindValue(':pid', $product_id, SQLITE3_INTEGER);
            $stmt->execute();

            $db->exec('COMMIT');
            flash('msg', 'Stock Opname berhasil disimpan. Stok diperbarui.');
            redirect('stock_opname.php');
        } catch (Exception $e) {
            $db->exec('ROLLBACK');
            flash('msg', 'Terjadi kesalahan: ' . $e->getMessage(), 'danger');
        }
    }
}

$products = $db->query("SELECT * FROM products ORDER BY name ASC");
$history = $db->query("SELECT so.*, p.name as product_name, p.size, p.brand 
                       FROM stock_opname so 
                       JOIN products p ON so.product_id = p.id 
                       ORDER BY so.date DESC, so.created_at DESC LIMIT 10");
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Input Stock Opname</h3>
    </div>
    
    <form method="POST">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label>Tanggal</label>
                <input type="date" name="date" value="<?= date('Y-m-d') ?>" required>
            </div>
            
            <div class="form-group">
                <label>Nama Barang</label>
                <select name="product_id" required id="productSelect">
                    <option value="">-- Pilih Barang --</option>
                    <?php while ($row = $products->fetchArray(SQLITE3_ASSOC)): ?>
                        <option value="<?= $row['id'] ?>" data-stock="<?= $row['stock'] ?>">
                            <?= htmlspecialchars($row['name']) ?> 
                            (Stok Sistem: <?= $row['stock'] ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Stok Fisik (Actual)</label>
                <input type="number" name="actual_qty" min="0" required>
                <small style="color: #666;">Masukkan jumlah stok yang ada di gudang saat ini.</small>
            </div>

            <div class="form-group">
                <label>Keterangan</label>
                <textarea name="notes" rows="1" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 4px;"></textarea>
            </div>
        </div>
        
        <div style="margin-top: 20px;">
            <button type="submit">Simpan & Sesuaikan Stok</button>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Riwayat Stock Opname Terakhir</h3>
    </div>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Barang</th>
                <th>Sistem</th>
                <th>Fisik</th>
                <th>Selisih</th>
                <th>Ket</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $history->fetchArray(SQLITE3_ASSOC)): ?>
            <tr>
                <td><?= $row['date'] ?></td>
                <td><?= htmlspecialchars($row['product_name']) ?> (<?= htmlspecialchars($row['brand']) ?>)</td>
                <td><?= $row['system_qty'] ?></td>
                <td><?= $row['actual_qty'] ?></td>
                <td style="color: <?= $row['difference'] >= 0 ? 'var(--success-color)' : 'var(--danger-color)' ?>">
                    <?= $row['difference'] > 0 ? '+' : '' ?><?= $row['difference'] ?>
                </td>
                <td><?= htmlspecialchars($row['notes']) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once 'footer.php'; ?>
