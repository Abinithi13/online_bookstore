<?php
session_start();
include '../includes/db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$book_id = intval($_GET['id']);

// Delete rental (remove rent duration and expiry)
$stmt = $conn->prepare("UPDATE books SET rent_duration = 0, rental_expiry = NULL WHERE id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();

echo "<h3>✅ Rental deleted successfully!</h3>";
echo "<a href='manage_rentals.php'>Back to Rentals</a>";
exit;
