<?php
session_start();
include '../config/connection.php';
$username = trim($_POST['username'] ?? '');
$password_raw = trim($_POST['password'] ?? '');

$_SESSION['old'] = [
    'username' => $username
];

if (empty($username)) {
    $errors['username'] = "Nama pengguna wajib diisi.";
}

if (empty($password_raw)) {
    $errors['password'] = "Kata sandi wajib diisi.";
}

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header("Location: ../login.php");
    exit();
} else {
    $password = md5($password_raw);
}

$sql = "SELECT * from users WHERE username='$username' and password='$password'";
$query = mysqli_query($conn, $sql);

if (mysqli_num_rows($query) > 0) {
    $user = mysqli_fetch_assoc($query);
    $_SESSION['id_user'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    if ($user['role'] == 'Admin') {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../index.php");
    }
    exit;
} else {
    $_SESSION['login_error'] = "Nama pengguna atau kata sandi salah.";
    header("Location: ../login.php");
    exit;
}
