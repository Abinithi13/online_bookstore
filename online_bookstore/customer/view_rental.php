<?php
session_start();
include '../includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    die("Access denied");
}

$user_id = $_SESSION['user_id'];
$file = basename($_GET['file']);
$file_path = "../uploads/ebooks/" . $file;

// Prepare the path as stored in the database
$db_path = "uploads/ebooks/" . $file;

// Fix query to compare full path
$stmt = $conn->prepare("
    SELECT r.* FROM rented_ebooks r
    JOIN books b ON r.book_id = b.id
    WHERE r.user_id = ? AND b.ebook_file = ? AND r.expiry_date >= CURDATE()
");
$stmt->bind_param("is", $user_id, $db_path);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0 || !file_exists($file_path)) {
    die("Unauthorized access or file not found.");
}

// Serve the PDF for inline view only
header("Content-Type: application/pdf");
header("Content-Disposition: inline; filename=\"" . $file . "\"");
readfile($file_path);
exit;
?>
