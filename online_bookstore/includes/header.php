<!-- includes/header.php -->
<style>
    /* Navigation Bar Styles */
    nav.main-nav {
        background-color: #007BFF;
        padding: 12px 30px;
        display: flex;
        align-items: center;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    nav.main-nav a {
        color: #fff;
        text-decoration: none;
        margin-right: 25px;
        font-weight: 600;
        font-size: 16px;
        transition: color 0.3s ease;
        padding: 6px 10px;
        border-radius: 5px;
    }

    nav.main-nav a:hover {
        background-color: #0056b3;
        color: #e2e2e2;
    }

    nav.main-nav a.active {
        background-color: #004080;
        color: #fff;
    }

    /* Push menu items to right except home */
    nav.main-nav .menu-right {
        margin-left: auto;
        display: flex;
        align-items: center;
    }
</style>

<nav class="main-nav">
    <a href="/online_bookstore/customer/index.php" class="active" aria-label="Home">Home</a>
</nav>
