<?php
// index.php
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foto Kiosk</title>

    <link rel="stylesheet" href="css/index.css">
</head>
<body>

<div class="wrapper">

    <button class="kill-button" onclick="killKiosk()">
        Klik op start
    </button>

    <button class="start-button" onclick="startKiosk()">
        <a href="photo.php">START</a>
    </button>

</div>

<script>
    function startKiosk() {
        alert('Foto kiosk gestart');
        //photo.php
    }

    function killKiosk() {
        if(confirm('Weet je zeker dat je de kiosk wil laten starten?')) {
            alert('Klik op start');
        }
    }
</script>

</body>
</html>