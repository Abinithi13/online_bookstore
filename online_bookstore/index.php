<?php
include 'includes/db_connect.php';
include 'includes/navbar.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Bookstore</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('assets/images/background.jpg');
            background-size: cover;
            background-attachment: fixed;
            margin: 0;
            padding: 0;
        }

        .container {
            padding: 20px;
            background: rgba(255, 255, 255, 0.95);
            margin: 40px auto;
            width: 90%;
            max-width: 1200px;
            border-radius: 10px;
        }

        .book-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }

        .book-card {
            border: 1px solid #ccc;
            border-radius: 10px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.2s ease;
        }

        .book-card:hover {
            transform: scale(1.02);
        }

        .book-card img {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }

        .book-info {
            padding: 10px;
        }

        .book-info h4 {
            margin: 10px 0 5px;
            font-size: 18px;
        }

        .book-info p {
            margin: 5px 0;
        }

        .book-info a {
            display: inline-block;
            margin: 6px 4px;
            padding: 6px 10px;
            background: #007BFF;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
        }

        .book-info a:hover {
            background: #0056b3;
        }

        /* Additional styles from fronteg.html */
        .bookstore-header {
            background-color: #BB2649;
            color: #fff;
            padding: 20px;
            text-align: center;
        }

        .bookstore-header h1 {
            font-size: 48px;
            margin-bottom: 10px;
        }

        .bookstore-header p {
            font-size: 20px;
            margin: 0;
        }
    </style>
</head>
<body>

<div class="bookstore-header">
    <h1>Welcome to Our Online Bookstore</h1>
    <p>Find your next great read!</p>
</div>

<div class="container">
    <h2>📚 Available Books</h2>
    <div class="book-grid">
        <?php
        $result = $conn->query("SELECT * FROM books WHERE available = 1");
        while ($row = $result->fetch_assoc()) {
            echo "
            <div class='book-card'>
                <img src='{$row['cover_image']}' alt='{$row['title']}'>
                <div class='book-info'>
                    <h4>{$row['title']}</h4>
                    <p>by {$row['author']}</p>
                    <p>₹{$row['price']}</p>
                    <p><em>{$row['type']}</em></p>
                    <a href='customer/book_details.php?id={$row['id']}'>View Details</a>
                    <a href='customer/add_to_cart.php?id={$row['id']}'>🛒</a>
                </div>
            </div>";
        }
        ?>
    </div>
</div>

</body>
</html>
