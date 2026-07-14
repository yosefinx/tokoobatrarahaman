<?php
$page = 'products';

include "../middleware/admin_security.php";
include "../../config/connection.php";

include "../includes/header.php";
include "../includes/sidebar.php";
?>
<?php
$sql = "SELECT products.*, categories.name as category_name FROM products JOIN categories ON products.id_category = categories.id ORDER BY products.id DESC";
$query = mysqli_query($conn, $sql);
?>

<main class="flex-grow-1 p-4 bg-light overflow-auto">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-0 text-dark fw-bold">Data Produk</h1>
            <p class="text-muted small mb-0">Kelola data produk obat-obatan di sini.</p>
        </div>
        <div>
            <a href="create.php" class="btn btn-primary btn-sm d-flex align-items-center gap-2 px-3 py-2">
                <i class="bi bi-plus-lg"></i> Tambah Produk
            </a>
        </div>
    </div>
    <div class="card border-0 shadow-sm">
        <!-- START: menampilkan pesan alert jika ada parameter success-create, success-update atau success-delete -->
        <?php if (isset($_GET['success-create']) && $_GET['success-create'] == '1') : ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3" role="alert">
                <strong>Berhasil!</strong> Produk berhasil ditambahkan.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['success-update']) && $_GET['success-update'] == '1') : ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3" role="alert">
                <strong>Berhasil!</strong> Produk berhasil diubah.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['success-delete']) && $_GET['success-delete'] == '1') : ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3" role="alert">
                <strong>Berhasil!</strong> Produk berhasil dihapus.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <!-- END: menampilkan pesan alert jika ada parameter success-create, success-update atau success-delete -->
        <!-- START: menampilkan tabel produk obat-obatan -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0 table-hover">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="ps-3">#</th>
                            <th scope="col">Nama Produk</th>
                            <th scope="col" class="w-25">Deskripsi</th>
                            <th scope="col">Harga</th>
                            <th scope="col">Stok</th>
                            <th scope="col">Kategori</th>
                            <th scope="col">Gambar Produk</th>
                            <th scope="col" class="text-end pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($result = mysqli_fetch_assoc($query)) : ?>
                            <tr>
                                <th scope="row" class="ps-3"><?= $no++ ?></th>
                                <td><?= htmlspecialchars($result['name']) ?></td>
                                <td><?= htmlspecialchars(mb_strimwidth($result['description'], 0, 80, "...")); ?></td>
                                <td>Rp <?= number_format($result['price'], 0, ',', '.') ?></td>
                                <td><?= htmlspecialchars($result['stock']) ?></td>
                                <td><?= htmlspecialchars($result['category_name']) ?></td>
                                <td><img style="width: 50px; height: 50px; object-fit: cover;" src="../../images/products/<?= htmlspecialchars($result['photo']) ?>"></td>
                                <td class="text-end pe-3">
                                    <div class="d-flex justify-content-end align-items-center">
                                        <a href="edit.php?id=<?= htmlspecialchars($result['id']) ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                                        <a href="delete.php?id=<?= htmlspecialchars($result['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?');"><i class="bi bi-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php
                        endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- END: menampilkan tabel produk obat-obatan -->
    </div>
</main>
<?php include "../includes/footer.php"; ?>