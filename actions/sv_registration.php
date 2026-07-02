<?php

include '../config/connection.php';
if (!isset($_POST)) {
    header("Location: ../index.php");
    exit();
}
$errors = [];

if (isset($_POST['username']) && isset($_POST['full_name']) && isset($_POST['phone_number']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['role'])) {
    $username     = trim($_POST['username']);
    $full_name    = trim($_POST['full_name']);
    $phone_number = (trim($_POST['phone_number']) ?? '');
    $email        = trim($_POST['email']);
    $role         = "User";
    $password_raw = $_POST['password'];
    $password = md5($password_raw);

    if (empty($username)) {
        $errors['username'] = "Username wajib diisi.";
    } elseif (strlen($username) < 3) {
        $errors['username'] = "Username minimal 3 karakter.";
    }

    if (empty($full_name)) {
        $errors['full_name'] = "Nama lengkap wajib diisi.";
    } elseif (strlen($full_name) < 3) {
        $errors['full_name'] = "Nama lengkap minimal 3 karakter.";
    }

    if (empty($phone_number)) {
        $errors['phone_number'] = "Nomor HP wajib diisi.";
    } elseif (!preg_match('/^[0-9]{10,15}$/', $phone_number)) {
        $errors['phone_number'] = "Nomor HP harus terdiri dari 10-15 digit angka.";
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

    if (empty($errors)) {
        $sql = "INSERT INTO users (username, full_name, phone_number, email, password, role) VALUES ('$username', '$full_name', '$phone_number', '$email', '$password', '$role')";
        if (mysqli_query($conn, $sql)) {

            header("Location: ../login.php?success=1");
            exit();
        } else {
            $errors['database'] = "Gagal menyimpan data.";
        }
    }
} else {
    header("Location: ../registration.php?error=1");
    exit();
}
