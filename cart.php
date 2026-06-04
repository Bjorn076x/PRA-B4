<?php
session_start();

$cart = $_SESSION['cart'] ?? [];

// Prijs per foto
$price_per_photo = 2.50;

/* ------------------------------
   1. ITEM VERWIJDEREN
--------------------------------*/
if (isset($_GET['remove'])) {
    $index = (int) $_GET['remove'];

    if (isset($cart[$index])) {
        array_splice($cart, $index, 1);
        $_SESSION['cart'] = $cart;
    }

    header('Location: cart.php');
    exit;
}

/* ------------------------------
   2. HOEVEELHEDEN AANPASSEN
--------------------------------*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quantities'])) {
    foreach ($_POST['quantities'] as $index => $qty) {
        $qty = max(1, (int)$qty);

        if (isset($cart[$index])) {
            if (is_string($cart[$index])) {
                $cart[$index] = [
                    'file' => $cart[$index],
                    'quantity' => $qty
                ];
            } else {
                $cart[$index]['quantity'] = $qty;
            }
        }
    }

    $_SESSION['cart'] = $cart;
    header('Location: cart.php');
    exit;
}

/* ------------------------------
   3. NORMALISEREN (BELANGRIJK!)
--------------------------------*/
foreach ($cart as $i => $item) {
    if (is_string($item)) {
        $cart[$i] = [
            'file' => $item,
            'quantity' => 1
        ];
    } elseif (!isset($cart[$i]['quantity'])) {
        $cart[$i]['quantity'] = 1;
    }
}

// FIX: zet de genormaliseerde cart terug in de sessie
$_SESSION['cart'] = $cart;

/* ------------------------------
   4. TOTAAL BEREKENEN
--------------------------------*/
$total = 0;
foreach ($cart as $item) {
    $qty = $item['quantity'] ?? 1;
    $total += $price_per_photo * $qty;
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jouw winkelmandje</title>
    <link rel="stylesheet" href="css/cart.css">
</head>
<body>
<div class="container">

    <h1>🛒 Jouw winkelmandje</h1>

    <?php if (empty($cart)): ?>

        <div class="empty">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
            </svg>
            <p>Je winkelmandje is leeg.</p>
            <a href="photo.php">← Terug naar de foto's</a>
        </div>

    <?php else: ?>

        <form method="POST" action="cart.php">

            <!-- Foto strip -->
            <div class="photo-strip">
                <?php foreach (array_slice($cart, 0, 3) as $item): ?>
                    <img src="<?= htmlspecialchars($item['file']) ?>" alt="Achtbaan foto">
                <?php endforeach; ?>
            </div>

            <div class="photo-strip-info">
                <h2>Achtbaan foto's — <?= count($cart) ?> foto<?= count($cart) !== 1 ? "'s" : "" ?> geselecteerd</h2>
                <p>Digitale download · Hoge resolutie</p>
            </div>

            <!-- Item rijen -->
            <div class="items-card">
                <?php foreach ($cart as $i => $item): 
                    $src = $item['file'];
                    $qty = $item['quantity'];
                    $label = 'Foto ' . ($i + 1);
                    $line = $price_per_photo * $qty;
                ?>
                <div class="item-row">
                    <img src="<?= htmlspecialchars($src) ?>" alt="<?= $label ?>" class="item-thumb">

                    <div class="item-info">
                        <div class="name"><?= $label ?></div>
                        <div class="unit-price">€<?= number_format($price_per_photo, 2, ',', '.') ?> per stuk</div>
                    </div>

                    <div class="qty-stepper">
                        <button type="button" onclick="changeQty(this, -1)">−</button>
                        <input type="number" name="quantities[<?= $i ?>]"
                               value="<?= $qty ?>" min="1" max="99"
                               onchange="updatePrice(this, <?= $price_per_photo ?>)">
                        <button type="button" onclick="changeQty(this, 1)">+</button>
                    </div>

                    <div class="item-line-price">€<?= number_format($line, 2, ',', '.') ?></div>

                    <a href="cart.php?remove=<?= $i ?>" class="btn-remove"
                       onclick="return confirm('Foto verwijderen uit winkelmandje?')">
                        🗑
                    </a>
                </div>
                <?php endforeach; ?>
            </div>

            <input type="hidden" name="update_cart" value="1">
        </form>

        <!-- Samenvatting -->
        <div class="summary">
            <div class="summary-row">
                <span>Subtotaal (<?= array_sum(array_column($cart, 'quantity')) ?> foto's)</span>
                <span>€<?= number_format($total, 2, ',', '.') ?></span>
            </div>

            <div class="summary-total">
                <span>Totaal</span>
                <span id="grand-total">€<?= number_format($total, 2, ',', '.') ?></span>
            </div>

            <a href="payment.php" class="btn-pay">Betalen →</a>
            <a href="photo.php" class="continue-link">← Verder winkelen</a>
        </div>

    <?php endif; ?>

</div>

<script>
const pricePerPhoto = <?= $price_per_photo ?>;

function changeQty(btn, delta) {
    const input = btn.parentElement.querySelector('input');
    const newVal = Math.max(1, parseInt(input.value) + delta);
    input.value = newVal;
    updatePrice(input, pricePerPhoto);
    autoSave();
}

function updatePrice(input, unitPrice) {
    const row = input.closest('.item-row');
    const qty = parseInt(input.value) || 1;
    const lineEl = row.querySelector('.item-line-price');
    const line = unitPrice * qty;
    lineEl.textContent = '€' + line.toFixed(2).replace('.', ',');
    recalcTotal();
}

function recalcTotal() {
    let total = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const qty = parseInt(row.querySelector('input[type=number]').value) || 1;
        total += pricePerPhoto * qty;
    });
    document.getElementById('grand-total').textContent = '€' + total.toFixed(2).replace('.', ',');
}

let saveTimer;
function autoSave() {
    clearTimeout(saveTimer);
    saveTimer = setTimeout(() => {
        document.querySelector('form').submit();
    }, 800);
}
</script>

</body>
</html>
