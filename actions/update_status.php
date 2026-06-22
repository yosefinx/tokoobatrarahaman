<?php
session_start();
include '../config/connection.php';
$id_admin_login = $_SESSION['id_user'];
$order_code = $_POST['order_code'];
$action = $_POST['status_baru'];

if ($action == 'Diproses') {
    $query = "UPDATE orders SET status = '1', followed_up_by = '$id_admin_login', followed_up_at = NOW() WHERE order_code = '$order_code'";
} elseif ($action == 'Dibatalkan') {
    $query = "UPDATE orders SET status = '0', followed_up_by = '$id_admin_login', followed_up_at = NOW() WHERE order_code = '$order_code'";
} elseif ($action == 'Selesai') {
    $query = "UPDATE orders SET status = '2',  followed_up_by = '$id_admin_login', followed_up_at = NOW() WHERE order_code = '$order_code'";
}

mysqli_query($conn, $query);

header("Location: ../admin/orders/index.php");
exit;
