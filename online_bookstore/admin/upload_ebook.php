<?php
session_start();
include '../includes/db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);

    // File uploads
    $cover_image = $_FILES['cover_image'];
    $ebook_file = $_FILES['ebook_file'];

    // Create directories if not exist
    $cover_dir = '../uploads/covers/';
    $ebook_dir = '../uploads/ebooks/';
    if (!file_exists($cover_dir)) mkdir($cover_dir, 0777, true);
    if (!file_exists($ebook_dir)) mkdir($ebook_dir, 0777, true);

    $cover_path = $cover_dir . basename($cover_image['name']);
    $ebook_path = $ebook_dir . basename($ebook_file['name']);

    // Move files
    if (move_uploaded_file($cover_image['tmp_name'], $cover_path) &&
        move_uploaded_file($ebook_file['tmp_name'], $ebook_path)) {
        
        // Save to DB (store paths relative to root)
        $stmt = $conn->prepare("INSERT INTO books (title, author, price, description, cover_image, ebook_file, type, available) VALUES (?, ?, ?, ?, ?, ?, 'ebook', 1)");
        $relative_cover = 'uploads/covers/' . basename($cover_image['name']);
        $relative_ebook = 'uploads/ebooks/' . basename($ebook_file['name']);
        $stmt->bind_param("ssdsss", $title, $author, $price, $description, $relative_cover, $relative_ebook);
        
        if ($stmt->execute()) {
            $message = "✅ eBook uploaded successfully.";
        } else {
            $message = "❌ Error saving to database.";
        }
    } else {
        $message = "❌ Failed to upload files.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload eBook</title>
    <style>
        body { font-family: Arial; background: #f0f0f0; padding: 20px; }
        form { background: white; padding: 20px; max-width: 500px; margin: auto; border-radius: 10px; }
        input, textarea { width: 100%; margin-bottom: 10px; padding: 10px; }
        button { padding: 10px 20px; background: #007BFF; color: white; border: none; cursor: pointer; }
        button:hover { background: #0056b3; }
        .message { margin: 15px auto; color: green; font-weight: bold; text-align: center; }
    </style>
</head>
<body>

<h2 style="text-align:center;">📥 Upload eBook</h2>

<?php if ($message): ?>
    <div class="message"><?= $message ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <label>Title:</label>
    <input type="text" name="title" required>

    <label>Author:</label>
    <input type="text" name="author" required>

    <label>Price:</label>
    <input type="number" step="0.01" name="price" required>

    <label>Description:</label>
    <textarea name="description" rows="4" required></textarea>

    <label>Cover Image:</label>
    <input type="file" name="cover_image" accept="image/*" required>

    <label>eBook PDF File:</label>
    <input type="file" name="ebook_file" accept="application/pdf" required>

    <button type="submit">Upload eBook</button>
</form>

</body>
</html>
