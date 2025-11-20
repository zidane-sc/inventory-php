<?php
$page_title = 'Barang Keluar';
require_once 'header.php';
requireLogin();

// Handle Form Submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)$_POST['product_id'];
    $qty = (int)$_POST['qty'];
    $reference_no = clean($_POST['reference_no']);
    $destination = clean($_POST['destination']);
    $notes = clean($_POST['notes']);
    $date = clean($_POST['date']);

    // Check Stock First
    $current_stock = $db->querySingle("SELECT stock FROM products WHERE id = $product_id");

    if ($qty <= 0) {
        flash('msg', 'Jumlah harus lebih dari 0', 'danger');
    } elseif ($current_stock < $qty) {
        flash('msg', "Stok tidak mencukupi! Stok saat ini: $current_stock", 'danger');
    } else {
        // Begin Transaction
        $db->exec('BEGIN');
        
        try {
            // Insert Transaction
            $stmt = $db->prepare("INSERT INTO transactions (type, product_id, qty, reference_no, location_destination, notes, date) VALUES ('out', :pid, :qty, :ref, :dest, :notes, :date)");
            $stmt->bindValue(':pid', $product_id, SQLITE3_INTEGER);
            $stmt->bindValue(':qty', $qty, SQLITE3_INTEGER);
            $stmt->bindValue(':ref', $reference_no, SQLITE3_TEXT);
            $stmt->bindValue(':dest', $destination, SQLITE3_TEXT);
            $stmt->bindValue(':notes', $notes, SQLITE3_TEXT);
            $stmt->bindValue(':date', $date, SQLITE3_TEXT);
            $stmt->execute();

            // Update Stock
            $stmt = $db->prepare("UPDATE products SET stock = stock - :qty WHERE id = :pid");
            $stmt->bindValue(':qty', $qty, SQLITE3_INTEGER);
            $stmt->bindValue(':pid', $product_id, SQLITE3_INTEGER);
            $stmt->execute();

            $db->exec('COMMIT');
            flash('msg', 'Barang keluar berhasil dicatat');
            redirect('outgoing.php');
        } catch (Exception $e) {
            $db->exec('ROLLBACK');
            flash('msg', 'Terjadi kesalahan: ' . $e->getMessage(), 'danger');
        }
    }
}

$products = $db->query("SELECT * FROM products WHERE stock > 0 ORDER BY name ASC");
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Input Barang Keluar</h3>
    </div>
    
    <form method="POST">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label>Tanggal</label>
                <input type="date" name="date" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="form-group">
                <label>No. Surat Jalan (SJ)</label>
                <input type="text" name="reference_no" required placeholder="Contoh: SJ-2023-001">
            </div>
            
            <div class="form-group">
                <label>Nama Barang</label>
                <select name="product_id" required id="productSelect">
                    <option value="">-- Pilih Barang --</option>
                    <?php while ($row = $products->fetchArray(SQLITE3_ASSOC)): ?>
                        <option value="<?= $row['id'] ?>" data-stock="<?= $row['stock'] ?>">
                            <?= htmlspecialchars($row['name']) ?> 
                            (Stok: <?= $row['stock'] ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Jumlah (Qty)</label>
                <input type="number" name="qty" min="1" required id="qtyInput">
                <small id="stockHelp" style="color: #666;"></small>
            </div>

            <div class="form-group">
                <label>Tujuan</label>
                <input type="text" name="destination" placeholder="Customer A, Cabang B, dll" required>
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

<script>
    // Simple client-side validation/helper
    const productSelect = document.getElementById('productSelect');
    const qtyInput = document.getElementById('qtyInput');
    const stockHelp = document.getElementById('stockHelp');

    productSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const stock = selectedOption.getAttribute('data-stock');
        if (stock) {
            qtyInput.max = stock;
            stockHelp.textContent = `Maksimal: ${stock}`;
        } else {
            qtyInput.removeAttribute('max');
            stockHelp.textContent = '';
        }
    });
</script>

<?php require_once 'footer.php'; ?>
