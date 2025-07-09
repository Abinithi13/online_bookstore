<?php
include '../includes/db_connect.php';
session_start();
include '../includes/header.php'; 

// Ensure the user_id is correctly set
$user_id = $_SESSION['user_id'] ?? 1; // Default to 1 if not set

// SQL query to fetch wishlist items
$query = "
    SELECT w.id as wish_id, b.* 
    FROM wishlist w 
    JOIN books b ON w.book_id = b.id 
    WHERE w.user_id = $user_id
";

$result = $conn->query($query);

// Debug: Check if there are any issues with the query
if (!$result) {
    die("Error executing query: " . $conn->error);
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>My Wishlist</title>
    <style>
        body { font-family: Arial; background: #f2f2f2; padding: 20px; }
        .wishlist-item {
            background: #fff;
            margin-bottom: 15px;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .wishlist-item h3 { margin: 0; }
        .actions a {
            margin-right: 10px;
            text-decoration: none;
            color: white;
            background: #007bff;
            padding: 6px 12px;
            border-radius: 5px;
        }
        .actions a:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h2>❤️ My Wishlist</h2>

    <?php
    // Check if the query returned any rows
    if ($result->num_rows > 0):
        while ($row = $result->fetch_assoc()):
    ?>
        <div class="wishlist-item">
            <h3><?= htmlspecialchars($row['title']) ?></h3>
            <p>Author: <?= $row['author'] ?> | Price: ₹<?= $row['price'] ?> | Type: <?= $row['type'] ?></p>
            <div class="actions">
                <a href="book_details.php?id=<?= $row['id'] ?>">View</a>
                <a href="remove_from_wishlist.php?id=<?= $row['wish_id'] ?>" style="background:#dc3545;">Remove</a>
            </div>
        </div>
    <?php
        endwhile;
    else:
        echo "<p>No items found in your wishlist.</p>";
    endif;
    ?>
</body>
</html>
