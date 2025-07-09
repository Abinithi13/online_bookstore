<?php
session_start();
include '../includes/db_connect.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<h3>❌ Your cart is empty. Please add items before placing an order.</h3>";
    exit;
}

$user_id = $_SESSION['user_id'];
$cart = $_SESSION['cart'];

// Sanitize inputs
$total_price = isset($_POST['total_price']) ? (float)$_POST['total_price'] : 0.00;
$payment_method = $_POST['payment_method'] ?? '';
$shipping_charge = isset($_POST['shipping_charge']) ? (float)$_POST['shipping_charge'] : 0.00;
$delivery_address = trim($_POST['delivery_address'] ?? '');

// Validate payment method
$allowed_methods = ['UPI', 'Credit/Debit Card', 'Cash on Delivery', 'NetBanking'];
if ($total_price <= 0 || empty($payment_method) || !in_array($payment_method, $allowed_methods)) {
    echo "<h3>❌ Invalid payment details. Please go back and try again.</h3>";
    exit;
}

// Validate delivery address
if (empty($delivery_address)) {
    echo "<h3>❌ Delivery address is required. Please go back and enter your address.</h3>";
    exit;
}

// Step 1: Validate stock before placing the order
foreach ($cart as $book_id => $quantity) {
    $stock_check = $conn->prepare("SELECT stock, title FROM books WHERE id = ?");
    $stock_check->bind_param("i", $book_id);
    $stock_check->execute();
    $stock_check->bind_result($available_stock, $book_title);
    $stock_check->fetch();
    $stock_check->close();

    if ($available_stock < $quantity) {
        echo "<h3>❌ Sorry, only $available_stock unit(s) available for \"$book_title\". Please update your cart.</h3>";
        exit;
    }
}

// Set payment status
$payment_status = ($payment_method === 'Cash on Delivery') ? 'Pending' : 'Completed';
$order_status = 'Pending';

// Step 2: Insert order into orders table
$stmt = $conn->prepare("INSERT INTO orders 
    (user_id, total_price, payment_method, payment_status, order_status, shipping_charge, delivery_address, order_date) 
    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");

if ($stmt === false) {
    echo "<h3>❌ Database error: Failed to prepare order statement.</h3>";
    exit;
}

$stmt->bind_param("idsssds", $user_id, $total_price, $payment_method, $payment_status, $order_status, $shipping_charge, $delivery_address);

if (!$stmt->execute()) {
    echo "<h3>❌ Failed to place order. Please try again later.</h3>";
    exit;
}

$order_id = $stmt->insert_id;

// Step 3: Insert each cart item and update stock
foreach ($cart as $book_id => $quantity) {
    // Insert into order_items
    $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, book_id, quantity) VALUES (?, ?, ?)");
    if ($stmt_item === false) {
        echo "<h3>❌ Database error: Failed to prepare order items statement.</h3>";
        exit;
    }
    $stmt_item->bind_param("iii", $order_id, $book_id, $quantity);
    if (!$stmt_item->execute()) {
        echo "<h3>❌ Failed to save order items. Please contact support.</h3>";
        exit;
    }

    // Reduce stock
    $update_stock = $conn->prepare("UPDATE books SET stock = stock - ? WHERE id = ?");
    if ($update_stock === false) {
        echo "<h3>❌ Database error: Failed to prepare stock update statement.</h3>";
        exit;
    }
    $update_stock->bind_param("ii", $quantity, $book_id);
    if (!$update_stock->execute()) {
        echo "<h3>❌ Failed to update stock for book ID $book_id.</h3>";
        exit;
    }
}

// Step 4: Clear cart
unset($_SESSION['cart']);

// Step 5: Show confirmation
echo "<h3>✅ Order #$order_id placed successfully!</h3>";
echo "<a href='my_orders.php'>View My Orders</a>";
exit;
?>
