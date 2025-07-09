<?php
include '../includes/db_connect.php';
session_start();
include '../includes/header.php';
// Get cart from session or empty array
$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    echo "<h3>Your cart is empty.</h3><a href='index.php'>← Back to Books</a>";
    exit;
}

// Get all book details for items in cart
$ids = implode(',', array_map('intval', array_keys($cart)));
$result = $conn->query("SELECT * FROM books WHERE id IN ($ids)");

$books = [];
while ($row = $result->fetch_assoc()) {
    $books[$row['id']] = $row;
}

// Clean up invalid book IDs from cart
foreach ($cart as $id => $qty) {
    if (!isset($books[$id])) {
        unset($_SESSION['cart'][$id]);
    }
}
$cart = $_SESSION['cart'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Cart</title>
    <style>
        body { font-family: Arial; background: #f9f9f9; padding: 20px; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: center; }
        th { background: #eee; }
        .button {
            padding: 6px 12px;
            background: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        .button:hover { background: #0056b3; }
        .danger-button {
            background: #dc3545;
        }
        .danger-button:hover {
            background: #c82333;
        }
    </style>
</head>
<body>
    <h2>Your Shopping Cart</h2>

    <form method="POST" action="update_cart.php">
    <table>
        <tr>
            <th>Book</th>
            <th>Price</th>
            <th>Qty</th>
            <th>Subtotal</th>
            <th>Remove</th>
        </tr>

        <?php
        $total = 0;
        foreach ($cart as $id => $qty):
            if (!isset($books[$id])) continue;
            $book = $books[$id];
            $subtotal = $book['price'] * $qty;
            $total += $subtotal;
        ?>
        <tr>
            <td><?= htmlspecialchars($book['title']) ?></td>
            <td>₹<?= number_format($book['price'], 2) ?></td>
            <td>
                <input type="number" name="quantities[<?= $id ?>]" value="<?= $qty ?>" min="1" style="width: 60px;">
            </td>
            <td>₹<?= number_format($subtotal, 2) ?></td>
            <td><a class="button danger-button" href="remove_from_cart.php?id=<?= $id ?>">Remove</a></td>
        </tr>
        <?php endforeach; ?>

        <tr>
            <th colspan="3">Total</th>
            <th colspan="2">₹<?= number_format($total, 2) ?></th>
        </tr>
    </table>

    <br>
    <button type="submit" class="button">🔄 Update Cart</button>
    </form>

    <br>
    <a class="button" href="checkout.php">🧾 Proceed to Checkout</a>
</body>
</html>
