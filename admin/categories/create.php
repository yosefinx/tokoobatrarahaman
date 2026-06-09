<?php
$page = 'categories';

include "../middleware/security.php";
include "../../config/connection.php";

include "../includes/header.php";
include "../includes/sidebar.php";
?>
<?php
$errors = [];
$name = "";
$description = "";

if (isset($_POST['create'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);

    if (empty($name)) {
        $errors['name'] = "Nama kategori wajib diisi.";
    } elseif (strlen($name) < 3) {
        $errors['name'] = "Nama kategori minimal harus 3 karakter.";
    }

    if (strlen($description) > 255) {
        $errors['description'] = "Deskripsi tidak boleh lebih dari 255 karakter.";
    }

    if (empty($errors)) {
        $query = "INSERT INTO categories (name, description) VALUES ('$name', '$description')";
        if (mysqli_query($conn, $query)) {
            echo "<script>window.location.href='index.php?success=1';</script>";
            exit;
        } else {
            $errors['database'] = "Gagal menyimpan kategori: " . mysqli_error($conn);
        }
    }
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
                    <li class="breadcrumb-item small active fw-semibold text-dark" aria-current="page">Tambah Kategori</li>
                </ol>
            </nav>

            <h1 class="h4 mb-0 text-dark fw-bold">Tambah Kategori</h1>
            <p class="text-muted small mb-0">Kelola data kategori obat-obatan di sini.</p>
        </div>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <?php if (isset($errors['database'])) : ?>
                <div class="alert alert-danger border-0 shadow-sm mb-4 rounded-3" role="alert">
                    <strong>Gagal!</strong> <?= $errors['database']; ?>
                </div>
            <?php endif; ?>
            <form action="" method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Kategori</label>
                    <input type="text"
                        class="form-control <?= isset($errors['name']) ? 'is-invalid' : ''; ?>"
                        id="name"
                        name="name"
                        placeholder="Masukkan nama kategori"
                        value="<?= htmlspecialchars($name); ?>">
                    <?php if (isset($errors['name'])) : ?>
                        <div class="invalid-feedback small"><?= $errors['name']; ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea class="form-control <?= isset($errors['description']) ? 'is-invalid' : ''; ?>"
                        id="description"
                        name="description"
                        rows="3"
                        placeholder="Masukkan deskripsi kategori"><?= htmlspecialchars($description); ?></textarea>
                    <?php if (isset($errors['description'])) : ?>
                        <div class="invalid-feedback small"><?= $errors['description']; ?></div>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary" name="create">Simpan Kategori</button>
            </form>
        </div>
    </div>
</main>
<?php include "../includes/footer.php"; ?>