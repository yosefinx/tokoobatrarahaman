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

// START: melakukan query untuk menghapus kategori berdasarkan id
$sql = "DELETE FROM categories WHERE id = $id";
$query = mysqli_query($conn, $sql);
// END: melakukan query untuk menghapus kategori berdasarkan id

header("Location: index.php?success-delete=1");
exit;
