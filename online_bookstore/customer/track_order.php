<?php
include '../includes/db_connect.php';
session_start();
include '../includes/header.php';
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$order_id = $_GET['order_id'] ?? 0;
$user_id = $_SESSION['user_id'];

// Fetch order details
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? AND id = ?");
$stmt->bind_param("ii", $user_id, $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    echo "Order not found.";
    exit;
}

// Fetch order items
$order_books = $conn->query("SELECT b.title, oi.quantity FROM order_items oi JOIN books b ON oi.book_id = b.id WHERE oi.order_id = $order_id");

$book_titles = [];
while ($book = $order_books->fetch_assoc()) {
    $book_titles[] = "{$book['title']} (x{$book['quantity']})";
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Track Order</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 40px;
        }
        .order-container {
            max-width: 800px;
            margin: auto;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #007BFF;
            text-align: center;
        }
        .order-details {
            margin-bottom: 20px;
        }
        .order-status {
            padding: 6px 12px;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
            margin-top: 10px;
        }
        .Completed {
            background-color: #28a745;
            color: white;
        }
        .Pending {
            background-color: #ffc107;
            color: black;
        }
        .Cancelled {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>

<div class="order-container">
    <h2>Track Your Order</h2>
    <div class="order-details">
        <h4>Order ID: <?= $order['id'] ?></h4>
        <p><strong>Books:</strong> <?= implode(', ', $book_titles) ?></p>
        <p><strong>Total Price:</strong> ₹<?= number_format($order['total_price'], 2) ?></p>
        <p><strong>Status:</strong>
            <span class="order-status <?= htmlspecialchars($order['status']) ?>">
                <?= htmlspecialchars($order['status']) ?>
            </span>
        </p>
    </div>

    <?php if ($order['status'] == 'Pending'): ?>
        <p>Your order is currently being processed. We will notify you once it's shipped.</p>
    <?php elseif ($order['status'] == 'Completed'): ?>
        <p>Your order has been delivered. Thank you for shopping with us!</p>
    <?php elseif ($order['status'] == 'Cancelled'): ?>
        <p>Your order has been cancelled. If this is a mistake, please contact us.</p>
    <?php endif; ?>
</div>

</body>
</html>
