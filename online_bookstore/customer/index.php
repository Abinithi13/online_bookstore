<?php
include '../includes/db_connect.php';
include '../includes/navbar.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$search = $_GET['search'] ?? '';
$filter = $_GET['filter'] ?? '';
$sort = $_GET['sort'] ?? '';

$query = "SELECT * FROM books WHERE available = 1";

if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $query .= " AND (title LIKE '%$search%' OR author LIKE '%$search%')";
}

if (!empty($filter)) {
    $filter = $conn->real_escape_string($filter);
    $query .= " AND type = '$filter'";
}

switch ($sort) {
    case 'price_asc':
        $query .= " ORDER BY price ASC";
        break;
    case 'price_desc':
        $query .= " ORDER BY price DESC";
        break;
    case 'title_asc':
        $query .= " ORDER BY title ASC";
        break;
    case 'title_desc':
        $query .= " ORDER BY title DESC";
        break;
    default:
        $query .= " ORDER BY id DESC";
}
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Online Bookstore - Browse</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f3f4f7;
            margin: 0;
            padding: 0;
        }
        .bookstore-header {
            background: #1f3c88;
            color: #fff;
            text-align: center;
            padding: 20px;
        }
        .bookstore-header h1 {
            font-size: 32px;
            margin-bottom: 8px;
        }
        .bookstore-header p {
            font-size: 16px;
            margin: 0;
        }
        .container {
            padding: 30px;
            background: #fff;
            margin: 20px auto;
            width: 95%;
            max-width: 1300px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        h2 {
            text-align: left;
            color: #333;
            font-size: 24px;
            margin-bottom: 25px;
        }
        form.filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 25px;
            align-items: center;
        }
        form.filter-form input,
        form.filter-form select {
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 6px;
            min-width: 180px;
        }
        form.filter-form button,
        form.filter-form a {
            padding: 10px 16px;
            background-color: #1f3c88;
            color: white;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
        }
        form.filter-form a {
            background-color: #777;
        }
        .book-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 25px;
        }
        .book-card {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
            text-align: center;
        }
        .book-card:hover {
            transform: translateY(-6px);
        }
        .book-card img {
            width: 100%;
            height: 280px;
            object-fit: cover;
        }
        .book-info {
            padding: 16px;
        }
        .book-info h4 {
            font-size: 18px;
            margin: 10px 0 6px;
            color: #1f3c88;
        }
        .book-info p {
            margin: 4px 0;
            font-size: 14px;
            color: #555;
        }
        .book-info a {
            display: inline-block;
            margin: 8px 6px 0;
            padding: 8px 12px;
            font-size: 13px;
            background-color: #007BFF;
            color: #fff;
            border-radius: 4px;
            text-decoration: none;
        }
        .book-info a:hover {
            background-color: #0056b3;
        }
        .tooltip {
            position: relative;
            display: inline-block;
        }
        .tooltip .tooltiptext {
            visibility: hidden;
            width: 100px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 4px;
            padding: 5px 0;
            position: absolute;
            z-index: 1;
            bottom: 120%;
            left: 50%;
            margin-left: -50px;
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 12px;
        }
        .tooltip:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
        }
        footer {
            text-align: center;
            padding: 15px;
            font-size: 13px;
            color: #666;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="bookstore-header">
    <h1>Welcome to Our Online Bookstore</h1>
    <p>Discover, rent, and enjoy the best reads instantly!</p>
</div>

<div class="container">
    <h2>📚 Available Books</h2>

    <form method="GET" class="filter-form">
        <input type="text" name="search" placeholder="Search by title or author" value="<?= htmlspecialchars($search) ?>">
        <select name="filter">
            <option value="">All Types</option>
            <option value="ebook" <?= $filter == 'ebook' ? 'selected' : '' ?>>ebook</option>
            <option value="physical" <?= $filter == 'physical' ? 'selected' : '' ?>>physical</option>
            <option value="both" <?= $filter == 'both' ? 'selected' : '' ?>>both</option>
        </select>
        <select name="sort">
            <option value="">Sort By</option>
            <option value="title_asc" <?= $sort == 'title_asc' ? 'selected' : '' ?>>Title A-Z</option>
            <option value="title_desc" <?= $sort == 'title_desc' ? 'selected' : '' ?>>Title Z-A</option>
            <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>Price Low to High</option>
            <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>Price High to Low</option>
        </select>
        <button type="submit">Apply</button>
        <a href="index.php">Clear</a>
    </form>

    <div class="book-grid">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
            <div class="book-card">
                <img src="<?= '../' . htmlspecialchars($row['cover_image']) ?>" alt="<?= htmlspecialchars($row['title']) ?>">
                <div class="book-info">
                    <h4><?= htmlspecialchars($row['title']) ?></h4>
                    <p>by <?= htmlspecialchars($row['author']) ?></p>
                    <p>₹<?= number_format($row['price'], 2) ?></p>
                    <p><em><?= htmlspecialchars($row['type']) ?></em></p>

                    <div class="tooltip">
                        <a href="book_details.php?id=<?= $row['id'] ?>">Details</a>
                        <span class="tooltiptext">More Info</span>
                    </div>

                    <?php if ($row['stock'] > 0): ?>
                        <div class="tooltip">
                            <a href="add_to_cart.php?id=<?= $row['id'] ?>">🛒</a>
                            <span class="tooltiptext">Add to Cart</span>
                        </div>
                    <?php else: ?>
                        <p style="color: red; font-weight: bold;">❌ Out of Stock</p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No books found matching your criteria.</p>
        <?php endif; ?>
    </div>
</div>

<footer>
    © 2025 Online Bookstore Management System
</footer>

</body>
</html>
