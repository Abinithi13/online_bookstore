<?php
include '../includes/admin_auth.php';
include '../includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title          = $_POST['title'];
    $author         = $_POST['author'];
    $description    = $_POST['description'];
    $price          = $_POST['price'];
    $type           = $_POST['type'];
    $category_id    = $_POST['category_id'];
    $subcategory_id = $_POST['subcategory_id'];

    // Upload cover image
    $cover_path = '';
    if (!empty($_FILES['cover']['name'])) {
        $cover_filename = basename($_FILES['cover']['name']);
        $cover_path = 'uploads/' . $cover_filename;
        move_uploaded_file($_FILES['cover']['tmp_name'], '../' . $cover_path);
    }

    // Upload eBook file
    $ebook_path = '';
    if ($type === 'ebook' || $type === 'both') {
        if (!empty($_FILES['ebook']['name'])) {
            $ebook_filename = basename($_FILES['ebook']['name']);
            $ebook_path = 'uploads/ebooks/' . $ebook_filename;
            move_uploaded_file($_FILES['ebook']['tmp_name'], '../' . $ebook_path);
        }
    }

    // Upload preview file
    $preview_path = '';
    if (!empty($_FILES['preview']['name'])) {
        $preview_filename = basename($_FILES['preview']['name']);
        $preview_path = 'uploads/previews/' . $preview_filename;
        move_uploaded_file($_FILES['preview']['tmp_name'], '../' . $preview_path);
    }

    $stmt = $conn->prepare("INSERT INTO books (title, author, description, price, type, category_id, subcategory_id, cover_image, ebook_file, preview_file) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssdsissss", $title, $author, $description, $price, $type, $category_id, $subcategory_id, $cover_path, $ebook_path, $preview_path);
    $stmt->execute();

    echo "<div class='success'>✅ Book added successfully!</div>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Book</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f7fa;
            padding: 30px;
        }
        .form-container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-container h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }
        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="number"],
        textarea,
        select,
        input[type="file"] {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 14px;
        }
        textarea {
            resize: vertical;
        }
        button {
            margin-top: 20px;
            width: 100%;
            padding: 12px;
            background: #007BFF;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        .success {
            text-align: center;
            color: green;
            font-weight: bold;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Add New Book</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Book Title:</label>
        <input type="text" name="title" required>

        <label>Author:</label>
        <input type="text" name="author" required>

        <label>Description:</label>
        <textarea name="description" rows="4"></textarea>

        <label>Price (₹):</label>
        <input type="number" step="0.01" name="price" required>

        <label>Type:</label>
        <select name="type" required>
            <option value="">Select type</option>
            <option value="physical">Physical</option>
            <option value="ebook">eBook</option>
            <option value="both">eBook and Physical</option>
        </select>

        <label>Category:</label>
        <select name="category_id" required>
            <option value="">Select Category</option>
            <?php
            $cat = $conn->query("SELECT id, name FROM categories");
            while ($row = $cat->fetch_assoc()) {
                echo "<option value='{$row['id']}'>{$row['name']}</option>";
            }
            ?>
        </select>

        <label>Subcategory:</label>
        <select name="subcategory_id">
            <option value="">Select Subcategory</option>
            <?php
            $subcat = $conn->query("SELECT id, name FROM subcategories");
            while ($row = $subcat->fetch_assoc()) {
                echo "<option value='{$row['id']}'>{$row['name']}</option>";
            }
            ?>
        </select>

        <label>Cover Image:</label>
        <input type="file" name="cover" accept="image/*" required>

        <label>eBook File (PDF, if applicable):</label>
        <input type="file" name="ebook" accept=".pdf">

        <button type="submit">Add Book</button>
    </form>
</div>

</body>
</html>
