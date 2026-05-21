<?php
date_default_timezone_set('Europe/Amsterdam');

// Dag van vandaag → juiste submap
$nowAMS   = new DateTime('now', new DateTimeZone('Europe/Amsterdam'));
$dayIndex = (int)$nowAMS->format('w');
$dayNames = [
    "0_Zondag",
    "1_Maandag",
    "2_Dinsdag",
    "3_Woensdag",
    "4_Donderdag",
    "5_Vrijdag",
    "6_Zaterdag"
];

$dagNL = [
    "0_Zondag"    => "Zondag",
    "1_Maandag"   => "Maandag",
    "2_Dinsdag"   => "Dinsdag",
    "3_Woensdag"  => "Woensdag",
    "4_Donderdag" => "Donderdag",
    "5_Vrijdag"   => "Vrijdag",
    "6_Zaterdag"  => "Zaterdag"
];

$baseFolder  = "foto";
$todayFolder = $baseFolder . "/" . $dayNames[$dayIndex];
$photos      = [];

$tenMinutesAgo = time() - 600;

// Controleer of de map bestaat
if (is_dir($todayFolder)) {
    foreach (glob($todayFolder . "/*.{jpg,jpeg,png,JPG,JPEG,PNG}", GLOB_BRACE) as $file) {

        $filename  = basename($file);
        $fileTime  = filemtime($file);

        // Alleen foto's waarvan het bestand de laatste 10 minuten op de server is gezet
        if ($fileTime < $tenMinutesAgo) continue;

        // Tijd uit bestandsnaam halen voor weergave
        if (preg_match('/(\d{2})_(\d{2})_(\d{2})/', $filename, $match)) {
            $hour   = intval($match[1]);
            $minute = intval($match[2]);
            $second = intval($match[3]);

            $photos[] = [
                "path"   => $file,
                "hour"   => $hour,
                "minute" => $minute,
                "second" => $second
            ];
        }
    }
}

// Nieuwste foto eerst (op basis van tijd in bestandsnaam)
usort($photos, function($a, $b) {
    $secA = $a["hour"] * 3600 + $a["minute"] * 60 + $a["second"];
    $secB = $b["hour"] * 3600 + $b["minute"] * 60 + $b["second"];
    return $secB - $secA;
});

$vandaag = $dagNL[$dayNames[$dayIndex]];
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Achtbaan Foto's</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/photo.css">
</head>
<body>

<header>
    <div class="header-inner">
        <h1>📸 Jouw Rit</h1>
        <p class="subhead"><?php echo $vandaag; ?> &mdash; foto's van de afgelopen 10 minuten</p>
    </div>
</header>

<main>
    <?php if (empty($photos)): ?>
        <div class="empty-state">
            <span class="empty-icon">🎢</span>
            <p>Nog geen foto's beschikbaar.<br>Rijd een rondje en kom terug!</p>
        </div>
    <?php else: ?>
        <div class="grid">
            <?php foreach ($photos as $index => $p):
                $h    = str_pad($p["hour"],   2, "0", STR_PAD_LEFT);
                $m    = str_pad($p["minute"], 2, "0", STR_PAD_LEFT);
                $s    = str_pad($p["second"], 2, "0", STR_PAD_LEFT);
                $time = "$h:$m:$s";
            ?>
            <div class="photo-card" style="animation-delay: <?php echo $index * 0.07; ?>s">
                <div class="photo-wrap">
                    <img src="<?php echo htmlspecialchars($p['path']); ?>"
                         alt="Achtbaanfoto om <?php echo $time; ?>"
                         loading="lazy"
                         onclick="openPopup('<?php echo htmlspecialchars($p['path']); ?>', '<?php echo $time; ?>')">
                    <div class="photo-overlay">
                        <span>🔍 Bekijken</span>
                    </div>
                </div>
                <div class="photo-meta">
                    <span class="photo-time">🕐 <?php echo $time; ?></span>

                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<!-- Popup -->
<div id="popup" onclick="closePopupOutside(event)">
    <div id="popup-content">
        <button class="close-btn" onclick="closePopup()">✕</button>
        <img id="popup-image" src="" alt="Geselecteerde foto">
        <p id="popup-time"></p>
        <div class="popup-actions">
            <button class="btn-cart" onclick="addToCart()">🛒 In winkelmandje</button>
            <button class="btn-close" onclick="closePopup()">Sluiten</button>
        </div>
    </div>
</div>

<script>
let selectedPhoto = null;

function openPopup(src, timeAgo) {
    selectedPhoto = src;
    document.getElementById("popup-image").src = src;
    document.getElementById("popup-time").textContent = timeAgo;
    document.getElementById("popup").classList.add("active");
    document.body.style.overflow = "hidden";
}

function closePopup() {
    document.getElementById("popup").classList.remove("active");
    document.body.style.overflow = "";
}

function closePopupOutside(e) {
    if (e.target.id === "popup") closePopup();
}

function addToCart() {
    window.location.href = "add_cart.php?file=" + encodeURIComponent(selectedPhoto);
}

// Escape-toets sluit popup
document.addEventListener("keydown", function(e) {
    if (e.key === "Escape") closePopup();
});
</script>

</body>
</html>