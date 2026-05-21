<?php
$conn = new mysqli("localhost", "root", "", "fotokiosk");

if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}

$results = [];
$searched = false;

$results = [];
$searched = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datum = $_POST['datum'] ?? '';
    $tijd = $_POST['tijd'] ?? '';
    $searched = true;

    $stmt = $conn->prepare("SELECT * FROM fotos WHERE datum = ? AND tijd = ?");
    $stmt->bind_param("ss", $datum, $tijd);
    $stmt->execute();
    $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Foto Vergeten</title>
    <link rel="stylesheet" href="css/photoforget.css">
</head>
<body>
    <div class="container">
        <h1>Foto vergeten?</h1>
        <form method="POST">
            <div class="form-group">
                <label for="datum">Datum</label>
                <select name="datum" id="datum" required>
                    <option value="">-- Kies een dag --</option>
                    <option value="maandag">Maandag</option>
                    <option value="dinsdag">Dinsdag</option>
                    <option value="woensdag">Woensdag</option>
                    <option value="donderdag">Donderdag</option>
                    <option value="vrijdag">Vrijdag</option>
                    <option value="zaterdag">Zaterdag</option>
                    <option value="zondag">Zondag</option>
                </select>
            </div>

            <div class="form-group">
                <label for="tijd">Tijd</label>
                <select name="tijd" id="tijd" required>
                    <option value="">-- Kies een tijdslot --</option>
                    <option value="10:00-11:00">10:00 - 11:00</option>
                    <option value="11:00-12:00">11:00 - 12:00</option>
                    <option value="12:00-13:00">12:00 - 13:00</option>
                    <option value="13:00-14:00">13:00 - 14:00</option>
                    <option value="14:00-15:00">14:00 - 15:00</option>
                    <option value="15:00-16:00">15:00 - 16:00</option>
                    <option value="16:00-17:00">16:00 - 17:00</option>
                    <option value="17:00-18:00">17:00 - 18:00</option>
                    <option value="18:00-19:00">18:00 - 19:00</option>
                    <option value="19:00-20:00">19:00 - 20:00</option>
                </select>
            </div>

            <button type="submit" class="btn-zoek">Zoek</button>
        </form>

        <?php if ($searched): ?>
            <div class="results">
                <?php if (count($results) > 0): ?>
                    <h2>Gevonden foto's</h2>
                    <div class="foto-grid">
                        <?php foreach ($results as $row): ?>
                            <div class="foto-card">
                                <img src="foto/<?= htmlspecialchars($row['bestandsnaam']) ?>" alt="Foto">
                                <p><?= htmlspecialchars($row['datum']) ?> | <?= htmlspecialchars($row['tijd']) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="geen-resultaat">Geen foto's gevonden voor deze dag en tijd.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>