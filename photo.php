<?php
date_default_timezone_set('Europe/Amsterdam');

//
// ======================================================
// 1. DEMO-FOTO GENERATOR (automatisch bij elke refresh)
// ======================================================
//

$bronMap = "foto/4_Donderdag";

// Verwijder oude demo-foto's
foreach (glob($bronMap . "/*_demo_*.jpg") as $oud) {
    unlink($oud);
}

// Haal originele foto's op (geen demo's)
$allefotos = array_filter(
    glob($bronMap . "/*.{jpg,jpeg,png,JPG,JPEG,PNG}", GLOB_BRACE),
    fn($f) => strpos(basename($f), '_demo_') === false
);

shuffle($allefotos);
$selectie = array_slice(array_values($allefotos), 0, 10);

$now = time();

foreach ($selectie as $i => $bronBestand) {
    $secGeleden = $i * 54;
    $tijd = $now - $secGeleden;

    $uur  = date('H', $tijd);
    $min  = date('i', $tijd);
    $sec  = date('s', $tijd);
    $id   = rand(1000, 9999);

    $nieuweNaam = "{$bronMap}/{$uur}_{$min}_{$sec}_demo_id{$id}.jpg";
    copy($bronBestand, $nieuweNaam);
}

//
// ======================================================
// 2. FILTERS INLADEN
// ======================================================
//

$filterDag = $_GET['dag'] ?? "";
$filterUur = $_GET['uur'] ?? "";

//
// ======================================================
// 3. FOTO'S INLADEN
// ======================================================
//

$baseFolder = "foto";
$folders    = glob($baseFolder . "/*", GLOB_ONLYDIR);

$photos = [];
$tenMinutesAgo = time() - 600;

foreach ($folders as $folder) {

    // Filter op dagmap
    if ($filterDag !== "" && basename($folder) !== $filterDag) {
        continue;
    }

    foreach (glob($folder . "/*.{jpg,jpeg,png,JPG,JPEG,PNG}", GLOB_BRACE) as $file) {

        $fileTime = filemtime($file);

        // Alleen laatste 10 minuten als GEEN filter is gekozen
        if ($filterDag === "" && $fileTime < $tenMinutesAgo) {
            continue;
        }

        $filename = basename($file);

        if (preg_match('/(\d{2})_(\d{2})_(\d{2})/', $filename, $match)) {
            $hour   = intval($match[1]);
            $minute = intval($match[2]);
            $second = intval($match[3]);

            // Filter op uur
            if ($filterUur !== "" && $hour != intval($filterUur)) {
                continue;
            }

            $photos[] = [
                "path"   => $file,
                "hour"   => $hour,
                "minute" => $minute,
                "second" => $second
            ];
        }
    }
}

usort($photos, function($a, $b) {
    $secA = $a["hour"] * 3600 + $a["minute"] * 60 + $a["second"];
    $secB = $b["hour"] * 3600 + $b["minute"] * 60 + $b["second"];
    return $secB - $secA;
});

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
        <p class="subhead">
            <?php 
            if ($filterDag === "") {
                echo $vandaag . "  foto's van de afgelopen 10 minuten";
            } else {
                echo "Gefilterd op: " . htmlspecialchars($filterDag);
                if ($filterUur !== "") echo " — " . str_pad($filterUur,2,"0",STR_PAD_LEFT) . ":00";
            }
            ?>
        </p>
    </div>
</header>

<!-- FILTERBALK -->
<form method="GET" class="filter-bar">

    <div class="filter-group">
        <label for="dag">Dag</label>
        <select name="dag" id="dag">
            <option value="">Laatste 10 minuten</option>
            <?php foreach (glob("foto/*", GLOB_ONLYDIR) as $f): ?>
                <?php $folderName = basename($f); ?>
                <option value="<?php echo $folderName; ?>" 
                    <?php if ($filterDag === $folderName) echo "selected"; ?>>
                    <?php echo $folderName; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="filter-group">
        <label for="uur">Uur</label>
        <select name="uur" id="uur">
            <option value="">Alles</option>
            <?php for ($i=0; $i<24; $i++): ?>
                <option value="<?php echo $i; ?>" 
                    <?php if ($filterUur !== "" && intval($filterUur) === $i) echo "selected"; ?>>
                    <?php echo str_pad($i,2,"0",STR_PAD_LEFT); ?>:00
                </option>
            <?php endfor; ?>
        </select>
    </div>

    <button class="filter-btn" type="submit">Filteren</button>

</form>


<main>

    <?php if (empty($photos)): ?>
        <div class="empty-state">
            <span class="empty-icon">🎢</span>
            <p>Geen foto's gevonden voor deze selectie.</p>
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
