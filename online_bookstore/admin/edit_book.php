<?php
include '../includes/db_connect.php';
if (!isset($_GET['id'])) {
    die("Book ID missing.");
}

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM books WHERE id = $id");
if ($result->num_rows === 0) {
    die("Book not found.");
}
$book = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Book</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f4f4;
            padding: 30px;
        }

        .form-container {
            max-width: 600px;
            background: #fff;
            margin: auto;
            padding: 25px 30px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .form-container h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            color: #555;
        }

        input[type="text"],
        input[type="number"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }

        textarea {
            resize: vertical;
            height: 100px;
        }

        input[type="submit"] {
            background-color: #007BFF;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .back-link {
            text-align: center;
            margin-top: 15px;
        }

        .back-link a {
            text-decoration: none;
            color: #007BFF;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>✏️ Edit Book</h2>
    <form action="update_book.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $book['id'] ?>">

        <label>Title:</label>
        <input type="text" name="title" value="<?= htmlspecialchars($book['title']) ?>" required>

        <label>Author:</label>
        <input type="text" name="author" value="<?= htmlspecialchars($book['author']) ?>" required>

        <label>Price (₹):</label>
        <input type="number" name="price" step="0.01" value="<?= $book['price'] ?>" required>

        <label>Type:</label>
        <input type="text" name="type" value="<?= htmlspecialchars($book['type']) ?>" required>

        <label>Description:</label>
        <textarea name="description"><?= htmlspecialchars($book['description']) ?></textarea>

        <label>Replace Full eBook (optional):</label>
        <input type="file" name="ebook_file" accept=".pdf">

        <input type="submit" value="Update Book">
    </form>
    <div class="back-link">
        <a href="manage_books.php">← Back to Manage Books</a>
    </div>
</div>

</body>
</html>
