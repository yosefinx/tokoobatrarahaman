<?php
$page = 'orders';

include "../middleware/admin_security.php";
include "../../config/connection.php";

include "../includes/header.php";
include "../includes/sidebar.php";
?>
<?php
// START: melakukan query untuk mengambil data pesanan beserta username pembeli dan admin yang menindaklanjuti
$sql = "SELECT orders.*,  pembeli.username AS username_pembeli,  admin.username AS username_admin
FROM orders JOIN users AS pembeli ON orders.id_user = pembeli.id  LEFT JOIN users AS admin ON orders.followed_up_by = admin.id 
ORDER BY orders.id DESC";
$query = mysqli_query($conn, $sql);
// END: melakukan query untuk mengambil data pesanan beserta username pembeli dan admin yang menindaklanjuti
?>
<main class="flex-grow-1 p-4 bg-light overflow-auto">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-0 text-dark fw-bold">Halaman Pemesanan</h1>
            <p class="text-muted small mb-0">Kelola pesanan di sini.</p>
        </div>
    </div>
    <div class="card border-0 shadow-sm">
        <!-- START: menampilkan pesan alert jika ada parameter success-delete -->
        <?php if (isset($_GET['success-delete']) && $_GET['success-delete'] == '1') : ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3" role="alert">
                <strong>Berhasil!</strong> Pesanan berhasil dihapus.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <!-- END: menampilkan pesan alert jika ada parameter success-delete -->
        <!-- START: menampilkan tabel pesanan -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0 table-hover">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="ps-3">#</th>
                            <th scope="col">Kode Pesanan</th>
                            <th scope="col">Alamat</th>
                            <th scope="col">Resep</th>
                            <th scope="col">Status</th>
                            <th scope="col">Tanggal Pemesanan</th>
                            <th scope="col">Catatan</th>
                            <th scope="col">Diperbarui Oleh</th>
                            <th scope="col">Diperbarui Pada</th>
                            <th scope="col" class="text-end pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($result = mysqli_fetch_assoc($query)) : ?>
                            <tr>
                                <th scope="row" class="ps-3"><?= $no++ ?></th>
                                <td><?= htmlspecialchars($result['order_code']) ?></td>
                                <td><?= htmlspecialchars($result['shipping_address']) ?></td>
                                <td><a href="../../recipe/<?= htmlspecialchars($result['recipe'] ?? '') ?>" target="_blank">
                                        <?= $result['recipe'] ? 'Lihat resep' : '' ?>
                                    </a></td>
                                <td><?php if ($result['status'] == '1'): ?>
                                        <span class="badge bg-warning text-dark">Diproses</span>
                                    <?php elseif ($result['status'] == '2'): ?>
                                        <span class="badge bg-success">Selesai</span>
                                    <?php elseif ($result['status'] == '0'): ?>
                                        <span class="badge bg-danger">Dibatalkan</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Tidak Diketahui</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $result['order_date'] ?></td>
                                <td><?= htmlspecialchars($result['notes']) ?></td>
                                <td><?= htmlspecialchars($result['username_admin'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($result['followed_up_at'] ?? '-') ?></td>
                                <td class="text-end pe-3">
                                    <div class="d-flex flex-nowrap justify-content-end align-items-center gap-1">
                                        <a href="detail.php?id=<?= htmlspecialchars($result['id']) ?>" class="btn btn-sm btn-outline-primary me-1">Detail</a>
                                        <a href="delete.php?id=<?= htmlspecialchars($result['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus order ini?');"><i class="bi bi-trash"></i></a>
                                        <!-- START: form untuk mengubah status pesanan -->
                                        <form action="../../actions/update_status.php" method="POST" class="d-flex flex-nowrap gap-1 m-0 justify-content-end">
                                            <input type="hidden" name="order_code" value="<?= $result['order_code']; ?>">
                                            <?php if ($result['status'] == '1'): ?>
                                                <button class="btn btn-sm btn-outline-success" type="submit" name="status_baru" value="Selesai">Selesai</button>
                                                <button class="btn btn-sm btn-outline-danger" type="submit" name="status_baru" value="Dibatalkan">Batalkan</button>
                                            <?php elseif ($result['status'] == '2'): ?>
                                                <button class="btn btn-sm btn-outline-warning" type="submit" name="status_baru" value="Diproses">Diproses</button>
                                                <button class="btn btn-sm btn-outline-danger" type="submit" name="status_baru" value="Dibatalkan">Batalkan</button>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-outline-warning" type="submit" name="status_baru" value="Diproses">Diproses</button>
                                                <button class="btn btn-sm btn-outline-success" type="submit" name="status_baru" value="Selesai">Selesai</button>
                                            <?php endif; ?>
                                        </form>
                                        <!-- END: form untuk mengubah status pesanan -->
                                    </div>
                                </td>
                            </tr>
                        <?php
                        endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- END: menampilkan tabel pesanan -->
    </div>
</main>
<?php include "../includes/footer.php"; ?>