<?php
include "config/connection.php";

$errors = [];

$username = "";
$full_name = "";
$phone_number = "";
$email = "";

if (isset($_POST['create'])) {

    $username = trim($_POST['username']);
    $full_name = trim($_POST['full_name']);
    $phone_number = trim($_POST['phone_number']);
    $email = trim($_POST['email']);
    $password_raw = $_POST['password'];
    $role = "User";

    if (empty($username)) {
        $errors['username'] = "Username wajib diisi.";
    } elseif (strlen($username) < 3) {
        $errors['username'] = "Username minimal 3 karakter.";
    }

    if (empty($full_name)) {
        $errors['full_name'] = "Nama lengkap wajib diisi.";
    }

    if (empty($phone_number)) {
        $errors['phone_number'] = "Nomor HP wajib diisi.";
    } elseif (!preg_match('/^[0-9]{10,15}$/', $phone_number)) {
        $errors['phone_number'] = "Nomor HP harus terdiri dari 10-15 digit.";
    }

    if (empty($email)) {
        $errors['email'] = "Email wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Format email tidak valid.";
    }

    if (empty($password_raw)) {
        $errors['password'] = "Password wajib diisi.";
    } elseif (strlen($password_raw) < 8) {
        $errors['password'] = "Password minimal 8 karakter.";
    }

    $cek = mysqli_query($conn, "SELECT id FROM users WHERE username='$username'");
    if (mysqli_num_rows($cek) > 0) {
        $errors['username'] = "Username sudah digunakan.";
    }

    $cek = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
    if (mysqli_num_rows($cek) > 0) {
        $errors['email'] = "Email sudah digunakan.";
    }

    if (empty($errors)) {

        $password = md5($password_raw);

        $sql = "INSERT INTO users
                (username, full_name, phone_number, email, password, role)
                VALUES
                ('$username','$full_name','$phone_number','$email','$password','$role')";

        if (mysqli_query($conn, $sql)) {
            header("Location: login.php?success=1");
            exit();
        } else {
            $errors['database'] = "Gagal menyimpan data.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Obat Arah Aman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
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
            <h2 class="fw-bold text-dark mb-3">Registration</h2>
            <form action="" method="POST">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>" id="username" name="username">
                        <?php if (isset($errors['username'])) : ?>
                            <div class="invalid-feedback small"><?= $errors['username']; ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label for="full_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>" id="full_name" name="full_name">
                        <?php if (isset($errors['full_name'])) : ?>
                            <div class="invalid-feedback small"><?= $errors['full_name']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="phone_number" class="form-label">Phone Number</label>
                    <input type="text" class="form-control <?= isset($errors['phone_number']) ? 'is-invalid' : '' ?>" id="phone_number" name="phone_number">
                    <?php if (isset($errors['phone_number'])) : ?>
                        <div class="invalid-feedback small"><?= $errors['phone_number']; ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" id="email" name="email">
                    <?php if (isset($errors['email'])) : ?>
                        <div class="invalid-feedback small"><?= $errors['email']; ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" id="password" name="password" autocomplete="new-password">
                    <?php if (isset($errors['password'])) : ?>
                        <div class="invalid-feedback small"><?= $errors['password']; ?></div>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>

</html>