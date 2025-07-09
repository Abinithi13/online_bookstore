<?php
include '../includes/db_connect.php';
session_start();
include '../includes/header.php';
$wish_id = intval($_GET['id']);
$conn->query("DELETE FROM wishlist WHERE id = $wish_id");
header("Location: view_wishlist.php");
exit;
?>
