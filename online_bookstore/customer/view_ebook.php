<?php
session_start();
include '../includes/db_connect.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['rental_id'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];
$rental_id = intval($_GET['rental_id']);

// Validate rental
$stmt = $conn->prepare("
    SELECT r.*, b.title, b.ebook_file
    FROM rented_ebooks r
    JOIN books b ON r.book_id = b.id
    WHERE r.id = ? AND r.user_id = ?
");
$stmt->bind_param("ii", $rental_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Rental not found or not authorized.");
}

$row = $result->fetch_assoc();
$expired = strtotime($row['expiry_date']) < time();
if ($expired) {
    die("Your rental period has expired.");
}

$ebook_path = '../' . $row['ebook_file'];
if (!file_exists($ebook_path)) {
    die("eBook file not found.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View eBook - <?= htmlspecialchars($row['title']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 20px;
            text-align: center;
        }
        .ebook-container {
            margin: auto;
            max-width: 90%;
            height: 90vh;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
            border-radius: 8px;
            overflow: hidden;
        }
        iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
        h2 {
            margin-bottom: 20px;
            color: #007bff;
        }
    </style>
</head>
<body>

<h2>📖 Viewing: <?= htmlspecialchars($row['title']) ?></h2>

<div class="ebook-container">
    <iframe src="<?= $ebook_path ?>"></iframe>
</div>

</body>
</html>
