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
    header("Location: ../admin/dashboard.php");
    exit;
} else {
    header("Location: ../login.php");
    exit;
}
