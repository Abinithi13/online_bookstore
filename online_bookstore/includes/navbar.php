<?php
session_start();
?>

<style>
    .navbar {
        background-color: #343a40;
        padding: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: white;
        font-family: Arial, sans-serif;
    }

    .navbar a {
        color: white;
        margin: 0 12px;
        text-decoration: none;
    }

    .navbar a:hover {
        text-decoration: underline;
    }

    .navbar .left, .navbar .right {
        display: flex;
        align-items: center;
    }
</style>

<div class="navbar">
    <div class="left">
        <a href="/online_bookstore/index.php"><strong>📚 Online Bookstore</strong></a>
        <a href="/online_bookstore/customer/cart.php">Cart</a>
        <a href="/online_bookstore/customer/wishlist.php">Wishlist</a>
        <a href="/online_bookstore/customer/orders.php">My Orders</a>
        <a href="/online_bookstore/customer/my_rentals.php"> My Rentals</a>
        <a href="/online_bookstore/customer/my_account.php"> My Account</a>
    </div>
    <div class="right">
        <?php if (isset($_SESSION['user_id'])): ?>
            <span>Welcome, <?= $_SESSION['user_name'] ?? 'User' ?></span>
            <a href="/online_bookstore/auth/logout.php">Logout</a>
        <?php else: ?>
            <a href="/online_bookstore/auth/login.php">Login</a>
        <?php endif; ?>

        <?php if (isset($_SESSION['admin_id'])): ?>
            <a href="/online_bookstore/admin/dashboard.php">Admin Dashboard</a>
        <?php endif; ?>
    </div>
</div>
