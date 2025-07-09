<?php
session_start();
include '../includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get cart items
$result = $conn->query("SELECT b.title, b.price, c.quantity, (b.price * c.quantity) AS item_total
                        FROM cart c
                        JOIN books b ON c.book_id = b.id
                        WHERE c.user_id = $user_id");

$cart_items = [];
$total_price = 0;

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $total_price += $row['item_total'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <h2>Checkout</h2>

    <?php if (count($cart_items) === 0): ?>
        <p>Your cart is empty. <a href="cart.php">Go back</a></p>
    <?php else: ?>

        <h3>Your Cart Items:</h3>
        <ul>
            <?php foreach ($cart_items as $item): ?>
                <li>
                    <?= htmlspecialchars($item['title']) ?> —
                    ₹<?= number_format($item['price'], 2) ?> × <?= $item['quantity'] ?> = 
                    ₹<?= number_format($item['item_total'], 2) ?>
                </li>
            <?php endforeach; ?>
        </ul>

        <p><strong>Total Amount: ₹<?= number_format($total_price, 2) ?></strong></p>

        <form method="POST" action="place_order.php">
            <label>Select Payment Method:</label><br>
            <select name="payment_method" required>
                <option value="">-- Choose --</option>
                <option value="Cash on Delivery">Cash on Delivery</option>
                <option value="UPI">UPI</option>
                <option value="Card">Credit/Debit Card</option>
                <option value="NetBanking">Net Banking</option>
            </select><br><br>

            <label for="address">Delivery Address:</label><br>
            <textarea name="address" rows="4" cols="50" required></textarea><br><br>

            <input type="hidden" name="total_price" value="<?= $total_price ?>">

            <button type="submit">Place Order</button>
        </form>

    <?php endif; ?>

</body>
</html>
