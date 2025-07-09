<?php
session_start();
include '../includes/db_connect.php';
include '../includes/header.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $email = trim($_POST['email']);

    $stmt = $conn->prepare("UPDATE users SET full_name = ?, phone = ?, address = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $full_name, $phone, $address, $email, $user_id);

    if ($stmt->execute()) {
        $message = "✅ Profile updated successfully.";
    } else {
        $message = "❌ Failed to update profile.";
    }
}

// Fetch user details
$stmt = $conn->prepare("SELECT full_name, phone, address, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title> My Account</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 40px;
        }
        .account-container {
            max-width: 600px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #007BFF;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            margin-top: 20px;
            padding: 10px 15px;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        .message {
            text-align: center;
            margin-bottom: 15px;
            color: green;
        }
    </style>
</head>
<body>

<div class="account-container">
    <h2> My Account</h2>

    <?php if ($message): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="full_name">Full Name</label>
        <input type="text" id="full_name" name="full_name" required value="<?= htmlspecialchars($user['full_name']) ?>">

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required value="<?= htmlspecialchars($user['email']) ?>">

        <label for="phone">Phone</label>
        <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']) ?>">

        <label for="address">Address</label>
        <textarea id="address" name="address" rows="3"><?= htmlspecialchars($user['address']) ?></textarea>

        <button type="submit">💾 Update Profile</button>
    </form>
</div>

</body>
</html>
