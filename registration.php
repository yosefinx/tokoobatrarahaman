<?php
session_start();

$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];

unset($_SESSION['errors']);
unset($_SESSION['old']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Obat Arah Aman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>

<body class="bg-light d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="p-3" style="width: 100%; max-width: 650px;">
        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
            <?php
            if (isset($_GET['error']) && $_GET['error'] == '1') {
            ?>
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3 rounded-3" role="alert">
                    <strong>Registrasi Gagal!</strong> Terjadi kesalahan saat menyimpan data, silakan coba lagi.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php
            }
            ?>
            <div class="d-flex align-items-center mb-4">
                <div class="bg-white rounded-2 d-flex align-items-center justify-content-center text-white me-3" style="width: 40px; height: 40px;">
                    <img src="images/logo/logo.png" alt="Logo" style="width: 40px;">
                </div>
                <div>
                    <div class="fw-bold text-dark lh-sm">Toko Obat Arah Aman</div>
                    <div class="small text-muted">Buat akun untuk masuk ke dashboard utama.</div>
                </div>
            </div>
            <div class="mb-4 overflow-hidden rounded-3 border">
                <img src="images/hero/hero-image.jpeg" class="img-fluid rounded-3 border w-100" style="height: 100px; object-fit: cover;" alt="Dashboard Preview">
            </div>
            <h2 class="fw-bold text-dark mb-3">Registrasi</h2>
            <form action="actions/sv_registration.php" method="POST">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="username" class="form-label">Nama Pengguna</label>
                        <input type="text" class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>" id="username" name="username" value="<?= htmlspecialchars($old['username'] ?? '') ?>">
                        <?php if (isset($errors['username'])) : ?>
                            <div class="invalid-feedback small"><?= $errors['username']; ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label for="full_name" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>" id="full_name" name="full_name" value="<?= htmlspecialchars($old['full_name'] ?? '') ?>">
                        <?php if (isset($errors['full_name'])) : ?>
                            <div class="invalid-feedback small"><?= $errors['full_name']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="phone_number" class="form-label">Nomor Telepon</label>
                    <input type="text" class="form-control <?= isset($errors['phone_number']) ? 'is-invalid' : '' ?>" id="phone_number" name="phone_number" value="<?= htmlspecialchars($old['phone_number'] ?? '') ?>">
                    <?php if (isset($errors['phone_number'])) : ?>
                        <div class="invalid-feedback small"><?= $errors['phone_number']; ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>">
                    <?php if (isset($errors['email'])) : ?>
                        <div class="invalid-feedback small"><?= $errors['email']; ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">Kata Sandi</label>
                    <div class="input-group">
                        <input
                            type="password"
                            class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                            id="password"
                            name="password"
                            autocomplete="new-password">
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                    <?php if (isset($errors['password'])): ?>
                        <div class="text-danger small"><?= $errors['password'] ?></div>
                    <?php endif; ?>
                </div>
                <input type="hidden" name="role">
                <button type="submit" name="create" class="btn btn-primary w-100 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2">
                    Register
                </button>
            </form>
            <div class="text-center mt-4 small text-muted">
                Sudah punya akun? <a href="login.php" class="text-primary text-decoration-none fw-medium">Login di sini</a>
            </div>
        </div>
    </div>
    <script src="js/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>

</html>