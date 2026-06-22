<?php
$page = 'orders';

include "../middleware/admin_security.php";
include "../../config/connection.php";

include "../includes/header.php";
include "../includes/sidebar.php";
?>
<?php
$id_order = $_GET['id'] ?? null;
if (!$id_order) {
    header("Location: index.php");
    exit();
}
$sql = "SELECT 
    orders.id AS order_id,
    orders.id_user,
    orders.order_code,
    orders.shipping_address,
    orders.recipe,
    orders.notes,
    orders.status,
    orders.followed_up_by,
    orders.order_date,
    orders.followed_up_at,
    orders_details.id AS id_detail,
    orders_details.id_product,
    orders_details.quantity,
    products.name AS product_name,
    products.photo AS product_photo,
    products.id_category AS product_id_category,
    products.price AS product_price,
    products.stock AS product_stock,
    products.description AS product_description
FROM orders
LEFT JOIN orders_details ON orders.id = orders_details.id_order
LEFT JOIN products ON orders_details.id_product = products.id
WHERE orders.id = $id_order";
$query = mysqli_query($conn, $sql);

$order_info = null;
$products = [];

while ($row = mysqli_fetch_assoc($query)) {
    if (!$order_info) {
        $order_info = [
            'order_code'       => $row['order_code'],
            'shipping_address' => $row['shipping_address'],
            'recipe'           => $row['recipe'],
            'status'           => $row['status'],
            'order_date'       => $row['order_date'],
            'notes'            => $row['notes'],
            'followed_up_at'   => $row['followed_up_at']
        ];
    }
    if ($row['id_detail']) {
        $products[] = [
            'product_name'  => $row['product_name'],
            'product_photo' => $row['product_photo'],
            'product_price' => $row['product_price'],
            'quantity'      => $row['quantity'],
            'subtotal'      => $row['product_price'] * $row['quantity']
        ];
    }
}

?>
<main class="flex-grow-1 p-4 bg-light">
    <div>
        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="index.php" class="btn btn-white border shadow-sm rounded-3 px-2 py-1.5 text-dark d-flex align-items-center justify-content-center" title="Kembali">
                <i class="bi bi-arrow-left fs-5"></i>
            </a>
            <div class="d-flex flex-column">
                <nav aria-label="breadcrumb" class="mb-1">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item small"><a href="../dashboard.php" class="text-decoration-none text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item small"><a href="index.php" class="text-decoration-none text-muted">Manajemen Pesanan</a></li>
                        <li class="breadcrumb-item small active fw-semibold text-dark" aria-current="page">Detail Pesanan</li>
                    </ol>
                </nav>

                <h1 class="h4 mb-0 text-dark fw-bold">Detail Pesanan</h1>
                <p class="text-muted small mb-0">Kelola data pesanan di sini.</p>
            </div>
        </div>

        <?php if ($order_info): ?>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white fw-bold py-3">
                        Informasi Transaksi
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="text-muted small d-block">Kode Pesanan</label>
                            <span class="fw-bold text-primary"><?= $order_info['order_code'] ?></span>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small d-block">Tanggal Pemesanan</label>
                            <span><?= $order_info['order_date'] ?></span>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small d-block">Status</label>
                            <?php if ($order_info['status'] == '1'): ?>
                                <span class="badge bg-warning text-dark">Diproses</span>
                            <?php elseif ($order_info['status'] == '2'): ?>
                                <span class="badge bg-success">Selesai</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Dibatalkan</span>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small d-block">Resep</label>
                            <?= $order_info['recipe'] ? '<a href="../../recipe/'.$order_info['recipe'].'" target="_blank" class="btn btn-sm btn-outline-info p-1 py-0 small">Lihat Resep</a>' : '<span class="text-muted">-</span>' ?>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small d-block">Alamat Pengiriman</label>
                            <p class="mb-0 bg-light p-2 rounded small"><?= $order_info['shipping_address'] ?></p>
                        </div>
                        <div class="mb-0">
                            <label class="text-muted small d-block">Catatan Tambahan</label>
                            <span class="small italic text-secondary"><?= $order_info['notes'] ?: '-' ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white fw-bold py-3">
                        Item yang Dibeli
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" class="ps-3" style="width: 80px;">Foto</th>
                                        <th scope="col">Nama Produk</th>
                                        <th scope="col" class="text-end">Harga Satuan</th>
                                        <th scope="col" class="text-center">Jumlah</th>
                                        <th scope="col" class="text-end pe-3">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $total_belanja = 0;
                                    if (!empty($products)): 
                                        foreach ($products as $item): 
                                            $total_belanja += $item['subtotal'];
                                    ?>
                                        <tr>
                                            <td class="ps-3">
                                                <img src="../../images/products/<?= $item['product_photo'] ?: '#' ?>" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;" alt="">
                                            </td>
                                            <td>
                                                <span class="fw-semibold d-block"><?= $item['product_name'] ?></span>
                                            </td>
                                            <td class="text-end">Rp <?= number_format($item['product_price'], 0, ',', '.') ?></td>
                                            <td class="text-center"><?= $item['quantity'] ?></td>
                                            <td class="text-end pe-3 fw-bold text-dark">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                                        </tr>
                                    <?php 
                                        endforeach; 
                                    else: 
                                    ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">Tidak ada detail produk untuk pesanan ini.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                                <?php if ($total_belanja > 0): ?>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="4" class="text-end fw-bold py-3">Total Pembayaran:</td>
                                        <td class="text-end pe-3 fw-bold text-success fs-5 py-3">Rp <?= number_format($total_belanja, 0, ',', '.') ?></td>
                                    </tr>
                                </tfoot>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
            <div class="alert alert-danger">Data pesanan tidak ditemukan.</div>
        <?php endif; ?>
    </div>
</main>
<?php include "../includes/footer.php"; ?>