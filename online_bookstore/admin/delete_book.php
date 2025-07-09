<?php
session_start();
include '../includes/db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $book_id = $_GET['id'];

    // Fetch the book's cover image filename from the database
    $stmt = $conn->prepare("SELECT cover_image FROM books WHERE id = ?");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $stmt->bind_result($cover_image);
    $stmt->fetch();
    $stmt->close();

    // Delete the book from the database
    $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $stmt->close();

    // If cover image exists, delete it from the server
    if ($cover_image && file_exists("../images/" . $cover_image)) {
        unlink("../images/" . $cover_image);
    }

    // Redirect back to the manage books page
    header("Location: manage_books.php");
    exit;
} else {
    echo "❌ No book ID provided.";
}
?>
