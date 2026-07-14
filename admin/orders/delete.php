<?php
include "../middleware/admin_security.php";
include "../../config/connection.php";

$id = $_GET['id'] ?? '';

// START: jika id kosong, redirect ke halaman index.php
if ($id == '') {
    header("Location: index.php");
    exit;
}
// END: jika id kosong, redirect ke halaman index.php

// START: melakukan query untuk menghapus order dan order details berdasarkan id
$sql = "DELETE orders, orders_details 
FROM orders
LEFT JOIN orders_details ON orders.id = orders_details.id_order
WHERE orders.id = $id";
$query = mysqli_query($conn, $sql);

header("Location: index.php?success-delete=1");
exit;
// END: melakukan query untuk menghapus order dan order details berdasarkan id
