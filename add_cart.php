<?php
session_start();

if (!isset($_GET['file'])) {
    header("Location: photo.php");
    exit;
}


// CONFIGURATIE

$cartFotoMap = "cart_fotos";

// Maak de map aan als die nog niet bestaat
if (!is_dir($cartFotoMap)) {
    mkdir($cartFotoMap, 0755, true);
}


// PAD OPSCHONEN

$origineel = ltrim($_GET['file'], './');

// Controleer of het bestand bestaat
if (!file_exists($origineel)) {
    header("Location: photo.php?error=bestand_niet_gevonden");
    exit;
}


// KOPIEER NAAR CART MAP
$bestandsnaam  = basename($origineel);
$doelBestand   = $cartFotoMap . '/' . $bestandsnaam;

// Kopieer alleen als het nog niet bestaat
if (!file_exists($doelBestand)) {
    copy($origineel, $doelBestand);
}

// VOEG TOE AAN CART
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$_SESSION['cart'][] = $doelBestand;

header("Location: cart.php");
exit;