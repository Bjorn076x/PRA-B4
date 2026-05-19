<?php
session_start();

$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    echo "<h2>Je winkelmandje is leeg</h2>";
    exit;
}
?>

<h2>🛒 Jouw winkelmandje</h2>

<?php foreach ($cart as $file): ?>
    <img src="<?php echo $file; ?>" width="200" style="margin:10px;">
<?php endforeach; ?>
