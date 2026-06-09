<?php
session_start();

$username = $_SESSION['username'];

if ($username == "") {
    header("Location: /toko-obat-arah-aman/login.php");
    exit;
}
