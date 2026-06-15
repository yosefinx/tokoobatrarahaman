<?php
session_start();
include '../config/connection.php';
$username = $_POST['username'];
$password = md5($_POST['password']);

$sql = "select * from users where username='$username' and password='$password'";
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
    header("Location: ../login.php");
    exit;
}
