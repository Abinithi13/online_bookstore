<?php
session_start();
include '../includes/db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch eBooks with rent info from books table directly
$result = $conn->query("SELECT * FROM books WHERE type = 'ebook' AND rent_duration > 0 AND rental_expiry IS NOT NULL");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage eBook Rentals</title>
    <style>
        body { font-family: Arial; background: #f9f9f9; padding: 20px; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: center; }
        th { background: #eee; }
        .rental-status {
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
        }
        .active { background: #28a745; color: white; }
        .expired { background: #ffc107; color: black; }
    </style>
</head>
<body>

<h2>Manage eBook Rentals</h2>

<?php if ($result->num_rows > 0): ?>
    <table>
        <tr>
            <th>Book Title</th>
            <th>Rent Duration</th>
            <th>Rental Expiry</th>
            <th>Status</th>
        </tr>
        <?php while ($book = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($book['title']) ?></td>
                <td><?= $book['rent_duration'] ?> Days</td>
                <td><?= $book['rental_expiry'] ?></td>
                <td>
                    <?php
                    $current_date = date('Y-m-d');
                    if ($current_date <= $book['rental_expiry']) {
                        echo "<span class='rental-status active'>Active</span>";
                    } else {
                        echo "<span class='rental-status expired'>Expired</span>";
                    }
                    ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>No eBook rentals found.</p>
<?php endif; ?>

</body>
</html>
