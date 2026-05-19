<?php
session_start();

if (!isset($_GET['file'])) {
    header("Location: photo.php");
    exit;
}

$file = $_GET['file'];

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$_SESSION['cart'][] = $file;

header("Location: cart.php");
exit;
