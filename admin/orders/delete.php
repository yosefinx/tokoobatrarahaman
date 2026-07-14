<?php
include "../middleware/admin_security.php";
include "../../config/connection.php";

$id = $_GET['id'] ?? '';

if ($id == '') {
    header("Location: index.php");
    exit;
}

$sql = "DELETE orders, orders_details 
FROM orders
LEFT JOIN orders_details ON orders.id = orders_details.id_order
WHERE orders.id = $id";
$query = mysqli_query($conn, $sql);

header("Location: index.php?success-delete=1");
exit;
