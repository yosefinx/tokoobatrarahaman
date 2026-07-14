<?php
session_start();
include "../config/connection.php";

$errors = [];

$username     = trim($_POST['username']);
$full_name    = trim($_POST['full_name']);
$phone_number = trim($_POST['phone_number']);
$email        = trim($_POST['email']);
$password_raw = $_POST['password'];
$role = "User";

$_SESSION['old'] = [
    'username' => $username,
    'full_name' => $full_name,
    'phone_number' => $phone_number,
    'email' => $email
];

if (empty($username)) {
    $errors['username'] = "Nama pengguna wajib diisi.";
} elseif (strlen($username) < 3) {
    $errors['username'] = "Nama pengguna minimal 3 karakter.";
}

if (empty($full_name)) {
    $errors['full_name'] = "Nama lengkap wajib diisi.";
}

if (empty($phone_number)) {
    $errors['phone_number'] = "Nomor telepon wajib diisi.";
} elseif (!preg_match('/^08[0-9]{8,13}$/', $phone_number)) {
    $errors['phone_number'] = "Nomor telepon harus diawali dengan 08 dan terdiri dari 10-15 digit."; // validasi nomor HP harus diawali dengan 08 dan Jumlah angka setelah 08 minimal harus 8 digit dan maksimal 13 digit.
}

if (empty($email)) {
    $errors['email'] = "Email wajib diisi.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = "Format email tidak valid.";
}

if (empty($password_raw)) {
    $errors['password'] = "Kata sandi wajib diisi.";
} elseif (strlen($password_raw) < 8) {
    $errors['password'] = "Kata sandi minimal 8 karakter.";
}

$cek = mysqli_query($conn, "SELECT id FROM users WHERE username='$username'");
if (mysqli_num_rows($cek) > 0) {
    $errors['username'] = "Nama pengguna sudah digunakan.";
}

$cek = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
if (mysqli_num_rows($cek) > 0) {
    $errors['email'] = "Email sudah digunakan.";
}

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header("Location: ../registration.php");
    exit;
}

if (empty($errors)) {
    $password = md5($password_raw);
    $sql = "INSERT INTO users
    (username, full_name, phone_number, email, password, role)
    VALUES
    ('$username','$full_name','$phone_number','$email','$password','$role')";
    if (mysqli_query($conn, $sql)) {
        unset($_SESSION['errors']);
        unset($_SESSION['old']);
        header("Location: ../login.php?success=1");
        exit;
    } else {
        $_SESSION['errors']['database'] = "Gagal mendaftar: " . mysqli_error($conn);
        header("Location: ../registration.php");
        exit;
    }
}
