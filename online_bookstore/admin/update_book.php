<?php
include '../includes/db_connect.php';

$id = intval($_POST['id']);
$title = $_POST['title'];
$author = $_POST['author'];
$price = $_POST['price'];
$type = $_POST['type'];
$description = $_POST['description'];

// Get existing file paths
$existing = $conn->query("SELECT ebook_file, preview_file FROM books WHERE id = $id")->fetch_assoc();
$ebook_path = $existing['ebook_file'];
$preview_path = $existing['preview_file'];

// Replace if new files uploaded
if (!empty($_FILES['ebook_file']['name'])) {
    $ebook_path = 'uploads/ebooks/' . basename($_FILES['ebook_file']['name']);
    move_uploaded_file($_FILES['ebook_file']['tmp_name'], '../' . $ebook_path);
}

if (!empty($_FILES['preview_file']['name'])) {
    $preview_path = 'uploads/previews/' . basename($_FILES['preview_file']['name']);
    move_uploaded_file($_FILES['preview_file']['tmp_name'], '../' . $preview_path);
}

// Update query
$stmt = $conn->prepare("UPDATE books SET title=?, author=?, price=?, type=?, description=?, ebook_file=?, preview_file=? WHERE id=?");
$stmt->bind_param("ssdssssi", $title, $author, $price, $type, $description, $ebook_path, $preview_path, $id);
$stmt->execute();

header("Location: manage_books.php?success=1");
exit;
