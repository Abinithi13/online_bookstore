<?php
include '../includes/db_connect.php';
session_start();

$user_id = $_SESSION['user_id'] ?? 1; // default/fake user
$book_id = intval($_GET['id']);

// Avoid duplicates
$stmt = $conn->prepare("SELECT * FROM wishlist WHERE user_id = ? AND book_id = ?");
$stmt->bind_param("ii", $user_id, $book_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $insert = $conn->prepare("INSERT INTO wishlist (user_id, book_id) VALUES (?, ?)");
    $insert->bind_param("ii", $user_id, $book_id);
    $insert->execute();
}

header("Location: view_wishlist.php");
exit;
?>
