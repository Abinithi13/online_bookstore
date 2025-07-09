<?php
session_start();
include '../includes/db_connect.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    echo "Your cart is empty. <a href='index.php'>Go back to books</a>";
    exit;
}

$ids = implode(',', array_map('intval', array_keys($cart)));
$sql = "SELECT id, price, title FROM books WHERE id IN ($ids)";
$result = $conn->query($sql);

$books = [];
while ($row = $result->fetch_assoc()) {
    $books[$row['id']] = $row;
}

$subtotal_price = 0;
foreach ($cart as $book_id => $qty) {
    if (isset($books[$book_id])) {
        $subtotal_price += $books[$book_id]['price'] * $qty;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Checkout - Payment</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <style>
        body { font-family: Arial, sans-serif; background: #f2f2f2; padding: 30px; }
        .cart-box { max-width: 600px; margin: auto; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2, h3 { color: #333; }
        select, button, input, textarea { padding: 10px; margin-top: 10px; width: 100%; font-size: 16px; border-radius: 5px; box-sizing: border-box; }
        button { background: #007BFF; color: white; border: none; cursor: pointer; transition: background-color 0.3s ease; }
        button:hover { background: #0056b3; }
        .extra-options { display: none; margin-top: 20px; background: #f9f9f9; padding: 15px; border-radius: 10px; }
        .extra-options img { width: 200px; }
        .price-display { font-size: 18px; margin-bottom: 15px; }
        label { font-weight: bold; margin-top: 15px; display: block; }
    </style>
</head>
<body>

<div class="cart-box">
    <h2>🛒 Checkout</h2>

    <p class="price-display">Subtotal (Book Price): ₹<span id="display_subtotal"><?= number_format($subtotal_price, 2) ?></span></p>
    <p class="price-display">Shipping Charge: ₹<span id="display_shipping">50.00</span></p>
    <p class="price-display"><strong>Total Price: ₹<span id="display_total"><?= number_format($subtotal_price + 50, 2) ?></span></strong></p>

    <form method="POST" action="process_payment.php" id="paymentForm">
        <label for="delivery_address">Delivery Address:</label>
        <textarea id="delivery_address" name="delivery_address" rows="4" required placeholder="Enter your full delivery address here"></textarea>

        <h3>Select Payment Method</h3>
        <select name="payment_method" id="payment_method" required onchange="updatePaymentOptions()">
            <option value="">-- Select --</option>
            <option value="UPI">UPI</option>
            <option value="Credit/Debit Card">Credit/Debit Card</option>
            <option value="NetBanking">NetBanking</option>
            <option value="Cash on Delivery">Cash on Delivery</option>
        </select>

        <!-- UPI Section -->
        <div class="extra-options" id="upi_option">
            <p>Scan the QR Code to Pay:</p>
            <img src="../assets/images/qr_dummy.jpg" alt="UPI QR Code" />
        </div>

        <!-- Card Section -->
        <div class="extra-options" id="card_option">
            <label for="bank">Select Your Bank (for Card):</label>
            <select name="bank_card" id="bank_card">
                <option value="HDFC">HDFC Bank</option>
                <option value="SBI">SBI Bank</option>
                <option value="ICICI">ICICI Bank</option>
                <option value="Axis">Axis Bank</option>
            </select>
        </div>

        <!-- NetBanking Section -->
        <div class="extra-options" id="netbanking_option">
            <label for="netbank">Select Your Bank (for NetBanking):</label>
            <select name="bank_net" id="bank_net">
                <option value="Kotak">Kotak Bank</option>
                <option value="Union">Union Bank</option>
                <option value="BOB">Bank of Baroda</option>
                <option value="PNB">Punjab National Bank</option>
            </select>
        </div>

        <!-- Hidden Inputs -->
        <input type="hidden" name="subtotal_price" id="subtotal_price_input" value="<?= htmlspecialchars($subtotal_price) ?>" />
        <input type="hidden" name="shipping_charge" id="shipping_charge_input" value="50" />
        <input type="hidden" name="total_price" id="total_price_input" value="<?= htmlspecialchars($subtotal_price + 50) ?>" />

        <button type="submit">Proceed to Payment</button>
    </form>
</div>

<script>
function updatePaymentOptions() {
    const method = document.getElementById("payment_method").value;
    const subtotal = parseFloat(document.getElementById("subtotal_price_input").value) || 0;
    const shippingCharge = 50;
    const total = subtotal + shippingCharge;

    // Hide all sections first
    document.getElementById("upi_option").style.display = "none";
    document.getElementById("card_option").style.display = "none";
    document.getElementById("netbanking_option").style.display = "none";

    // Show only selected
    if (method === "UPI") {
        document.getElementById("upi_option").style.display = "block";
    } else if (method === "Credit/Debit Card") {
        document.getElementById("card_option").style.display = "block";
    } else if (method === "NetBanking") {
        document.getElementById("netbanking_option").style.display = "block";
    }

    // Update total and hidden inputs
    document.getElementById("display_shipping").innerText = shippingCharge.toFixed(2);
    document.getElementById("display_total").innerText = total.toFixed(2);
    document.getElementById("shipping_charge_input").value = shippingCharge;
    document.getElementById("total_price_input").value = total.toFixed(2);
}

window.onload = updatePaymentOptions;
</script>

</body>
</html>
