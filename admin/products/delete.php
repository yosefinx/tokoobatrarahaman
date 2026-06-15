<?php
include "../middleware/admin_security.php";
include "../../config/connection.php";

$id = $_GET['id'] ?? '';

if ($id == '') {
    header("Location: index.php");
    exit;
}

$sql_select = "SELECT photo FROM products WHERE id = $id";
$query_select = mysqli_query($conn, $sql_select);
$product = mysqli_fetch_assoc($query_select);

if ($product) {
    $photo_name = $product['photo'];

    $file_path = '../../images/products/' . $photo_name;

    $sql_delete = "DELETE FROM products WHERE id = $id";

    if (mysqli_query($conn, $sql_delete)) {
        if (!empty($photo_name) && file_exists($file_path)) {
            unlink($file_path);
        }
        header("Location: index.php?success-delete=1");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
