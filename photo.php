<?php
// Database connectie
$conn = new mysqli("localhost", "root", "", "fotokiosk");

// Tijd 10 minuten geleden
$tenMinutesAgo = date("Y-m-d H:i:s", time() - 600);

// Foto’s ophalen
$sql = "SELECT * FROM photos WHERE created_at >= '$tenMinutesAgo' ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<title>Achtbaan Foto's</title>

<style>
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 15px;
}
.grid img {
    width: 100%;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.2s;
}
.grid img:hover {
    transform: scale(1.05);
}

/* Pop-up */
#popup {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.7);
    display: none;
    justify-content: center;
    align-items: center;
}
#popupContent {
    background: white;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
}
#popupContent img {
    max-width: 400px;
    border-radius: 10px;
}
</style>

</head>
<body>

<h2>📸 Foto’s van de afgelopen 10 minuten</h2>

<div class="grid">
<?php while ($row = $result->fetch_assoc()): ?>
    <img src="uploads/<?php echo $row['filename']; ?>" 
         onclick="openPopup('uploads/<?php echo $row['filename']; ?>', <?php echo $row['id']; ?>)">
<?php endwhile; ?>
</div>

<!-- Pop-up -->
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
let selectedPhotoId = null;

function openPopup(src, id) {
    document.getElementById("popupImage").src = src;
    selectedPhotoId = id;
    document.getElementById("popup").style.display = "flex";
}

function closePopup() {
    document.getElementById("popup").style.display = "none";
}

function addToCart() {
    window.location.href = "cart_add.php?id=" + selectedPhotoId;
}
</script>

</body>
</html>
