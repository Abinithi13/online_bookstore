<?php
session_start();
include '../includes/db_connect.php';
include '../includes/header.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user orders
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Orders</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 40px;
        }
        .orders-container {
            max-width: 1000px;
            margin: auto;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #007BFF;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background: #007BFF;
            color: #fff;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        .order-status {
            padding: 6px 12px;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;.Completed {
    background-color: #28a745;
    color: white;
}

.Failed {
    background-color: #dc3545;
    color: white;
}

.Pending {
    background-color: #ffc107;
    color: black;
}

.Shipped {
    background-color: #17a2b8;
    color: white;
}

.Delivered {
    background-color: #007bff;
    color: white;
}

.Cancelled {
    background-color: #6c757d;
    color: white;
}

 }
        .no-orders {
            text-align: center;
            font-size: 18px;
            color: #555;
            padding: 30px;
        }
    </style>
</head>
<body>

<div class="orders-container">
    <h2>🛒 My Orders</h2>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Order ID</th>
                <th>Order Date</th>
                <th>Books</th>
                <th>Subtotal (Books)</th>
                <th>Final Total</th>
                <th>Status</th>
            </tr>
            <?php while ($order = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($order['id']) ?></td>
                    <td><?= htmlspecialchars($order['order_date']) ?></td>
                    <td>
                        <?php
                        $order_id = $order['id'];
                        $book_query = $conn->prepare("
                            SELECT b.title, b.price, oi.quantity
                            FROM order_items oi 
                            JOIN books b ON oi.book_id = b.id 
                            WHERE oi.order_id = ?
                        ");
                        $book_query->bind_param("i", $order_id);
                        $book_query->execute();
                        $book_result = $book_query->get_result();

                        $titles = [];
                        $subtotal = 0;

                        while ($row = $book_result->fetch_assoc()) {
                            $titles[] = $row['title'] . ' (x' . $row['quantity'] . ')';
                            $subtotal += $row['price'] * $row['quantity'];
                        }

                        echo count($titles) > 0 ? implode(', ', $titles) : 'No books found';
                        ?>
                    </td>
                    <td>₹<?= number_format($subtotal, 2) ?></td>
                    <td>₹<?= number_format($order['total_price'], 2) ?></td>
                    <td>
                        <span class="order-status <?= htmlspecialchars($order['payment_status']) ?>">
                             Payment: <?= htmlspecialchars($order['payment_status']) ?>
                        </span>
                            <br>
<span class="order-status <?= htmlspecialchars($order['order_status']) ?>">
    Order: <?= htmlspecialchars($order['order_status']) ?>
</span>

                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p class="no-orders">You haven’t placed any orders yet.</p>
    <?php endif; ?>
</div>

</body>
</html>
