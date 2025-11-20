<?php
$page_title = 'Stock Opname';
require_once 'header.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)$_POST['product_id'];
    $actual_qty = (int)$_POST['actual_qty'];
    $date = clean($_POST['date']);
    $description = clean($_POST['description']);

    $current_stock = $db->querySingle("SELECT stock FROM products WHERE id = $product_id");
    $difference = $actual_qty - $current_stock;

    if ($difference == 0) {
        flash('msg', 'Stok fisik sama dengan sistem, tidak ada perubahan.', 'warning');
    } else {
        $db->exec("BEGIN");
        try {
            // Record Opname
            $stmt = $db->prepare("INSERT INTO stock_opname (product_id, date, system_qty, actual_qty, difference, description) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bindValue(1, $product_id, SQLITE3_INTEGER);
            $stmt->bindValue(2, $date, SQLITE3_TEXT);
            $stmt->bindValue(3, $current_stock, SQLITE3_INTEGER);
            $stmt->bindValue(4, $actual_qty, SQLITE3_INTEGER);
            $stmt->bindValue(5, $difference, SQLITE3_INTEGER);
            $stmt->bindValue(6, $description, SQLITE3_TEXT);
            $stmt->execute();

            // Update Product Stock
            $db->exec("UPDATE products SET stock = $actual_qty WHERE id = $product_id");

            // Optional: Record as transaction for history tracking
            $type = $difference > 0 ? 'in' : 'out';
            $qty_diff = abs($difference);
            $desc_tx = "Stock Opname Adjustment ($description)";
            
            $stmt_tx = $db->prepare("INSERT INTO transactions (product_id, type, qty, date, ref_no, description) VALUES (?, ?, ?, ?, 'OPNAME', ?)");
            $stmt_tx->bindValue(1, $product_id, SQLITE3_INTEGER);
            $stmt_tx->bindValue(2, $type, SQLITE3_TEXT);
            $stmt_tx->bindValue(3, $qty_diff, SQLITE3_INTEGER);
            $stmt_tx->bindValue(4, $date, SQLITE3_TEXT);
            $stmt_tx->bindValue(5, $desc_tx, SQLITE3_TEXT);
            $stmt_tx->execute();

            $db->exec("COMMIT");
            flash('msg', 'Stock opname berhasil disimpan!', 'success');
            redirect('stock_opname.php');
        } catch (Exception $e) {
            $db->exec("ROLLBACK");
            flash('msg', 'Terjadi kesalahan: ' . $e->getMessage(), 'danger');
        }
    }
}

$products = $db->query("SELECT * FROM products ORDER BY name ASC");
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fa-solid fa-clipboard-check"></i> Input Stock Opname</h3>
    </div>
    
    <form method="POST">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div class="form-group">
                <label>Tanggal</label>
                <input type="date" name="date" required value="<?= date('Y-m-d') ?>">
            </div>
            <div class="form-group">
                <label>Barang</label>
                <select name="product_id" required id="productSelect">
                    <option value="">-- Pilih Barang --</option>
                    <?php while ($row = $products->fetchArray(SQLITE3_ASSOC)): ?>
                        <option value="<?= $row['id'] ?>" data-stock="<?= $row['stock'] ?>"><?= htmlspecialchars($row['name']) ?> (System: <?= $row['stock'] ?>)</option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Stok Fisik (Aktual)</label>
                <input type="number" name="actual_qty" min="0" required>
            </div>
            <div class="form-group" style="grid-column: 1 / -1;">
                <label>Keterangan</label>
                <textarea name="description" rows="2"></textarea>
            </div>
        </div>

        <div style="margin-top: 1rem;">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-save"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('productSelect').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const stock = selected.getAttribute('data-stock');
    if (stock) {
        // Could auto-fill or show alert, but for now just letting user know
    }
});
</script>

<?php require_once 'footer.php'; ?>
