<?php
$page = 'orders';

include "../middleware/admin_security.php";
include "../../config/connection.php";

include "../includes/header.php";
include "../includes/sidebar.php";
?>
<?php
$sql = "SELECT * FROM orders ORDER BY id DESC";
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
        <?php if (isset($_GET['success-update']) && $_GET['success-update'] == '1') : ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3" role="alert">
                <strong>Berhasil!</strong> Kategori berhasil diubah.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['success-delete']) && $_GET['success-delete'] == '1') : ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3" role="alert">
                <strong>Berhasil!</strong> Kategori berhasil dihapus.
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
                                <td></td>
                                <td><?= $result['status'] ?></td>
                                <td><?= $result['order_date'] ?></td>
                                <td><?= $result['notes'] ?></td>
                                <td class="text-end pe-3">
                                    <a href="edit.php?id=<?= $result['id'] ?>" class="btn btn-sm btn-outline-primary me-1">Detail</a>
                                    <a href="delete.php?id=<?= $result['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');"><i class="bi bi-trash"></i></a>
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