
<?php
session_start();
require_once 'db.php';

// Admin authentication
if (
    !isset($_SESSION['admin_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
) {
    header("Location: admin-login.php");
    exit();
}

// Get total books
$bookResult = $conn->query("SELECT COUNT(*) AS total FROM books");
$totalBooks = 0;

if ($bookResult) {
    $totalBooks = $bookResult->fetch_assoc()['total'];
}

// Get total registered customers
$userResult = $conn->query(
    "SELECT COUNT(*) AS total FROM users WHERE role = 'customer'"
);
$totalUsers = 0;

if ($userResult) {
    $totalUsers = $userResult->fetch_assoc()['total'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Admin Dashboard | Online Bookstore</title>

<style>

/* General Styling */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background: #f7f1e8;
    color: #4b2e1e;
}

/* Sidebar */
.sidebar {
    width: 250px;
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    background: #4b2e1e;
    padding: 30px 20px;
    color: white;
}

.sidebar h2 {
    font-size: 22px;
    margin-bottom: 40px;
    text-align: center;
}

.sidebar a {
    display: block;
    color: #f7f1e8;
    text-decoration: none;
    padding: 14px 15px;
    border-radius: 7px;
    margin-bottom: 10px;
    transition: 0.2s;
}

.sidebar a:hover {
    background: #7a4a2e;
}

/* Main Content */
.main-content {
    margin-left: 250px;
    padding: 35px;
}

/* Topbar */
.topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 35px;
}

.topbar h1 {
    font-size: 27px;
}

.logout {
    background: #7a4a2e;
    color: white;
    text-decoration: none;
    padding: 11px 20px;
    border-radius: 6px;
}

.logout:hover {
    background: #4b2e1e;
}

/* Welcome Section */
.welcome {
    background: white;
    padding: 25px;
    border-radius: 12px;
    margin-bottom: 30px;
    box-shadow: 0 4px 15px rgba(75,46,30,0.06);
}

.welcome h2 {
    margin-bottom: 8px;
    color: #7a4a2e;
}

/* Dashboard Cards */
.cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 22px;
}

.card {
    background: white;
    padding: 28px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(75,46,30,0.06);
}

.card h3 {
    color: #8b776b;
    font-size: 15px;
    margin-bottom: 15px;
}

.card p {
    font-size: 32px;
    font-weight: bold;
    color: #7a4a2e;
}

/* Quick Actions */
.section-title {
    margin: 35px 0 18px;
}

.quick-links {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 15px;
}

.quick-links a {
    display: block;
    background: #e6d5c3;
    color: #4b2e1e;
    padding: 20px;
    border-radius: 9px;
    text-decoration: none;
    font-weight: bold;
    transition: 0.2s;
}

.quick-links a:hover {
    background: #d8bea6;
}

/* Responsive Design */
@media(max-width: 700px) {

    .sidebar {
        position: relative;
        width: 100%;
        height: auto;
    }

    .sidebar h2 {
        margin-bottom: 20px;
    }

    .main-content {
        margin-left: 0;
        padding: 20px;
    }
}

</style>
</head>

<body>

<!-- Sidebar -->
<div class="sidebar">

    <h2>Bookstore Admin</h2>

    <a href="admin_dashboard.php">Dashboard</a>

    <a href="manage_books.php">Manage Books</a>

    <a href="add_book.php">Add New Book</a>

    <a href="manage_users.php">Manage Users</a>

    <a href="manage_orders.php">Manage Orders</a>

    <a href="index.php">Visit Website</a>

</div>


<!-- Main Dashboard -->
<div class="main-content">

    <!-- Topbar -->
    <div class="topbar">

        <h1>Admin Dashboard</h1>

        <a href="admin-logout.php" class="logout">Logout</a>

    </div>


    <!-- Welcome Message -->
    <div class="welcome">

        <h2>
            Welcome,
            <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Administrator'); ?>!
        </h2>

        <p>Manage your online bookstore from here.</p>

    </div>


    <!-- Dashboard Statistics -->
    <div class="cards">

        <div class="card">

            <h3>Total Books</h3>

            <p><?php echo $totalBooks; ?></p>

        </div>


        <div class="card">

            <h3>Registered Customers</h3>

            <p><?php echo $totalUsers; ?></p>

        </div>

    </div>


    <!-- Quick Actions -->
    <h2 class="section-title">Quick Actions</h2>

    <div class="quick-links">

        <a href="add_book.php">+ Add New Book</a>

        <a href="manage_books.php">Manage Books</a>

        <a href="manage_users.php">View Customers</a>

        <a href="manage_orders.php">Manage Orders</a>

    </div>

</div>

</body>
</html>
