<?php
include "../middleware/admin_security.php";
include "../../config/connection.php";

$id = $_GET['id'] ?? '';

if ($id == '') {
    header("Location: index.php");
    exit;
}

$sql = "DELETE FROM categories WHERE id = $id";
$query = mysqli_query($conn, $sql);

header("Location: index.php?success-delete=1");
exit;
