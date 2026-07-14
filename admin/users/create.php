<?php
$page = 'users';

include "../middleware/admin_security.php";
include "../../config/connection.php";

include "../includes/header.php";
include "../includes/sidebar.php";
?>
<?php
$errors = [];

if (isset($_POST['create'])) {
    $username     = trim($_POST['username']);
    $full_name    = trim($_POST['full_name']);
    $email        = trim($_POST['email']);
    $password     = trim($_POST['password']);
    $phone_number = trim($_POST['phone_number']);

    // START: validasi input
    if (empty($username)) {
        $errors['username'] = "Nama pengguna wajib diisi.";
    } elseif (strlen($username) < 3) {
        $errors['username'] = "Nama pengguna minimal 3 karakter.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors['username'] = "Nama pengguna hanya boleh huruf, angka, dan underscore.";
    } else {
        $check = mysqli_query($conn, "SELECT id FROM users WHERE username='$username'");
        if (mysqli_num_rows($check) > 0) {
            $errors['username'] = "Nama pengguna sudah digunakan.";
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
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
        if (mysqli_num_rows($check) > 0) {
            $errors['email'] = "Email sudah digunakan.";
        }
    }

    if (empty($password)) {
        $errors['password'] = "Kata sandi wajib diisi.";
    } elseif (strlen($password) < 8) {
        $errors['password'] = "Kata sandi minimal 8 karakter.";
    }

    if (empty($phone_number)) {
        $errors['phone_number'] = "Nomor telepon wajib diisi.";
    } elseif (!preg_match('/^08[0-9]{8,13}$/', $phone_number)) {
        $errors['phone_number'] = "Nomor telepon harus diawali dengan 08 dan terdiri dari 10-15 digit.";
    }
    // END: validasi input

    // START: menyimpan data admin baru ke database jika tidak ada error
    if (empty($errors)) {
        $password = md5($password);
        $sql = "INSERT INTO users (username, full_name, email, password, phone_number, role)
                VALUES ('$username', '$full_name', '$email', '$password', '$phone_number', 'Admin')";
        if (mysqli_query($conn, $sql)) {
            echo "<script>window.location.href='index.php?success-create=1';</script>";
            exit;
        } else {
            $errors['database'] = "Gagal menambahkan admin: " . mysqli_error($conn);
        }
    }
    // END: menyimpan data admin baru ke database jika tidak ada error
}
?>
<main class="flex-grow-1 p-3 p-md-4 bg-light">
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

            <h1 class="h4 mb-0 text-dark fw-bold">Tambah Admin</h1>
            <p class="text-muted small mb-0">Kelola data admin obat-obatan di sini.</p>
        </div>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <?php if (isset($errors['database'])) : ?>
                <div class="alert alert-danger border-0 shadow-sm mb-4 rounded-3" role="alert">
                    <strong>Gagal!</strong> <?= $errors['database']; ?>
                </div>
            <?php endif; ?>
            <!-- START: form untuk menambahkan admin baru -->
            <form action="" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Nama Pengguna</label>
                    <input type="text" class="form-control <?= isset($errors['username']) ? 'is-invalid' : ''; ?>" id="username" name="username" placeholder="Masukkan nama pengguna" value="<?= htmlspecialchars($username ?? '') ?>">
                    <?php if (isset($errors['username'])) : ?>
                        <div class="invalid-feedback small"><?= htmlspecialchars($errors['username']); ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="full_name" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : ''; ?>" id="full_name" name="full_name" placeholder="Masukkan nama admin" value="<?= htmlspecialchars($full_name ?? '') ?>">
                    <?php if (isset($errors['full_name'])) : ?>
                        <div class="invalid-feedback small"><?= htmlspecialchars($errors['full_name']); ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : ''; ?>" id="email" name="email" placeholder="Masukkan nama lengkap admin" value="<?= htmlspecialchars($email ?? '') ?>">
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
                    <input type="number" class="form-control <?= isset($errors['phone_number']) ? 'is-invalid' : ''; ?>" id="phone_number" name="phone_number" placeholder="Masukkan nomor telepon" value="<?= htmlspecialchars($phone_number ?? '') ?>">
                    <?php if (isset($errors['phone_number'])) : ?>
                        <div class="invalid-feedback small"><?= htmlspecialchars($errors['phone_number']); ?></div>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary" name="create">Tambah Data Admin</button>
            </form>
            <!-- END: form untuk menambahkan admin baru -->
        </div>
    </div>
</main>
<?php include "../includes/footer.php"; ?>