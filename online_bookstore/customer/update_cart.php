<?php
session_start();
include '../includes/header.php';
if (isset($_POST['quantities']) && is_array($_POST['quantities'])) {
    foreach ($_POST['quantities'] as $book_id => $qty) {
        $book_id = intval($book_id);
        $qty = intval($qty);
        if ($qty > 0) {
            $_SESSION['cart'][$book_id] = $qty;
        } else {
            unset($_SESSION['cart'][$book_id]); // Remove if qty is 0 or less
        }
    }
}

header("Location: cart.php");
exit;
