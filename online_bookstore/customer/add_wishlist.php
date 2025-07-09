<?php
session_start();
include '../includes/db_connect.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get the book ID from the query parameter
if (!isset($_GET['id'])) {
    echo "Book ID not provided.";
    exit;
}

$book_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Check if the book is already in the user's wishlist
$query = "SELECT * FROM wishlist WHERE user_id = ? AND book_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $book_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Book already in wishlist
    header("Location: book_details.php?id=$book_id&message=Already in your wishlist");
    exit;
}

// Insert the book into the wishlist
$query = "INSERT INTO wishlist (user_id, book_id) VALUES (?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $book_id);

if ($stmt->execute()) {
    // Redirect back to the book details page with a success message
    header("Location: book_details.php?id=$book_id&message=Added to wishlist");
} else {
    echo "Error adding book to wishlist.";
}

// Close the prepared statement
$stmt->close();
?>
