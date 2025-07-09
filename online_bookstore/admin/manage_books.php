<?php
session_start();
include '../includes/db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch books
$result = $conn->query("SELECT * FROM books ORDER BY title");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Manage Books</title>
<style>
    /* Your existing styles for body, h2, ul.book-list, etc. */
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f9fafb;
        margin: 0;
        padding: 40px 20px;
        color: #333;
    }
    h2 {
        text-align: center;
        font-size: 2.4rem;
        margin-bottom: 30px;
        color: #2c3e50;
    }
    ul.book-list {
        max-width: 600px;
        margin: 0 auto;
        list-style: none;
        padding: 0;
    }
    ul.book-list li {
        background: #fff;
        margin-bottom: 10px;
        padding: 14px 20px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 600;
        color: #444;
    }
    ul.book-list li a {
        margin-left: 15px;
        color: #3498db;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.2s ease;
    }
    ul.book-list li a:hover {
        color: #2980b9;
    }
    ul.book-list li a.delete {
        color: #e74c3c;
    }
    ul.book-list li a.delete:hover {
        color: #c0392b;
    }
    .add-new-btn {
        display: block;
        max-width: 600px;
        margin: 0 auto 30px auto;
        padding: 12px 20px;
        background-color: #3498db;
        color: #fff;
        text-align: center;
        font-weight: 700;
        text-decoration: none;
        border-radius: 10px;
        transition: background-color 0.3s ease;
    }
    .add-new-btn:hover {
        background-color: #2980b9;
    }
    h3 {
    max-width: 600px;
    margin: 40px auto 20px auto;
    color: #2c3e50;
    font-size: 1.8rem;
    border-bottom: 2px solid #3498db;
    padding-bottom: 8px;
    text-align: center; /* optional */
}

</style>
</head>
<body>

<h2>Manage Books</h2>

<!-- Optional: Link to Add New Book page -->
<a href="add_book.php" class="add-new-btn">Add New Book</a>

<h3>Existing Books</h3>
<ul class="book-list">
    <?php while ($book = $result->fetch_assoc()): ?>
        <li>
            <span><?= htmlspecialchars($book['title']) ?> - <?= htmlspecialchars($book['author']) ?></span>
            <span>
                <a href="edit_book.php?id=<?= $book['id'] ?>">Edit</a>
                <a href="delete_book.php?id=<?= $book['id'] ?>" class="delete" onclick="return confirm('Are you sure you want to delete this book?');">Delete</a>
            </span>
        </li>
    <?php endwhile; ?>
</ul>

</body>
</html>
