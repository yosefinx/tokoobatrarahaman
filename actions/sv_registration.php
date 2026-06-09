<?php

include '../config/connection.php';
if (!isset($_POST)) {
    header("Location: ../index.php");
    exit();
}
if (isset($_POST['username']) && isset($_POST['full_name']) && isset($_POST['phone_number']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['role'])) {
    $username     = trim($_POST['username']);
    $full_name    = trim($_POST['full_name']);
    $phone_number = (trim($_POST['phone_number']) ?? '');
    $email        = trim($_POST['email']);
    $role         = trim($_POST['role']);
    $password_raw = $_POST['password'];
    $password = md5($password_raw);

    $sql = "INSERT INTO users (username, full_name, phone_number, email, password, role) VALUES ('$username', '$full_name', '$phone_number', '$email', '$password', '$role')";
    if (mysqli_query($conn, $sql)) {
        header("Location: ../login.php?success=1");
        exit();
    } else {
        header("Location: ../registration.php?error=1");
        exit();
    }
} else {
    header("Location: ../registration.php?error=1");
    exit();
}
