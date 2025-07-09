<?php 
include '../includes/admin_auth.php'; // Admin authentication check
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="../assets/css/styles.css"> <!-- Optional external stylesheet -->
  <style>
    :root {
      --primary-color: #007bff;
      --primary-hover: #0056b3;
      --background-color: #f4f6f9;
      --card-bg: #ffffff;
      --text-color: #333;
      --border-radius: 10px;
      --box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
      background: var(--background-color);
      color: var(--text-color);
    }

    .dashboard-container {
      max-width: 600px;
      margin: 50px auto;
      background: var(--card-bg);
      padding: 30px 40px;
      border-radius: var(--border-radius);
      box-shadow: var(--box-shadow);
      text-align: center;
    }

    h2 {
      color: var(--primary-color);
      margin-bottom: 30px;
    }

    .dashboard-links {
      display: flex;
      flex-direction: column;
      gap: 15px;
      margin-top: 20px;
    }

    .dashboard-links a {
      display: block;
      background-color: var(--primary-color);
      color: white;
      padding: 12px 20px;
      text-decoration: none;
      border-radius: 6px;
      font-weight: bold;
      transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .dashboard-links a:hover {
      background-color: var(--primary-hover);
      transform: translateY(-2px);
    }

    @media (max-width: 768px) {
      .dashboard-container {
        margin: 20px;
        padding: 20px;
      }
    }
  </style>
</head>
<body>

  <div class="dashboard-container">
    <h2>📊 Admin Dashboard</h2>

    <div class="dashboard-links">
      <a href="manage_books.php">📚 Manage Books</a>
      <a href="view_orders.php">🛒 View Orders</a>
      <a href="manage_rentals.php">📖 Manage eBook Rentals</a>
      <a href="add_book.php">➕ Add New Book</a>
      <a href="upload_ebook.php">⬆️ Upload eBooks</a>
      <a href="logout.php">🚪 Logout</a>
    </div>
  </div>

</body>
</html>
