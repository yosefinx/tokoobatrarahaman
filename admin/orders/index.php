<?php
$page = 'orders';

include "../middleware/admin_security.php";
include "../../config/connection.php";

include "../includes/header.php";
include "../includes/sidebar.php";
?>
<?php
$sql = "SELECT orders.*,  pembeli.username AS username_pembeli,  admin.username AS username_admin
FROM orders JOIN users AS pembeli ON orders.id_user = pembeli.id  LEFT JOIN users AS admin ON orders.followed_up_by = admin.id 
ORDER BY orders.id DESC";
$query = mysqli_query($conn, $sql);




?>
<main class="flex-grow-1 p-4 bg-light">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-0 text-dark fw-bold">Halaman Pemesanan</h1>
            <p class="text-muted small mb-0">Kelola pesanan di sini.</p>
        </div>

    </div>
    <div class="card border-0 shadow-sm">
        <?php if (isset($_GET['success-delete']) && $_GET['success-delete'] == '1') : ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3" role="alert">
                <strong>Berhasil!</strong> Pesanan berhasil dihapus.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
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
                            <th scope="col">Followed Up By</th>
                            <th scope="col">Followed Up At</th>
                            <th scope="col" class="text-end pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($result = mysqli_fetch_assoc($query)) : ?>
                            <tr>
                                <th scope="row" class="ps-3"><?= $no++ ?></th>
                                <td><?= $result['order_code'] ?></td>
                                <td><?= $result['shipping_address'] ?></td>
                                <td><a href="../../recipe/<?= $result['recipe'] ?: '' ?>" target="_blank">
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
                                <td><?= $result['notes'] ?></td>
                                <td><?= $result['username_admin'] ?></td>
                                <td><?= $result['followed_up_at'] ?></td>
                                <td class="text-end pe-3">
                                    <div class="d-inline-flex gap-1 align-items-center">
                                        <a href="detail.php?id=<?= $result['id'] ?>" class="btn btn-sm btn-outline-primary me-1">Detail</a>
                                        <a href="delete.php?id=<?= $result['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');"><i class="bi bi-trash"></i></a>
                                        <form action="../../actions/update_status.php" method="POST" class="d-inline">
                                            <input type="hidden" name="order_code" value="<?= $result['order_code']; ?>">
                                            <?php if ($result['status'] == '1'): ?>
                                                <button class="btn btn-sm btn-outline-success" type="submit" name="status_baru" value="Selesai">Selesai</button>
                                                <button class="btn btn-sm btn-outline-danger" type="submit" name="status_baru" value="Dibatalkan">Batalkan</button>
                                            <?php elseif ($result['status'] == '2'): ?>
                                                <button class="btn btn-sm btn-outline-danger" type="submit" name="status_baru" value="Dibatalkan">Batalkan</button>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-outline-success" type="submit" name="status_baru" value="Selesai">Selesai</button>
                                            <?php endif; ?>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php
                        endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<?php include "../includes/footer.php"; ?>