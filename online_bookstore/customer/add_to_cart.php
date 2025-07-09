<?php
session_start();
$book_id = intval($_GET['id']);

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_SESSION['cart'][$book_id])) {
    $_SESSION['cart'][$book_id] += 1;
} else {
    $_SESSION['cart'][$book_id] = 1;
}

header("Location: view_cart.php");
exit;
?>
