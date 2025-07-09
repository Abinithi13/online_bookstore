# online_bookstore
A full-stack DBMS project using PHP, MySQL, HTML, CSS & JS. Features include user/admin login, book listings, cart, orders, eBook rentals, and multiple payment options. Built as part of an academic project.
 
# 📚 Online Bookstore Management System

Welcome to the **Online Bookstore Management System**, a full-stack web application developed as part of a **Database Management System (DBMS)** project. This system allows users to browse, rent, and purchase books online, while giving admins complete control over inventory and order management.

Built using **PHP, MySQL, HTML, CSS, and JavaScript**, this project simulates a real-world e-commerce bookstore with features like cart, checkout, payments, rentals, and more.

---

## 🚀 Features

### 👥 User & Admin Roles
- **Customers** can browse books, rent or buy, track their orders, and make payments.
- **Admins** can manage books, availability, rentals, and customer orders through a dedicated dashboard.

### 🛒 Customer Functionality
- Browse books with detailed information
- Add books to cart or rent eBooks
- Choose from multiple payment options:
  - UPI (with QR code display)
  - Credit/Debit Card (bank selection included)
  - Cash on Delivery (adds shipping charge)
- Track order status and rental duration
- View past purchases and rentals

### 🧑‍💼 Admin Functionality
- Login-secured dashboard
- Add, edit, or remove books
- Toggle book availability
- View and manage customer orders and rentals

---

## 🛠️ Tech Stack

| Layer       | Technology             |
|-------------|------------------------|
| Frontend    | HTML, CSS, JavaScript  |
| Backend     | PHP                    |
| Database    | MySQL                  |
| Tools Used  | XAMPP / WAMP, VS Code  |

---

## 📁 Folder Structure

```
online_bookstore/
├── admin/             # Admin dashboard and tools
├── customer/          # Customer-facing pages and actions
├── includes/          # Database connection and config
|__ auth/              # login and logout
|__ payment            # payment of the customers
├── uploads/           # Book images
├── assets/            # CSS and images
|__ sql                # sql query
|__ hash.php           # admin password
├── index.php          # Entry point to the application
└── README.md          # Project documentation
```

---

## 💡 How to Run Locally

1. **Clone this repository**:
   ```bash
   git clone https://github.com/Abinithi13/online_bookstore.git
   ```

2. **Set up your environment**:
   - Use XAMPP, WAMP, or any local PHP server.
   - Place the project folder in the `htdocs` directory (if using XAMPP).

3. **Import the database**:
   - Open `phpMyAdmin` or any MySQL interface.
   - Create a new database (e.g., `online_bookstore`).
   - Import the provided `.sql` file into it.

4. **Update DB Config**:
   - Edit `/db/db_connect.php` with your MySQL username and password.

5. **Start the server** and visit:
   ```
   http://localhost/online_bookstore/
   ```

---

## 📌 Learning Highlights

- Database design using **ER models**, **normalization**, and **relationships**
- Implementation of **session-based login and role access**
- Use of **SQL queries** including JOINs and constraints
- Real-world **frontend-backend integration**

---

## 🎓 Project Purpose

This project was developed as part of an academic DBMS course to demonstrate how relational databases power dynamic applications. It provides practical exposure to full-stack development and showcases how databases, logic, and UI work together in an e-commerce setting.

---

## 🤝 Contributing

This project is open for learning and contributions. Feel free to fork it, report issues, or suggest improvements!

---

## 📬 Contact

Made with ❤️ by [Abinithi]  
📧 [abinithi.m13@gmail.com]  
🔗 [https://www.linkedin.com/in/abinithi-m-1a33b32a4] 
