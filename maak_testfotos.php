<?php
date_default_timezone_set('Europe/Amsterdam');

$bronMap = "foto/4_Donderdag";

// Verwijder eerst alle eerder aangemaakte demo-kopieën (herkenbaar aan _demo_ in naam)
foreach (glob($bronMap . "/*_demo_*.jpg") as $oud) {
    unlink($oud);
}

// Haal 10 willekeurige originele foto's op (geen demo-kopieën)
$allefotos = array_filter(
    glob($bronMap . "/*.{jpg,jpeg,png,JPG,JPEG,PNG}", GLOB_BRACE),
    fn($f) => strpos(basename($f), '_demo_') === false
);
shuffle($allefotos);
$selectie = array_slice(array_values($allefotos), 0, 10);

$now = time();

foreach ($selectie as $i => $bronBestand) {
    // Gespreid over de laatste 9 minuten
    $secGeleden = $i * 54;
    $tijd = $now - $secGeleden;

    $uur  = date('H', $tijd);
    $min  = date('i', $tijd);
    $sec  = date('s', $tijd);
    $id   = rand(1000, 9999);

    $nieuweNaam = "{$bronMap}/{$uur}_{$min}_{$sec}_demo_id{$id}.jpg";
    copy($bronBestand, $nieuweNaam);
    echo "✅ Aangemaakt: " . basename($nieuweNaam) . "<br>";
}

echo "<br><strong>Klaar! <a href='photo.php'>Ga naar photo.php</a></strong>";
?>