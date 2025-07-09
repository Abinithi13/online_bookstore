<?php
session_start();
include '../includes/db_connect.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch rentals
$stmt = $conn->prepare("
    SELECT r.*, b.title, b.ebook_file 
    FROM rented_ebooks r
    JOIN books b ON r.book_id = b.id
    WHERE r.user_id = ?
    ORDER BY r.rent_date DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Rentals</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 40px;
        }
        .container {
            max-width: 900px;
            margin: auto;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #007BFF;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }
        th {
            background: #007BFF;
            color: white;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        .expired {
            color: red;
            font-weight: bold;
        }
        .active {
            color: green;
            font-weight: bold;
        }
        a.view-btn {
            background: #17a2b8;
            color: white;
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 4px;
        }
        a.view-btn:hover {
            background: #138496;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>📚 My Rented eBooks</h2>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Title</th>
                <th>Rented On</th>
                <th>Expires On</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): 
                $expired = strtotime($row['expiry_date']) < time();
            ?>
                <tr>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= htmlspecialchars($row['rent_date']) ?></td>
                    <td><?= htmlspecialchars($row['expiry_date']) ?></td>
                    <td class="<?= $expired ? 'expired' : 'active' ?>">
                        <?= $expired ? 'Expired' : 'Active' ?>
                    </td>
                    <td>
                        <?php if (!$expired && !empty($row['ebook_file'])): ?>
                            <a class="view-btn" href="view_rental.php?file=<?= urlencode($row['ebook_file']) ?>" target="_blank">👁️ View</a>
                        <?php else: ?>
                            <span style="color: gray;">Not Available</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p style="text-align: center;">You haven’t rented any eBooks yet.</p>
    <?php endif; ?>
</div>

</body>
</html>
