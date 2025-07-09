<?php
session_start();
include '../includes/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Check if order_id is provided in the URL
if (!isset($_GET['id'])) {
    die("Order ID not specified.");
}

$order_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Verify the order belongs to this user and is still pending AND payment is pending
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ? AND order_status = 'Pending' AND payment_status = 'Pending'");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Invalid order or order cannot be cancelled because it is either paid or already processed.");
}

// Update the order status to Cancelled
$update = $conn->prepare("UPDATE orders SET order_status = 'Cancelled' WHERE id = ?");
$update->bind_param("i", $order_id);
$update->execute();

// Redirect back to orders page with success message
header("Location: orders.php?cancel=success");
exit;
?>
