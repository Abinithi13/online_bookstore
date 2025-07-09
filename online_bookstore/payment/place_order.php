<?php
session_start();
include '../includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $payment_method = $_POST['payment_method'];
    $address = $_POST['address'];

    $result = $conn->query("SELECT SUM(b.price * c.quantity) AS total
                            FROM cart c JOIN books b ON c.book_id = b.id
                            WHERE c.user_id = $user_id");
    $row = $result->fetch_assoc();
    $total_price = $row['total'] ?? 0;

    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_price, payment_method, delivery_address) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("idss", $user_id, $total_price, $payment_method, $address);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // Insert each cart item
    $cartItems = $conn->query("SELECT * FROM cart WHERE user_id = $user_id");
    while ($item = $cartItems->fetch_assoc()) {
        $book_id = $item['book_id'];
        $quantity = $item['quantity'];

        $book = $conn->query("SELECT price FROM books WHERE id = $book_id")->fetch_assoc();
        $price = $book['price'];

        $insertItem = $conn->prepare("INSERT INTO order_items (order_id, book_id, quantity, price) VALUES (?, ?, ?, ?)");
        $insertItem->bind_param("iiid", $order_id, $book_id, $quantity, $price);
        $insertItem->execute();
    }

    // Clear cart
    $conn->query("DELETE FROM cart WHERE user_id = $user_id");

    echo "<h3>✅ Order placed successfully!</h3><p>Your Order ID: <strong>$order_id</strong></p>";
}
?>
