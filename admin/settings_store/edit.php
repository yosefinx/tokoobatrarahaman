<?php
$page = 'settings_store';

include "../middleware/admin_security.php";
include "../../config/connection.php";

include "../includes/header.php";
include "../includes/sidebar.php";
?>
<?php
$id = $_GET['id'] ?? '';

if ($id == '') {
    header("Location: index.php");
    exit;
}

$sql = "SELECT * FROM contacts WHERE id = $id";
$query = mysqli_query($conn, $sql);
$result = mysqli_fetch_assoc($query);

if (!$result) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['edit'])) {
    $location = trim($_POST['location']);
    $whatsapp_number = trim($_POST['whatsapp_number']);
    $operational_time = trim($_POST['operational_time']);
    if (empty($location)) {
        $errors['location'] = "Nama lokasi wajib diisi.";
    }

    if (empty($whatsapp_number)) {
        $errors['whatsapp_number'] = "Nomor WhatsApp wajib diisi.";
    }

    if (empty($operational_time)) {
        $errors['operational_time'] = "Waktu operasional wajib diisi.";
    }

    if (empty($errors)) {
        $sql = "UPDATE contacts SET location = '$location', whatsapp_number = '$whatsapp_number', operational_time = '$operational_time' WHERE id = $id";
        if (mysqli_query($conn, $sql)) {
            echo "<script>window.location.href='edit.php?id=$id&success-update=1';</script>";
            exit;
        } else {
            $errors['database'] = "Gagal mengubah informasi toko: " . mysqli_error($conn);
        }
    }
}
?>
<main class="flex-grow-1 p-4 bg-light min-vh-100">
    <div class="d-flex align-items-center gap-3 mb-4">
        <div class="d-flex flex-column">
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item small"><a href="../dashboard.php" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item small active fw-semibold text-dark" aria-current="page">Edit Informasi Toko</li>
                </ol>
            </nav>

            <h1 class="h4 mb-0 text-dark fw-bold">Edit Informasi Toko</h1>
            <p class="text-muted small mb-0">Kelola data informasi toko di sini.</p>
        </div>
    </div>
    <?php if (isset($_GET['success-update']) && $_GET['success-update'] == '1') : ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3" role="alert">
                <strong>Berhasil!</strong> Informasi toko berhasil diubah.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <?php if (isset($errors['database'])) : ?>
                <div class="alert alert-danger border-0 shadow-sm mb-4 rounded-3" role="alert">
                    <strong>Gagal!</strong> <?= $errors['database']; ?>
                </div>
            <?php endif; ?>
            <form action="edit.php?id=<?= $id ?>" method="POST">
                <input type="hidden" name="id" value="<?= $id ?>">
                <div class="mb-3">
                    <label for="location" class="form-label">Lokasi</label>
                    <textarea class="form-control <?= isset($errors['location']) ? 'is-invalid' : ''; ?>" id="location" name="location" rows="3" placeholder="Masukkan lokasi toko"><?= htmlspecialchars($result['location']) ?></textarea>
                    <?php if (isset($errors['location'])) : ?>
                        <div class="invalid-feedback small"><?= $errors['location']; ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="whatsapp_number" class="form-label">Nomor WhatsApp</label>
                    <input type="text" class="form-control <?= isset($errors['whatsapp_number']) ? 'is-invalid' : ''; ?>" id="whatsapp_number" name="whatsapp_number" value="<?= htmlspecialchars($result['whatsapp_number']) ?>" placeholder="Masukkan nomor WhatsApp">
                    <?php if (isset($errors['whatsapp_number'])) : ?>
                        <div class="invalid-feedback small"><?= $errors['whatsapp_number']; ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="operational_time" class="form-label">Waktu Operasional</label>
                    <textarea class="form-control <?= isset($errors['operational_time']) ? 'is-invalid' : ''; ?>" id="operational_time" name="operational_time" rows="3" placeholder="Masukkan waktu operasional"><?= htmlspecialchars($result['operational_time']) ?></textarea>
                    <?php if (isset($errors['operational_time'])) : ?>
                        <div class="invalid-feedback small"><?= $errors['operational_time']; ?></div>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary" name="edit">Ubah Informasi Toko</button>
            </form>
        </div>
    </div>
</main>
<?php include "../includes/footer.php"; ?>