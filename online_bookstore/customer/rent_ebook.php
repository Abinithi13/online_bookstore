<?php
session_start();
include '../includes/db_connect.php';
include '../includes/header.php';
// Redirect if user not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$book_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];
$book = null;
$error = '';
$success = false;

if ($book_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM books WHERE id = ? AND (type = 'ebook' OR type = 'both')");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $error = "This book is not available for rent.";
    } else {
        $book = $result->fetch_assoc();
    }
} else {
    $error = "Invalid book ID.";
}

// Handle rent form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $book) {
    $duration = intval($_POST['duration']);
    $base_price = $book['price'];

    switch ($duration) {
        case 7:
            $rent_price = round($base_price * 0.15, 2); // 15% for 7 days
            break;
        case 14:
            $rent_price = round($base_price * 0.25, 2); // 25% for 14 days
            break;
        case 30:
            $rent_price = round($base_price * 0.30, 2); // 30% for 30 days
            break;
        default:
            $rent_price = round($base_price * 0.30, 2); // fallback
    }

    $rent_date = date('Y-m-d');
    $expiry_date = date('Y-m-d', strtotime("+$duration days"));

    $stmt = $conn->prepare("INSERT INTO rented_ebooks (user_id, book_id, rent_date, expiry_date, amount_paid)
                            VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iissd", $user_id, $book_id, $rent_date, $expiry_date, $rent_price);

    if ($stmt->execute()) {
        $success = true;
    } else {
        $error = "❌ Failed to rent the eBook. Try again later.";
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
            background: #f2f2f2;
            padding: 40px;
            text-align: center;
        }
        .box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            display: inline-block;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 100%;
        }
        h2 {
            color: #333;
        }
        label {
            display: block;
            margin-top: 15px;
            text-align: left;
        }
        select, button {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .btn {
            background-color: #007BFF;
            color: white;
            border: none;
            margin-top: 20px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            margin-top: 15px;
        }
        .success {
            color: green;
            font-weight: bold;
            margin-top: 20px;
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
<?php elseif ($book): ?>
    <div class="box">
        <h2>Rent: <?= htmlspecialchars($book['title']) ?></h2>

        <?php if (!empty($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST">
            <label for="duration">Select Duration (in days):</label>
            <select name="duration" id="duration" required>
                <option value="7">7 Days</option>
                <option value="14">14 Days</option>
                <option value="30">30 Days</option>
            </select>

            <button type="submit" class="btn" id="rentButton">⏳ Rent Now</button>
        </form>
    </div>

    <script>
        const price = <?= $book['price'] ?>;
        const durationSelect = document.getElementById('duration');
        const rentButton = document.getElementById('rentButton');

        function updatePrice() {
            const days = parseInt(durationSelect.value);
            let amount = 0;
            if (days === 7) amount = price * 0.15;
            else if (days === 14) amount = price * 0.25;
            else if (days === 30) amount = price * 0.30;
            rentButton.innerText = `⏳ Rent Now for ₹${amount.toFixed(2)}`;
        }

        durationSelect.addEventListener('change', updatePrice);
        window.addEventListener('load', updatePrice);
    </script>

<?php else: ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

</body>
</html>
