<?php
$page = 'orders';

include "../middleware/admin_security.php";
include "../../config/connection.php";

include "../includes/header.php";
include "../includes/sidebar.php";

$id_order = $_GET['id'] ?? null;
$errors = [];

if (!$id_order) {
    header("Location: index.php");
    exit();
}

$query_products = mysqli_query($conn, "SELECT * FROM products ORDER BY name ASC");

// START: proses update total
if (isset($_POST['update_order'])) {
    $shipping_address = trim($_POST['shipping_address']);
    $notes = trim($_POST['notes']);
    $status = $_POST['status'];
    $admin_id = $_SESSION['id_user'] ?? 1;

    $update_order_sql = "UPDATE orders SET shipping_address = '$shipping_address', notes = '$notes', status = '$status',followed_up_by = '$admin_id', followed_up_at = NOW() 
    WHERE id = $id_order";

    if (mysqli_query($conn, $update_order_sql)) {
        if (isset($_POST['products']) && is_array($_POST['products'])) {
            foreach ($_POST['products'] as $id_detail => $data) {
                if (isset($data['delete']) && $data['delete'] == '1') {
                    mysqli_query($conn, "DELETE FROM orders_details WHERE id = $id_detail");
                } else {
                    $id_product = intval($data['id_product']);
                    $quantity = intval($data['quantity']);
                    if ($quantity > 0) {
                        mysqli_query($conn, "UPDATE orders_details SET id_product = $id_product, quantity = $quantity WHERE id = $id_detail");
                    }
                }
            }
        }
        if (isset($_POST['new_products']) && is_array($_POST['new_products'])) {
            foreach ($_POST['new_products'] as $item) {
                $id_product = intval($item['id_product']);
                $quantity   = intval($item['quantity']);
                if ($id_product > 0 && $quantity > 0) {
                    mysqli_query($conn, "INSERT INTO orders_details (id_order, id_product, quantity) VALUES ($id_order, $id_product, $quantity) ");
                }
            }
        }
        echo "<script>alert('Seluruh data pesanan dan produk berhasil diperbarui!'); window.location.href='edit.php?id=$id_order';</script>";
        exit();
    } else {
        $errors['database'] = "Gagal memperbarui data pesanan: " . mysqli_error($conn);
    }
}
// END: proses update total

// START: ambil data pilihan produk untuk dropdown di tabel edit nanti
$all_products = [];
$product_master_query = mysqli_query($conn, "SELECT id, name, price, stock FROM products ORDER BY name ASC");
while ($p = mysqli_fetch_assoc($product_master_query)) {
    $all_products[] = $p;
}
// END: ambil data pilihan produk


// START: ambil data pesanan dan detail produk saat ini
$sql = "SELECT 
    orders.id AS order_id,
    orders.order_code,
    orders.shipping_address,
    orders.recipe,
    orders.notes,
    orders.status,
    orders.order_date,
    orders_details.id AS id_detail,
    orders_details.id_product,
    orders_details.quantity,
    products.name AS product_name,
    products.price AS product_price
FROM orders
LEFT JOIN orders_details ON orders.id = orders_details.id_order
LEFT JOIN products ON orders_details.id_product = products.id
WHERE orders.id = $id_order";
$query = mysqli_query($conn, $sql);

$order_info = null;
$order_items = [];

while ($row = mysqli_fetch_assoc($query)) {
    if (!$order_info) {
        $order_info = [
            'order_code' => $row['order_code'],
            'shipping_address' => $row['shipping_address'],
            'recipe' => $row['recipe'],
            'status' => $row['status'],
            'order_date' => $row['order_date'],
            'notes' => $row['notes']
        ];
    }
    if ($row['id_detail']) {
        $order_items[] = [
            'id_detail' => $row['id_detail'],
            'id_product' => $row['id_product'],
            'product_name' => $row['product_name'],
            'product_price' => $row['product_price'],
            'quantity' => $row['quantity'],
            'subtotal' => $row['product_price'] * $row['quantity']
        ];
    }
}
?>

