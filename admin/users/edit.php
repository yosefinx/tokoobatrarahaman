<?php
$page = 'users';

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

$sql = "SELECT * FROM users WHERE id = $id";
$query = mysqli_query($conn, $sql);
$result = mysqli_fetch_assoc($query);

if (!$result) {
    header("Location: index.php");
    exit;
}
$errors = [];

if (isset($_POST['edit'])) {
    $username     = trim($_POST['username']);
    $full_name    = trim($_POST['full_name']);
    $email        = trim($_POST['email']);
    $password     = trim($_POST['password']);
    $phone_number = trim($_POST['phone_number']);

    // START: validasi input
    if (empty($username)) {
        $errors['username'] = "Username wajib diisi.";
    } elseif (strlen($username) < 3) {
        $errors['username'] = "Username minimal 3 karakter.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors['username'] = "Username hanya boleh huruf, angka, dan underscore.";
    } else {
        $check = mysqli_query($conn, "SELECT id FROM users WHERE username='$username' AND id != $id");
        if (mysqli_num_rows($check) > 0) {
            $errors['username'] = "Username sudah digunakan.";
        }
    }

    if (empty($full_name)) {
        $errors['full_name'] = "Nama lengkap wajib diisi.";
    } elseif (strlen($full_name) < 3) {
        $errors['full_name'] = "Nama lengkap minimal 3 karakter.";
    }

    if (empty($email)) {
        $errors['email'] = "Email wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Format email tidak valid.";
    } else {
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email' AND id != $id");
        if (mysqli_num_rows($check) > 0) {
            $errors['email'] = "Email sudah digunakan.";
        }
    }

    if (!empty($password)) {
        if (strlen($password) < 8) {
            $errors['password'] = "Password minimal 8 karakter.";
        }
    }

    if (empty($phone_number)) {
        $errors['phone_number'] = "Nomor telepon wajib diisi.";
    } elseif (!preg_match('/^[0-9]{10,15}$/', $phone_number)) {
        $errors['phone_number'] = "Nomor telepon harus terdiri dari 10-15 digit.";
    }
    // END: validasi input
    // START: menyimpan perubahan data admin ke database jika tidak ada error
    if (empty($errors)) {
        if (!empty($password)) {
            $password = md5($password);
            $sql = "UPDATE users SET username='$username', full_name='$full_name', email='$email', password='$password',
            phone_number='$phone_number' WHERE id='$id'";
        } else {
            $sql = "UPDATE users SET username='$username', full_name='$full_name', email='$email',
            phone_number='$phone_number' WHERE id='$id'";
        }

        if (mysqli_query($conn, $sql)) {
            echo "<script>window.location.href='index.php?success-update=1';</script>";
            exit;
        } else {
            $errors['database'] = "Gagal mengubah data admin: " . mysqli_error($conn);
        }
    }
    // END: menyimpan perubahan data admin ke database jika tidak ada error
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
                    <li class="breadcrumb-item small"><a href="index.php" class="text-decoration-none text-muted">Admin</a></li>
                    <li class="breadcrumb-item small active fw-semibold text-dark" aria-current="page">Edit Admin</li>
                </ol>
            </nav>

            <h1 class="h4 mb-0 text-dark fw-bold">Edit Admin</h1>
            <p class="text-muted small mb-0">Kelola data admin di sini.</p>
        </div>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <?php if (isset($errors['database'])) : ?>
                <div class="alert alert-danger border-0 shadow-sm mb-4 rounded-3" role="alert">
                    <strong>Gagal!</strong> <?= htmlspecialchars($errors['database']); ?>
                </div>
            <?php endif; ?>
            <!-- START: form untuk mengubah data admin -->
            <form action="" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Nama Pengguna</label>
                    <input type="text" class="form-control <?= isset($errors['username']) ? 'is-invalid' : ''; ?>" id="username" name="username" value="<?= htmlspecialchars($result['username']) ?>" placeholder="Masukkan nama admin">
                    <?php if (isset($errors['username'])) : ?>
                        <div class="invalid-feedback small"><?= htmlspecialchars($errors['username']); ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="full_name" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : ''; ?>" id="full_name" name="full_name" value="<?= htmlspecialchars($result['full_name']) ?>" placeholder="Masukkan lengkap admin">
                    <?php if (isset($errors['full_name'])) : ?>
                        <div class="invalid-feedback small"><?= htmlspecialchars($errors['full_name']); ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?= htmlspecialchars($result['email']) ?>" placeholder="Masukkan nomor telepon">
                    <?php if (isset($errors['email'])) : ?>
                        <div class="invalid-feedback small"><?= htmlspecialchars($errors['email']); ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Kata Sandi</label>
                    <div class="input-group">
                        <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : ''; ?>" id="password" name="password" placeholder="Masukkan kata sandi">
                        <?php if (isset($errors['password'])) : ?>
                            <div class="invalid-feedback small"><?= htmlspecialchars($errors['password']); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="phone_number" class="form-label">Nomor Telepon</label>
                    <input type="number" class="form-control <?= isset($errors['phone_number']) ? 'is-invalid' : ''; ?>" id="phone_number" name="phone_number" value="<?= htmlspecialchars($result['phone_number']) ?>" placeholder="Masukkan nama pengguna">
                    <?php if (isset($errors['phone_number'])) : ?>
                        <div class="invalid-feedback small"><?= htmlspecialchars($errors['phone_number']); ?></div>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary" name="edit">Ubah Data Admin</button>
            </form>
            <!-- END: form untuk mengubah data admin -->
        </div>
    </div>
</main>
<?php include "../includes/footer.php"; ?>