<?php
$page = 'users';

include "../middleware/admin_security.php";
include "../../config/connection.php";

include "../includes/header.php";
include "../includes/sidebar.php";
?>
<?php
$sql = "SELECT * FROM users WHERE role = 'Admin' ORDER BY id DESC ";
$query = mysqli_query($conn, $sql);

?>
<main class="flex-grow-1 p-4 bg-light">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-0 text-dark fw-bold">Kelola Admin</h1>
            <p class="text-muted small mb-0">Kelola data admin di sini.</p>
        </div>
        <div>
            <a href="create.php" class="btn btn-primary btn-sm d-flex align-items-center gap-2 px-3 py-2">
                <i class="bi bi-plus-lg"></i> Tambah Admin
            </a>
        </div>
    </div>
    <div class="card border-0 shadow-sm">
        <?php if (isset($_GET['success-update']) && $_GET['success-update'] == '1') : ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3" role="alert">
                <strong>Berhasil!</strong> Data admin berhasil diubah.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['success-delete']) && $_GET['success-delete'] == '1') : ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3" role="alert">
                <strong>Berhasil!</strong> Data admin berhasil dihapus.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0 table-hover">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="ps-3">#</th>
                            <th scope="col">Nama Pengguna</th>
                            <th scope="col">Nama Lengkap</th>
                             <th scope="col">Email</th>
                              <th scope="col">Nomor Telepon</th>
                            <th scope="col" class="text-end pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($result = mysqli_fetch_assoc($query)) : ?>
                            <tr>
                                <th scope="row" class="ps-3"><?= $no++ ?></th>
                                <td><?= htmlspecialchars($result['username']) ?></td>
                                <td><?= htmlspecialchars($result['full_name']) ?></td>
                               <td><?= htmlspecialchars($result['email']) ?></td>
                               <td><?= htmlspecialchars($result['phone_number']) ?></td>
                                <td class="text-end pe-3">
                                    <a href="edit.php?id=<?= htmlspecialchars($result['id']) ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                                    <a href="delete.php?id=<?= htmlspecialchars($result['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data admin ini?');"><i class="bi bi-trash"></i></a>
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