<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: /toko-obat-arah-aman/login.php");
    exit;
}

if ($_SESSION['role'] !== 'Admin') {
    header("Location: /toko-obat-arah-aman/index.php");
    exit;
}