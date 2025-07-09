<?php
session_start();
include '../includes/db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$book_id = intval($_GET['id']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $additional_days = intval($_POST['additional_days']);
    $current_expiry = $_POST['current_expiry'];
    $new_expiry = date('Y-m-d', strtotime("$current_expiry + $additional_days days"));

    // Update rental expiry
    $stmt = $conn->prepare("UPDATE books SET rental_expiry = ? WHERE id = ?");
    $stmt->bind_param("si", $new_expiry, $book_id);
    $stmt->execute();

    echo "<h3>✅ Rental extended! New expiry date: $new_expiry</h3>";
    echo "<a href='manage_rentals.php'>Back to Rentals</a>";
    exit;
}

// Get current rental expiry
$result = $conn->query("SELECT rental_expiry FROM books WHERE id = $book_id");
$book = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Extend Rental</title>
</head>
<body>

<h2>Extend Rental for eBook</h2>
<form method="POST">
    <label for="additional_days">Additional Days:</label><br>
    <input type="number" name="additional_days" min="1" required><br><br>

    <input type="hidden" name="current_expiry" value="<?= $book['rental_expiry'] ?>">

    <button type="submit">Extend Rental</button>
</form>

</body>
</html>
