<?php
include '../includes/db_connect.php';
session_start();

$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Cart - Empty</title>
        <style>
            .empty-cart-box {
                font-family: 'Segoe UI', sans-serif;
                text-align: center;
                margin: 80px auto;
                padding: 40px;
                max-width: 500px;
                background-color: #fff;
                border: 1px solid #ddd;
                border-radius: 10px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            }
            .empty-cart-box h3 {
                color: #dc3545;
                font-size: 24px;
                margin-bottom: 20px;
            }
            .empty-cart-box a {
                display: inline-block;
                text-decoration: none;
                padding: 10px 20px;
                background-color: #007bff;
                color: white;
                border-radius: 5px;
                transition: background-color 0.3s ease;
            }
            .empty-cart-box a:hover {
                background-color: #0056b3;
            }
        </style>
    </head>
    <body>
        <div class="empty-cart-box">
            <h3>🛒 Your cart is empty.</h3>
            <a href="index.php">← Back to Books</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Get book details for cart items
$ids = implode(',', array_keys($cart));
$result = $conn->query("SELECT * FROM books WHERE id IN ($ids)");
$books = [];
while ($row = $result->fetch_assoc()) {
    $books[$row['id']] = $row;
}
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
            padding: 6px 10px;
            background: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 4px;
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
            <th colspan="3" style="text-align: right;">Total (Book Price Only)</th>
            <th colspan="2">₹<?= number_format($total, 2) ?></th>
        </tr>
    </table>

    <?php $_SESSION['book_total'] = $total; ?>

    <br>
    <button type="submit" class="button">🔄 Update Cart</button>
    </form>

    <br>
    <a class="button" href="checkout.php">🧾 Proceed to Checkout</a> 
</body>
</html>
