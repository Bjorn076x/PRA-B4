<?php

// Functie: tijd sinds foto
function timeAgoString($timestamp) {
    $now = time();
    $diff = $now - $timestamp;

    $hours = floor($diff / 3600);
    $minutes = floor(($diff % 3600) / 60);
    $seconds = $diff % 60;

    return "$hours uur, $minutes minuten en $seconds seconden geleden";
}

// Nederlandse dagen
$daysNL = [
    "Monday" => "Maandag",
    "Tuesday" => "Dinsdag",
    "Wednesday" => "Woensdag",
    "Thursday" => "Donderdag",
    "Friday" => "Vrijdag",
    "Saturday" => "Zaterdag",
    "Sunday" => "Zondag"
];

$baseFolder = "foto";
$tenMinutesAgo = time() - 600;
$photos = [];

$folders = array_filter(glob($baseFolder . '/*'), 'is_dir');

foreach ($folders as $folder) {

    foreach (glob($folder . "/*.{jpg,jpeg,png,JPG,JPEG,PNG}", GLOB_BRACE) as $file) {

        $filename = basename($file);

        // Tijd uit bestandsnaam halen: HH_MM_SS
        if (preg_match('/(\d{2})_(\d{2})_(\d{2})/', $filename, $match)) {

            $hour = intval($match[1]);
            $minute = intval($match[2]);
            $second = intval($match[3]);

            // Timestamp van vandaag
            $timestamp = strtotime(date("Y-m-d") . " $hour:$minute:$second");

            // Alleen foto's van de laatste 10 minuten
            if ($timestamp >= $tenMinutesAgo) {
                $photos[] = [
                    "path" => $file,
                    "time" => $timestamp
                ];
            }
        }
    }
}

usort($photos, function($a, $b) {
    return $b["time"] - $a["time"];
});
?>

<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<title>Achtbaan Foto's</title>
<link rel="stylesheet" href="css/photo.css">
</head>
<body>

<h2>📸 Foto’s van de afgelopen 10 minuten</h2>

<div class="grid">
<?php foreach ($photos as $p): 
    $timestamp = $p["time"];
    $day = date("l", $timestamp);
    $dayNL = $daysNL[$day];
    $timeAgo = timeAgoString($timestamp);
?>
    <div class="photoItem">
        <img src="<?php echo $p['path']; ?>" 
             onclick="openPopup('<?php echo $p['path']; ?>')">

        <p class="timeLabel">
            <?php echo "$dayNL – $timeAgo"; ?>
        </p>
    </div>
<?php endforeach; ?>
</div>

<div id="popup">
    <div id="popupContent">
        <img id="popupImage">
        <br><br>
        <button onclick="addToCart()">🛒 In winkelmandje</button>
        <br><br>
        <button onclick="closePopup()">Sluiten</button>
    </div>
</div>

<script>
let selectedPhoto = null;

function openPopup(src) {
    selectedPhoto = src;
    document.getElementById("popupImage").src = src;
    document.getElementById("popup").style.display = "flex";
}

function closePopup() {
    document.getElementById("popup").style.display = "none";
}

function addToCart() {
    window.location.href = "add_cart.php?file=" + encodeURIComponent(selectedPhoto);
}
</script>

</body>
</html>
