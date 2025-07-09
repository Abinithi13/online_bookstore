<?php
session_start();
include '../includes/db_connect.php';

$login_error = '';
$register_msg = '';

// LOGIN
if (isset($_POST['action']) && $_POST['action'] === 'login') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $username, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            header("Location: ../customer/index.php");
            exit;
        } else {
            $login_error = "❌ Incorrect password.";
        }
    } else {
        $login_error = "❌ No account found with that email.";
    }
}

// REGISTER
if (isset($_POST['action']) && $_POST['action'] === 'register') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        $register_msg = "✅ Registered successfully. You can now login.";
    } else {
        $register_msg = "❌ Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Login & Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eef2f3;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-top: 60px;
            height: 100vh;
        }
        .container {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            width: 400px;
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
        }
        form {
            display: none;
        }
        form.active {
            display: block;
        }
        label {
            display: block;
            margin-top: 10px;
            color: #333;
        }
        input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            margin-top: 20px;
            background: #007BFF;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background: #0056b3;
        }
        .tabs {
            display: flex;
            justify-content: space-around;
            margin-bottom: 15px;
        }
        .tabs button {
            background: #f0f0f0;
            color: #333;
            padding: 10px 20px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
        }
        .tabs button.active {
            background: #007BFF;
            color: white;
        }
        .message {
            color: red;
            margin: 10px 0;
            text-align: center;
        }
        .success {
            color: green;
        }
    </style>
    <script>
        function switchTab(tab) {
            document.querySelectorAll('form').forEach(f => f.classList.remove('active'));
            document.getElementById(tab).classList.add('active');

            document.querySelectorAll('.tabs button').forEach(b => b.classList.remove('active'));
            document.getElementById(tab + '-btn').classList.add('active');
        }

        window.onload = () => {
            switchTab('login'); // default tab
        };
    </script>
</head>
<body>

<div class="container">
    <div class="tabs">
        <button id="login-btn" onclick="switchTab('login')">Login</button>
        <button id="register-btn" onclick="switchTab('register')">Register</button>
    </div>

    <!-- Login Form -->
    <form id="login" method="POST" class="active">
        <h2>Login</h2>
        <?php if (!empty($login_error)): ?>
            <div class="message"><?= htmlspecialchars($login_error) ?></div>
        <?php endif; ?>
        <input type="hidden" name="action" value="login">
        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>
    </form>

    <!-- Register Form -->
    <form id="register" method="POST">
        <h2>Register</h2>
        <?php if (!empty($register_msg)): ?>
            <div class="message <?= strpos($register_msg, '✅') !== false ? 'success' : '' ?>">
                <?= htmlspecialchars($register_msg) ?>
            </div>
        <?php endif; ?>
        <input type="hidden" name="action" value="register">
        <label>Username:</label>
        <input type="text" name="username" required>

        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <button type="submit">Register</button>
    </form>
</div>

</body>
</html>
