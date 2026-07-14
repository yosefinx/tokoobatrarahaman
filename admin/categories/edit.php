<?php
$page = 'categories';

include "../middleware/admin_security.php";
include "../../config/connection.php";

include "../includes/header.php";
include "../includes/sidebar.php";
?>
<?php
$id = $_GET['id'] ?? '';

// START: jika id kosong, redirect ke halaman index.php 
if ($id == '') {
    header("Location: index.php");
    exit;
}
// END: jika id kosong, redirect ke halaman index.php

$sql = "SELECT * FROM categories WHERE id = $id";
$query = mysqli_query($conn, $sql);
$result = mysqli_fetch_assoc($query);

if (!$result) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['edit'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);

    // START: validasi input untuk mengubah kategori
    if (empty($name)) {
        $errors['name'] = "Nama kategori wajib diisi.";
    } elseif (strlen($name) < 3) {
        $errors['name'] = "Nama kategori minimal harus 3 karakter.";
    }

    if (strlen($description) > 255) {
        $errors['description'] = "Deskripsi tidak boleh lebih dari 255 karakter.";
    }
    // END: validasi input untuk mengubah kategori
    // START: menyimpan perubahan kategori ke database jika tidak ada error
    if (empty($errors)) {
        $sql = "UPDATE categories SET name = '$name', description = '$description' WHERE id = $id";
        if (mysqli_query($conn, $sql)) {
            echo "<script>window.location.href='index.php?success-update=1';</script>";
            exit;
        } else {
            $errors['database'] = "Gagal mengubah kategori: " . mysqli_error($conn);
        }
    }
    // END: menyimpan perubahan kategori ke database jika tidak ada error
}
?>
<main class="flex-grow-1 p-4 bg-light min-vh-100">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="index.php" class="btn btn-white border shadow-sm rounded-3 px-2 py-1.5 text-dark d-flex align-items-center justify-content-center" title="Kembali">
            <i class="bi bi-arrow-left fs-5"></i>
        </a>
        <div class="d-flex flex-column">
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item small"><a href="../dashboard.php" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item small"><a href="index.php" class="text-decoration-none text-muted">Kategori</a></li>
                    <li class="breadcrumb-item small active fw-semibold text-dark" aria-current="page">Edit Kategori</li>
                </ol>
            </nav>

            <h1 class="h4 mb-0 text-dark fw-bold">Edit Kategori</h1>
            <p class="text-muted small mb-0">Kelola data kategori obat-obatan di sini.</p>
        </div>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <?php if (isset($errors['database'])) : ?>
                <div class="alert alert-danger border-0 shadow-sm mb-4 rounded-3" role="alert">
                    <strong>Gagal!</strong> <?= htmlspecialchars($errors['database']); ?>
                </div>
            <?php endif; ?>
            <!-- START: form untuk mengubah kategori -->
            <form action="" method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Kategori</label>
                    <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : ''; ?>" id="name" name="name" value="<?= htmlspecialchars($result['name']) ?>" placeholder="Masukkan nama kategori">
                    <?php if (isset($errors['name'])) : ?>
                        <div class="invalid-feedback small"><?= htmlspecialchars($errors['name']); ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea class="form-control <?= isset($errors['description']) ? 'is-invalid' : ''; ?>" id="description" name="description" rows="3" placeholder="Masukkan deskripsi kategori"><?= htmlspecialchars($result['description']) ?></textarea>
                    <?php if (isset($errors['description'])) : ?>
                        <div class="invalid-feedback small"><?= htmlspecialchars($errors['description']); ?></div>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary" name="edit">Ubah Kategori</button>
            </form>
            <!-- END: form untuk mengubah kategori -->
        </div>
    </div>
</main>
<?php include "../includes/footer.php"; ?>