<main class="flex-grow-1 p-4 bg-light">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="index.php" class="btn btn-white border shadow-sm rounded-3 px-2 py-1.5 text-dark d-flex align-items-center justify-content-center" title="Kembali">
            <i class="bi bi-arrow-left fs-5"></i>
        </a>
        <div class="d-flex flex-column">
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item small"><a href="../dashboard.php" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item small"><a href="index.php" class="text-decoration-none text-muted">Manajemen Pesanan</a></li>
                    <li class="breadcrumb-item small active fw-semibold text-dark" aria-current="page">Edit Informasi & Item Pesanan</li>
                </ol>
            </nav>
            <h1 class="h4 mb-0 text-dark fw-bold">Edit Pesanan & Item Produk</h1>
            <p class="text-muted small mb-0">Ubah informasi transaksi beserta produk yang dibeli pelanggan.</p>
        </div>
    </div>

    <?php if (isset($errors['database'])) : ?>
        <div class="alert alert-danger border-0 shadow-sm mb-4 rounded-3" role="alert">
            <strong>Gagal!</strong> <?= $errors['database']; ?>
        </div>
    <?php endif; ?>

    <?php if ($order_info): ?>
        <form action="" method="POST">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white fw-bold py-3 border-bottom">
                            Informasi Transaksi
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="text-muted small d-block mb-1">Kode Pesanan</label>
                                <span class="fw-bold text-primary fs-5"><?= htmlspecialchars($order_info['order_code']) ?></span>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small d-block mb-1">Tanggal Masuk</label>
                                <span><?= htmlspecialchars($order_info['order_date']) ?></span>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small d-block mb-1">Resep Dokter</label>
                                <?php if ($order_info['recipe']): ?>
                                    <a href="../../recipe/<?= htmlspecialchars($order_info['recipe']) ?>" target="_blank" class="btn btn-sm btn-outline-info p-1 py-0 small mt-1">
                                        <i class="bi bi-file-earmark-medical"></i> Lihat Berkas Resep
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small italic">Tanpa Resep Dokter</span>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label fw-semibold small text-dark">Status Pesanan</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="1" <?= $order_info['status'] == '1' ? 'selected' : '' ?>>Diproses</option>
                                    <option value="2" <?= $order_info['status'] == '2' ? 'selected' : '' ?>>Selesai</option>
                                    <option value="3" <?= $order_info['status'] == '3' ? 'selected' : '' ?>>Dibatalkan</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="shipping_address" class="form-label fw-semibold small text-dark">Alamat Pengiriman</label>
                                <textarea class="form-control" id="shipping_address" name="shipping_address" rows="3" required><?= htmlspecialchars($order_info['shipping_address']) ?></textarea>
                            </div>
                            <div class="mb-0">
                                <label for="notes" class="form-label fw-semibold small text-dark">Catatan Tambahan</label>
                                <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Catatan internal..."><?= htmlspecialchars($order_info['notes'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white fw-bold py-3 border-bottom">
                            Daftar Item Produk yang Dibeli
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col" class="ps-3">Pilih Produk Obat</th>
                                            <th scope="col" class="text-center" style="width: 130px;">Jumlah (Qty)</th>
                                            <th scope="col" class="text-end">Harga Terakhir</th>
                                            <th scope="col" class="text-center" style="width: 90px;">Hapus</th>
                                        </tr>
                                    </thead>
                                    <tbody id="new-products-container">
                                        <?php
                                        $total_belanja = 0;
                                        if (!empty($order_items)):
                                            foreach ($order_items as $item):
                                                $total_belanja += $item['subtotal'];
                                                $detail_id = $item['id_detail'];
                                        ?>
                                                <tr>
                                                    <td class="ps-3">
                                                        <select name="products[<?= $detail_id ?>][id_product]" class="form-select form-select-sm">
                                                            <?php foreach ($all_products as $p): ?>
                                                                <option value="<?= $p['id'] ?>" <?= $p['id'] == $item['id_product'] ? 'selected' : '' ?>>
                                                                    <?= htmlspecialchars($p['name']) ?> — (Rp <?= number_format($p['price'], 0, ',', '.') ?>)
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="products[<?= $detail_id ?>][quantity]" class="form-control form-control-sm text-center" min="1" value="<?= htmlspecialchars($item['quantity']) ?>" required>
                                                    </td>
                                                    <td class="text-end small text-muted">
                                                        Rp <?= number_format($item['product_price'], 0, ',', '.') ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="checkbox" name="products[<?= $detail_id ?>][delete]" value="1" class="form-check-input border-danger">
                                                    </td>
                                                </tr>
                                            <?php
                                            endforeach;
                                        else:
                                            ?>
                                            <tr>
                                                <td colspan="4" class="text-center py-4 text-muted">Tidak ada item produk di dalam orderan ini.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>

                                    <?php if ($total_belanja > 0): ?>
                                        <tfoot class="table-light">
                                            <tr>
                                                <td colspan="2" class="text-end fw-bold py-3 text-secondary small">Estimasi Awal Total Belanja:</td>
                                                <td colspan="2" class="text-start fw-bold text-success fs-6 py-3 ps-3">Rp <?= number_format($total_belanja, 0, ',', '.') ?> <span class="text-muted small style-italic">(Akan disesuaikan ulang otomatis setelah disimpan)</span></td>
                                            </tr>
                                        </tfoot>
                                    <?php endif; ?>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-white py-3 border-top text-end">
                            <button
                                type="button"
                                class="btn btn-success"
                                id="btnAddProduct">
                                <i class="bi bi-plus"></i>
                                Tambah Produk
                            </button>
                            <a href="index.php" class="btn btn-light border px-4 me-2">Batal</a>
                            <button type="submit" name="update_order" class="btn btn-primary px-4 shadow-sm">Simpan Seluruh Perubahan</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    <?php else: ?>
        <div class="alert alert-danger border-0 shadow-sm">Data pesanan tidak ditemukan.</div>
    <?php endif; ?>
</main>
<script>
    let index = 0;

    document.getElementById("btnAddProduct").addEventListener("click", function() {

        const tbody = document.getElementById("new-products-container");

        tbody.insertAdjacentHTML("beforeend", `
        <tr class="new-product-row">
            <td class="ps-3">
                <select class="form-select form-select-sm"
                        name="new_products[${index}][id_product]">
                    <option value="">Pilih Produk</option>
                    <?php foreach ($all_products as $p): ?>
                        <option value="<?= $p['id'] ?>">
                            <?= htmlspecialchars($p['name']) ?>
                        </option>
                    <?php endforeach; ?>

                </select>
            </td>
            <td>
                <input
                    class="form-control form-control-sm text-center"
                    type="number"
                    name="new_products[${index}][quantity]"
                    value="1"
                    min="1">
            </td>
            <td class="text-end text-muted">
                -
            </td>
            <td class="text-center">
                <button
                    type="button"
                    class="btn btn-sm btn-outline-danger remove-row">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
    `);
        index++;
    });
    document.addEventListener("click", function(e) {

        if (e.target.closest(".remove-row")) {
            e.target.closest(".new-product-row").remove();
        }

    });
</script>
<?php include "../includes/footer.php"; ?>