<?php
$page_title = 'Barang Masuk';
require_once 'header.php';
requireLogin();

// Handle Form Submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)$_POST['product_id'];
    $qty = (int)$_POST['qty'];
    $reference_no = clean($_POST['reference_no']);
    $location = clean($_POST['location']);
    $notes = clean($_POST['notes']);
    $date = clean($_POST['date']);

    if ($qty <= 0) {
        flash('msg', 'Jumlah harus lebih dari 0', 'danger');
    } else {
        // Begin Transaction
        $db->exec('BEGIN');
        
        try {
            // Insert Transaction
            $stmt = $db->prepare("INSERT INTO transactions (type, product_id, qty, reference_no, location_destination, notes, date) VALUES ('in', :pid, :qty, :ref, :loc, :notes, :date)");
            $stmt->bindValue(':pid', $product_id, SQLITE3_INTEGER);
            $stmt->bindValue(':qty', $qty, SQLITE3_INTEGER);
            $stmt->bindValue(':ref', $reference_no, SQLITE3_TEXT);
            $stmt->bindValue(':loc', $location, SQLITE3_TEXT);
            $stmt->bindValue(':notes', $notes, SQLITE3_TEXT);
            $stmt->bindValue(':date', $date, SQLITE3_TEXT);
            $stmt->execute();

            // Update Stock
            $stmt = $db->prepare("UPDATE products SET stock = stock + :qty WHERE id = :pid");
            $stmt->bindValue(':qty', $qty, SQLITE3_INTEGER);
            $stmt->bindValue(':pid', $product_id, SQLITE3_INTEGER);
            $stmt->execute();

            $db->exec('COMMIT');
            flash('msg', 'Barang masuk berhasil dicatat');
            redirect('incoming.php');
        } catch (Exception $e) {
            $db->exec('ROLLBACK');
            flash('msg', 'Terjadi kesalahan: ' . $e->getMessage(), 'danger');
        }
    }
}

$products = $db->query("SELECT * FROM products ORDER BY name ASC");
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Input Barang Masuk</h3>
    </div>
    
    <form method="POST">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label>Tanggal</label>
                <input type="date" name="date" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="form-group">
                <label>No. PO / FPB</label>
                <input type="text" name="reference_no" required placeholder="Contoh: PO-2023-001">
            </div>
            
            <div class="form-group">
                <label>Nama Barang</label>
                <select name="product_id" required>
                    <option value="">-- Pilih Barang --</option>
                    <?php while ($row = $products->fetchArray(SQLITE3_ASSOC)): ?>
                        <option value="<?= $row['id'] ?>">
                            <?= htmlspecialchars($row['name']) ?> 
                            (<?= htmlspecialchars($row['brand']) ?> - <?= htmlspecialchars($row['size']) ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Jumlah (Qty)</label>
                <input type="number" name="qty" min="1" required>
            </div>

            <div class="form-group">
                <label>Lokasi Penyimpanan</label>
                <input type="text" name="location" placeholder="Rak A1, Gudang Utama, dll">
            </div>

            <div class="form-group">
                <label>Keterangan</label>
                <textarea name="notes" rows="1" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 4px;"></textarea>
            </div>
        </div>
        
        <div style="margin-top: 20px;">
            <button type="submit">Simpan Transaksi</button>
        </div>
    </form>
</div>

<?php require_once 'footer.php'; ?>
