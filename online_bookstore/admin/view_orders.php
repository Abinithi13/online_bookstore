<?php
include '../includes/db_connect.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch all orders
$order_result = $conn->query("
    SELECT o.*, u.phone 
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.order_date DESC
");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - View Orders</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 20px;
        }
        h2 {
            margin-bottom: 20px;
            text-align: center;
            color: #333;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }
        th {
            background: #007BFF;
            color: white;
        }
        .status {
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
            text-transform: capitalize;
        }
        .Completed { background-color: #28a745; color: white; }
        .Pending { background-color: #ffc107; color: black; }
        .Cancelled, .Failed { background-color: #dc3545; color: white; }
        .Shipped { background-color: #17a2b8; color: white; }
        .Delivered { background-color: #007bff; color: white; }
    </style>
</head>
<body>

<h2>📦 All Orders</h2>

<?php if ($order_result->num_rows > 0): ?>
    <table>
        <tr>
            <th>Order ID</th>
            <th>User ID</th>
            <th>Books Ordered</th>
            <th>Subtotal</th>
            <th>Shipping</th>
            <th>Total</th>
            <th>Payment Status</th>
            <th>Order Status</th>
            <th>Order Date</th>
            <th>Delivery Address</th>
            <th>Phone Number</th>
        </tr>
        <?php while ($order = $order_result->fetch_assoc()): ?>
            <tr>
                <td><?= $order['id'] ?></td>
                <td><?= $order['user_id'] ?></td>
                <td>
                    <?php
                    $order_id = $order['id'];
                    $book_query = $conn->query("
                        SELECT b.title, oi.quantity 
                        FROM order_items oi 
                        JOIN books b ON b.id = oi.book_id 
                        WHERE oi.order_id = $order_id
                    ");
                    $books = [];
                    while ($book = $book_query->fetch_assoc()) {
                        $books[] = htmlspecialchars($book['title']) . " (x" . $book['quantity'] . ")";
                    }
                    echo implode(', ', $books);
                    ?>
                </td>
                <td>₹<?= number_format($order['total_price'] - $order['shipping_charge'], 2) ?></td>
                <td>₹<?= number_format($order['shipping_charge'], 2) ?></td>
                <td>₹<?= number_format($order['total_price'], 2) ?></td>
                <td><span class="status <?= $order['payment_status'] ?>"><?= $order['payment_status'] ?></span></td>
                <td><span class="status <?= $order['order_status'] ?>"><?= $order['order_status'] ?></span></td>
                <td><?= $order['order_date'] ?></td>
                <td><?= nl2br(htmlspecialchars($order['delivery_address'])) ?></td>
                <td><?= htmlspecialchars($order['phone']) ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>No orders found.</p>
<?php endif; ?>

</body>
</html>
