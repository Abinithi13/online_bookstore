<?php
session_start();
include '../includes/db_connect.php';
include '../includes/header.php'; 

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get the user_id from the session
$user_id = $_SESSION['user_id'];

// Query to fetch the wishlist items along with book details
$query = "
    SELECT w.id AS wish_id, b.id AS book_id, b.title, b.author, b.price, b.type
    FROM wishlist w
    JOIN books b ON w.book_id = b.id
    WHERE w.user_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);  // Bind user_id as an integer
$stmt->execute();
$result = $stmt->get_result();

// Check if there are items in the wishlist
if ($result->num_rows > 0):
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Wishlist</title>
    <style>
        body {
            font-family: Arial;
            background: #f2f2f2;
            padding: 20px;
        }
        .wishlist-item {
            background: #fff;
            margin-bottom: 15px;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .wishlist-item h3 {
            margin: 0;
        }
        .actions a {
            margin-right: 10px;
            text-decoration: none;
            color: white;
            background: #007bff;
            padding: 6px 12px;
            border-radius: 5px;
        }
        .actions a:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <h2>❤️ My Wishlist</h2>

    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="wishlist-item">
            <h3><?= htmlspecialchars($row['title']) ?></h3>
            <p>Author: <?= htmlspecialchars($row['author']) ?> | Price: ₹<?= number_format($row['price'], 2) ?> | Type: <?= htmlspecialchars($row['type']) ?></p>
            <div class="actions">
                <a href="book_details.php?id=<?= $row['book_id'] ?>">View</a>
                <a href="remove_from_wishlist.php?id=<?= $row['wish_id'] ?>" style="background:#dc3545;">Remove</a>
            </div>
        </div>
    <?php endwhile; ?>

</body>
</html>

<?php
else:
    ?>
    <style>
        .empty-wishlist-box {
            font-family: 'Segoe UI', sans-serif;
            text-align: center;
            margin: 80px auto;
            padding: 40px;
            max-width: 500px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .empty-wishlist-box h3 {
            color: #ff6b6b;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .empty-wishlist-box a {
            display: inline-block;
            text-decoration: none;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .empty-wishlist-box a:hover {
            background-color: #0056b3;
        }
    </style>

    <div class="empty-wishlist-box">
        <h3>💔 Your wishlist is empty.</h3>
        <a href="index.php">← Browse Books</a>
    </div>
    <?php
endif;

// Close the prepared statement
$stmt->close();
?>
