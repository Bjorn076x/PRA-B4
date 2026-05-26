<?php
date_default_timezone_set('Europe/Amsterdam');

// Alle dagmappen in /foto ophalen
$baseFolder = "foto";
$folders    = glob($baseFolder . "/*", GLOB_ONLYDIR);

$photos = [];
$tenMinutesAgo = time() - 600;

// Nederlandse dagnaam voor header
$dagNL = [
    0 => "Zondag",
    1 => "Maandag",
    2 => "Dinsdag",
    3 => "Woensdag",
    4 => "Donderdag",
    5 => "Vrijdag",
    6 => "Zaterdag"
];

$vandaag = $dagNL[(int)date("w")];

// Foto’s ophalen uit ALLE dagmappen
foreach ($folders as $folder) {
    foreach (glob($folder . "/*.{jpg,jpeg,png,JPG,JPEG,PNG}", GLOB_BRACE) as $file) {

        $fileTime = filemtime($file);
        if ($fileTime < $tenMinutesAgo) continue;

        $filename = basename($file);

        // Tijd uit bestandsnaam halen (HH_MM_SS)
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

// Sorteren op tijd (nieuwste eerst)
usort($photos, function($a, $b) {
    $secA = $a["hour"] * 3600 + $a["minute"] * 60 + $a["second"];
    $secB = $b["hour"] * 3600 + $b["minute"] * 60 + $b["second"];
    return $secB - $secA;
});
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Achtbaan Foto's</title>
    <link rel="stylesheet" href="css/photo.css">
</head>
<body>

<header>
    <div class="header-inner">
        <h1>📸 Jouw Rit</h1>
        <p class="subhead"><?php echo $vandaag; ?> — foto's van de afgelopen 10 minuten</p>
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
                    <img src="<?php echo './' . htmlspecialchars($p['path']); ?>"
                         alt="Achtbaanfoto om <?php echo $time; ?>"
                         loading="lazy"
                         onclick="openPopup('<?php echo './' . htmlspecialchars($p['path']); ?>', '<?php echo $time; ?>')">
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

document.addEventListener("keydown", function(e) {
    if (e.key === "Escape") closePopup();
});
</script>

</body>
</html>
