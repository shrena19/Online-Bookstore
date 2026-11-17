<?php
session_start();
require_once 'db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["admin_email"] ?? "");
    $password = $_POST["admin_password"] ?? "";

    if ($email === "" || $password === "") {

        $error = "All fields are required.";

    } else {

        $stmt = $conn->prepare(
            "SELECT id, name, email, password, role
             FROM users
             WHERE email = ? AND role = 'admin'
             LIMIT 1"
        );

        if (!$stmt) {
            die("Database query failed: " . $conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();

        if ($admin) {

            if (password_verify($password, $admin["password"])) {

                session_regenerate_id(true);

                $_SESSION["admin_id"] = $admin["id"];
                $_SESSION["admin_username"] = $admin["name"];
                $_SESSION["admin_email"] = $admin["email"];
                $_SESSION["role"] = "admin";

                $stmt->close();

                header("Location: /Online-Bookstore/admin_dashboard.php");
exit();
            } else {

                $error = "Incorrect password.";

            }

        } else {

            $error = "Admin account not found.";

        }

        $stmt->close();
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Login</title>

<style>
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: 'Poppins', sans-serif;
    background: #f2f2f2;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.navbar {
    position: absolute;
    top: 0;
    width: 100%;
    background: #7a4a2e;
    padding: 15px 0;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.navbar img {
    width: 140px;
}

.auth-box {
    background: #fff;
    padding: 40px 35px;
    border-radius: 15px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    width: 380px;
    text-align: center;
}

.auth-box h2 {
    margin-bottom: 25px;
    color: #7a4a2e;
}

.auth-box input {
    width: 100%;
    padding: 12px 15px;
    margin-bottom: 20px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 15px;
}

.auth-box input:focus {
    border-color: #7a4a2e;
    outline: none;
}

.btn {
    background: #7a4a2e;
    color: #fff;
    padding: 12px;
    width: 100%;
    border: none;
    border-radius: 25px;
    font-size: 16px;
    cursor: pointer;
    transition: background 0.3s;
}

.btn:hover {
    background: #5c3620;
}

.error {
    background: #f8d7da;
    color: #721c24;
    padding: 10px;
    margin-bottom: 20px;
    border-radius: 5px;
    font-size: 14px;
}

.auth-box p {
    margin-top: 15px;
}

.auth-box a {
    text-decoration: none;
    color: #7a4a2e;
    font-weight: 500;
}

.auth-box a:hover {
    text-decoration: underline;
}

.auth-page {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    height: 100%;
    padding-top: 60px;
}
</style>
</head>

<body>

<div class="navbar">
    <img src="logo.png" alt="Admin Panel">
</div>

<div class="auth-page">
    <div class="auth-box">

        <h2>Admin Login</h2>

        <?php if ($error): ?>
            <div class="error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <input
                type="email"
                name="admin_email"
                placeholder="Admin Email"
                required
            >

            <input
                type="password"
                name="admin_password"
                placeholder="Password"
                required
            >

            <button type="submit" class="btn">Login</button>

        </form>

        <p>
            <a href="index.php">← Back to Website</a>
        </p>

    </div>
</div>

</body>
</html>
