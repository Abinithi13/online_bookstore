<?php
session_start();
$id = intval($_GET['id']);
unset($_SESSION['cart'][$id]);
header("Location: view_cart.php");
exit;
?>
