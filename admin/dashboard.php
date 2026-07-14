<?php
$page = 'dashboard';
include "middleware/admin_security.php";
include "../config/connection.php";
//START: query untuk menampilkan ringkasan aktivitas dan operasional toko obat
$count_process = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE status = '1'"))['total'];
$count_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM products"))['total'];
$count_category = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM categories"))['total'];
$count_all_order = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM orders"))['total'];
// END: query untuk menampilkan ringkasan aktivitas dan operasional toko obat

// START: query untuk menampilkan 5 pesanan terakhir
$query_orders = mysqli_query($conn, "SELECT * FROM orders ORDER BY order_date DESC LIMIT 5");
// END: query untuk menampilkan 5 pesanan terakhir

?>
<?php include "includes/header.php"; ?>
<?php include "includes/sidebar.php"; ?>
<div class="flex-grow-1 p-4 overflow-auto">
    <div class="mb-4">
        <h2 class="fw-bold text-dark m-0 h3">Dashboard</h2>
        <small class="text-muted">Ringkasan aktivitas dan operasional toko obat saat ini.</small>
    </div>
    <!-- START: menampilkan ringkasan aktivitas dan operasional toko obat -->
    <div class="row g-3 mb-5">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border border-light-subtle bg-white rounded-2">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-dark fw-bold h5 mb-1"><?= $count_process ?></div>
                        <span class="text-muted small">Pesanan Diproses</span>
                    </div>
                    <div class="text-warning h4 mb-0"><i class="bi bi-clock-history"></i></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border border-light-subtle bg-white rounded-2">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-dark fw-bold h5 mb-1"><?= $count_products ?></div>
                        <span class="text-muted small">Total Produk</span>
                    </div>
                    <div class="text-primary h4 mb-0"><i class="bi bi-box-seam"></i></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border border-light-subtle bg-white rounded-2">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-dark fw-bold h5 mb-1"><?= $count_category ?></div>
                        <span class="text-muted small">Kategori Obat</span>
                    </div>
                    <div class="text-success h4 mb-0"><i class="bi bi-tags"></i></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border border-light-subtle bg-white rounded-2">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-dark fw-bold h5 mb-1"><?= $count_all_order ?></div>
                        <span class="text-muted small">Total Transaksi</span>
                    </div>
                    <div class="text-info h4 mb-0"><i class="bi bi-wallet2"></i></div>
                </div>
            </div>
        </div>
    </div>
    <!-- END: menampilkan ringkasan aktivitas dan operasional toko obat -->
    <!-- START: menampilkan 5 pesanan terakhir -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-dark m-0 h5">5 Pesanan Terakhir</h5>
        <a href="orders/index.php" class="btn btn-sm btn-primary px-3 rounded-2" style="font-size: 0.85rem;">Lihat Semua</a>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0 border-top border-light-subtle">
            <thead>
                <tr class="text-dark small border-bottom border-light-subtle">
                    <th scope="col" class="fw-bold py-3" style="width: 5%;">#</th>
                    <th scope="col" class="fw-bold py-3">Kode Pesanan</th>
                    <th scope="col" class="fw-bold py-3">Alamat</th>
                    <th scope="col" class="fw-bold py-3">Resep</th>
                    <th scope="col" class="fw-bold py-3">Status</th>
                    <th scope="col" class="fw-bold py-3">Tanggal Pemesanan</th>
                    <th scope="col" class="fw-bold py-3">Catatan</th>
                    <th scope="col" class="text-end pe-1 fw-bold py-3" style="width: 15%;">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-dark small">
                <?php
                $no = 1;
                if (mysqli_num_rows($query_orders) > 0) :
                    while ($result = mysqli_fetch_assoc($query_orders)) : ?>
                        <tr class="border-bottom border-light-subtle">
                            <td><?= $no++ ?></td>
                            <td><?= $result['order_code'] ?></td>
                            <td class="text-secondary"><?= $result['shipping_address'] ?></td>
                            <td>
                                <?php if (!empty($result['recipe'])): ?>
                                    <a href="../recipe/<?= $result['recipe'] ?>" target="_blank" class="text-decoration-none text-primary">
                                        Lihat resep
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($result['status'] == '1'): ?>
                                    <span class="badge bg-warning text-dark">Diproses</span>
                                <?php elseif ($result['status'] == '2'): ?>
                                    <span class="badge bg-success">Selesai</span>
                                <?php elseif ($result['status'] == '0'): ?>
                                    <span class="badge bg-danger">Dibatalkan</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-secondary"><?= date('Y-m-d H:i:s', strtotime($result['order_date'])) ?></td>
                            <td class="text-secondary"><?= $result['notes'] ?: '-' ?></td>
                            <td class="text-end pe-1">
                                <div class="d-inline-flex gap-1 align-items-center">
                                    <a href="orders/detail.php?id=<?= $result['id'] ?>" class="btn btn-sm btn-outline-primary p-1 px-2" title="Detail" style="border-radius: 4px;">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="orders/delete.php?id=<?= $result['id'] ?>" class="btn btn-sm btn-outline-danger p-1 px-2" style="border-radius: 4px;" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile;
                else : ?>
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">Belum ada pesanan masuk.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <!-- END: menampilkan 5 pesanan terakhir -->
</div>
<?php include "includes/footer.php"; ?>