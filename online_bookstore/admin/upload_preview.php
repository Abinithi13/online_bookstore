<?php
session_start();
include '../includes/db_connect.php';

// Optional: Restrict access to admins only
// if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
//     header("Location: ../auth/login.php");
//     exit;
// }

$success = "";
$error = "";

// Handle upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = intval($_POST['book_id']);
    $file = $_FILES['preview_file'];

    // Check file type and upload
    if ($file['error'] === 0 && strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) === 'pdf') {
        $targetDir = '../previews/';
        $filename = uniqid('preview_', true) . '.pdf';
        $targetPath = $targetDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            // Save filename to database
            $relativePath = 'previews/' . $filename;
            $stmt = $conn->prepare("UPDATE books SET preview_file = ? WHERE id = ?");
            $stmt->bind_param("si", $relativePath, $book_id);
            if ($stmt->execute()) {
                $success = "✅ Preview uploaded successfully!";
            } else {
                $error = "❌ Failed to update book record.";
            }
        } else {
            $error = "❌ Failed to upload file.";
        }
    } else {
        $error = "❌ Please upload a valid PDF file.";
    }
}

// Fetch all books for dropdown
$books = $conn->query("SELECT id, title FROM books ORDER BY title ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Preview File</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f4f4;
            padding: 40px;
            text-align: center;
        }
        .upload-box {
            background: #fff;
            padding: 30px;
            margin: auto;
            width: 400px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        select, input[type="file"], button {
            width: 100%;
            padding: 10px;
            margin-top: 15px;
            font-size: 16px;
        }
        .message {
            margin-top: 15px;
            color: green;
        }
        .error {
            margin-top: 15px;
            color: red;
        }
    </style>
</head>
<body>
    <div class="upload-box">
        <h2>📤 Upload eBook Preview (PDF)</h2>
        <form method="POST" enctype="multipart/form-data">
            <label>Select Book:</label>
            <select name="book_id" required>
                <option value="">-- Select Book --</option>
                <?php while ($book = $books->fetch_assoc()): ?>
                    <option value="<?= $book['id'] ?>"><?= htmlspecialchars($book['title']) ?></option>
                <?php endwhile; ?>
            </select>

            <label>Choose PDF File:</label>
            <input type="file" name="preview_file" accept="application/pdf" required>

            <button type="submit">Upload Preview</button>
        </form>

        <?php if ($success): ?><p class="message"><?= $success ?></p><?php endif; ?>
        <?php if ($error): ?><p class="error"><?= $error ?></p><?php endif; ?>
    </div>
</body>
</html>
