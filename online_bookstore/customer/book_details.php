<?php
include '../includes/db_connect.php';
session_start();

if (!isset($_GET['id'])) {
    echo "Book ID not provided.";
    exit;
}

$book_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Book not found.";
    exit;
}

$book = $result->fetch_assoc();

$type = strtolower($book['type']); // Normalize for case-insensitive comparison
$stock = (int)$book['stock'];

$previewFileName = trim($book['preview_file']);
$previewPath = "../previews/" . $previewFileName;
$hasPreview = !empty($previewFileName) && file_exists($previewPath);
$previewWebPath = '/online_bookstore/previews/' . rawurlencode($previewFileName);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title><?= htmlspecialchars($book['title']) ?> - Details</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 30px;
        }
        .book-details {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            max-width: 800px;
            margin: auto;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            display: flex;
            gap: 20px;
        }
        .book-details img {
            width: 250px;
            height: 350px;
            object-fit: cover;
            border-radius: 8px;
        }
        .info {
            flex: 1;
        }
        .info h2 {
            margin-top: 0;
        }
        .info p {
            margin: 10px 0;
        }
        .buttons a, .buttons span {
            display: inline-block;
            margin: 6px 6px 0 0;
            padding: 8px 12px;
            background: #007BFF;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }
        .buttons a:hover {
            background: #0056b3;
        }
        .buttons span {
            background: #ccc;
            color: #666;
            cursor: not-allowed;
        }
        .stock-info {
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="book-details">
    <img src="../<?= htmlspecialchars($book['cover_image']) ?>" alt="<?= htmlspecialchars($book['title']) ?>">
    <div class="info">
        <h2><?= htmlspecialchars($book['title']) ?></h2>
        <p><strong>Author:</strong> <?= htmlspecialchars($book['author']) ?></p>
        <p><strong>Price:</strong> ₹<?= number_format($book['price'], 2) ?></p>
        <p><strong>Type:</strong> <?= ucfirst(htmlspecialchars($book['type'])) ?></p>
        <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($book['description'])) ?></p>

        <?php if ($type === 'physical' || $type === 'both'): ?>
            <p class="stock-info">
                <?php if ($stock == 0): ?>
                    <span style="color: red;">❌ Out of Stock</span>
                <?php elseif ($stock < 5): ?>
                    <span style="color: orange;">⚠️ Hurry! Only <?= $stock ?> left in stock</span>
                <?php else: ?>
                    <span style="color: green;">✅ In Stock (<?= $stock ?> available)</span>
                <?php endif; ?>
            </p>
        <?php endif; ?>

        <div class="buttons">
            <?php if ($type === 'physical' || $type === 'both'): ?>
                <?php if ($stock > 0): ?>
                    <a href="add_to_cart.php?id=<?= $book['id'] ?>">🛒 Add to Cart</a>
                <?php else: ?>
                    <span>🛒 Add to Cart</span>
                <?php endif; ?>
            <?php endif; ?>

            <a href="add_to_wishlist.php?id=<?= $book['id'] ?>">❤️ Wishlist</a>

            <?php if ($type === 'ebook' || $type === 'both'): ?>
                <a href="rent_ebook.php?id=<?= $book['id'] ?>">⏳ Rent eBook</a>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
