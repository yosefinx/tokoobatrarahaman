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

$sql_select = "SELECT recipe FROM orders WHERE id = $id";
$query_select = mysqli_query($conn, $sql_select);
$recipe = mysqli_fetch_assoc($query_select);

if ($recipe) {
    $recipe_photo = $recipe['recipe'];
    $file_path = "../../recipe/" . $recipe_photo;

    if (!empty($recipe_photo) && file_exists($file_path)) {
        unlink($file_path);
    }
}

$sql_delete_orders = "DELETE orders, orders_details FROM orders LEFT JOIN orders_details ON orders.id = orders_details.id_order WHERE orders.id = $id";
$query_delete = mysqli_query($conn, $sql_delete_orders);

if ($query_delete) {
    header("Location: index.php?success-delete=1");
    exit;
} else {
    header("Location: index.php");
    exit;
}