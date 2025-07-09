<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include '../includes/db_connect.php';
include '../includes/header.php';
// Redirect if user not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$success = false;
$error = '';
$book = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $book_id = intval($_POST['book_id']);
    $duration = intval($_POST['duration']);

    // Validate book and ensure it's an ebook or both
    $stmt = $conn->prepare("SELECT * FROM books WHERE id = ? AND (type = 'ebook' OR type = 'both')");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $error = "Invalid eBook selection.";
    } else {
        $book = $result->fetch_assoc();
        $rent_price = round($book['price'] * 0.3, 2); // 30% of price
        $rent_date = date('Y-m-d');
        $expiry_date = date('Y-m-d', strtotime("+$duration days"));

        // Insert rental info into rented_ebooks
        $stmt = $conn->prepare("INSERT INTO rented_ebooks (user_id, book_id, rent_date, expiry_date, amount_paid) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iissd", $user_id, $book_id, $rent_date, $expiry_date, $rent_price);

        if ($stmt->execute()) {
            $success = true;
        } else {
            $error = "❌ Failed to rent the eBook. Try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Rent eBook</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 40px;
            text-align: center;
        }
        .box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            display: inline-block;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            max-width: 450px;
            width: 100%;
        }
        .box h2 {
            margin-bottom: 15px;
            color: #333;
        }
        label {
            display: block;
            margin-top: 15px;
            text-align: left;
        }
        select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .btn {
            margin-top: 20px;
            background-color: #007BFF;
            color: white;
            padding: 10px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
        .success-box {
            background: #e6ffe6;
            border: 1px solid #28a745;
            padding: 25px;
            border-radius: 10px;
        }
    </style>
</head>
<body>

<?php if ($success && $book): ?>
    <div class="box success-box">
        <h2>✅ eBook Rented Successfully!</h2>
        <p><strong>Title:</strong> <?= htmlspecialchars($book['title']) ?></p>
        <p><strong>Duration:</strong> <?= htmlspecialchars($duration) ?> days</p>
        <p><strong>Amount Paid:</strong> ₹<?= number_format($rent_price, 2) ?></p>
        <p><strong>Valid Till:</strong> <?= htmlspecialchars($expiry_date) ?></p>
        <a class="btn" href="my_rentals.php">📚 View My Rentals</a>
    </div>
<?php else: ?>
    <div class="box">
        <h2>Rent an eBook</h2>
        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <p>Something went wrong. Please go back and try again from the book details page.</p>
        <a class="btn" href="index.php">← Back to Books</a>
    </div>
<?php endif; ?>

</body>
</html>
