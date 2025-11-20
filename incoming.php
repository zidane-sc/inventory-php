<?php
$page_title = 'Barang Masuk';
require_once 'header.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)$_POST['product_id'];
    $qty = (int)$_POST['qty'];
    $date = clean($_POST['date']);
    $ref_no = clean($_POST['ref_no']); // No PO/FPB
    $description = clean($_POST['description']);

    if ($qty <= 0) {
        flash('msg', 'Jumlah harus lebih dari 0!', 'danger');
    } else {
        $db->exec("BEGIN");
        try {
            // Insert transaction
            $stmt = $db->prepare("INSERT INTO transactions (product_id, type, qty, date, ref_no, description) VALUES (?, 'in', ?, ?, ?, ?)");
            $stmt->bindValue(1, $product_id, SQLITE3_INTEGER);
            $stmt->bindValue(2, $qty, SQLITE3_INTEGER);
            $stmt->bindValue(3, $date, SQLITE3_TEXT);
            $stmt->bindValue(4, $ref_no, SQLITE3_TEXT);
            $stmt->bindValue(5, $description, SQLITE3_TEXT);
            $stmt->execute();

            // Update stock
            $db->exec("UPDATE products SET stock = stock + $qty WHERE id = $product_id");

            $db->exec("COMMIT");
            flash('msg', 'Barang masuk berhasil dicatat!', 'success');
            redirect('incoming.php');
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
        <h3 class="card-title"><i class="fa-solid fa-arrow-right-to-bracket"></i> Input Barang Masuk</h3>
    </div>
    
    <form method="POST">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div class="form-group">
                <label>Tanggal</label>
                <input type="date" name="date" required value="<?= date('Y-m-d') ?>">
            </div>
            <div class="form-group">
                <label>No. PO / FPB</label>
                <input type="text" name="ref_no" required placeholder="Contoh: PO-2023-001">
            </div>
            <div class="form-group">
                <label>Barang</label>
                <select name="product_id" required>
                    <option value="">-- Pilih Barang --</option>
                    <?php while ($row = $products->fetchArray(SQLITE3_ASSOC)): ?>
                        <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?> (Stok: <?= $row['stock'] ?>)</option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Jumlah Masuk</label>
                <input type="number" name="qty" min="1" required>
            </div>
            <div class="form-group" style="grid-column: 1 / -1;">
                <label>Keterangan</label>
                <textarea name="description" rows="2"></textarea>
            </div>
        </div>

        <div style="margin-top: 1rem;">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-save"></i> Simpan Transaksi
            </button>
        </div>
    </form>
</div>

<?php require_once 'footer.php'; ?>
