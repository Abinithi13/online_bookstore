<?php
session_start();
include '../includes/db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$order_id = intval($_GET['id']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    
    // Update order status
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $order_id);
    $stmt->execute();

    echo "<h3>✅ Order status updated to: $status</h3>";
    echo "<a href='view_orders.php'>Back to Orders</a>";
    exit;
}

// Get current status
$result = $conn->query("SELECT * FROM orders WHERE id = $order_id");
$order = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Order Status</title>
</head>
<body>

<h2>Update Order Status</h2>
<form method="POST">
    <label for="status">Select Status:</label><br>
    <select name="status" required>
        <option value="Pending" <?= ($order['status'] === 'Pending') ? 'selected' : '' ?>>Pending</option>
        <option value="Completed" <?= ($order['status'] === 'Completed') ? 'selected' : '' ?>>Completed</option>
        <option value="Shipped" <?= ($order['status'] === 'Shipped') ? 'selected' : '' ?>>Shipped</option>
        <option value="Cancelled" <?= ($order['status'] === 'Cancelled') ? 'selected' : '' ?>>Cancelled</option>
    </select><br><br>

    <button type="submit">Update Status</button>
</form>

</body>
</html>
