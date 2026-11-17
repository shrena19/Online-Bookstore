
<?php
session_start();
include 'db.php';

$error = "";

if (isset($_GET['registered'])) {
    $successMessage = "Registration successful! Please login.";
} else {
    $successMessage = "";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {
        $error = "Please enter your email and password.";
    } else {

        $stmt = $conn->prepare(
            "SELECT id, name, email, password, role FROM users WHERE email = ?"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {

                session_regenerate_id(true);

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] === 'admin') {
                    header("Location: admin/admin_dashboard.php");
                    exit();
                } elseif ($user['role'] === 'customer') {
                    header("Location: index.php");
                    exit();
                } else {
                    $error = "Your account role is not valid. Please contact the administrator.";
                    session_unset();
                    session_destroy();
                }

            } else {
                $error = "Incorrect email or password.";
            }

        } else {
            $error = "Incorrect email or password.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login | Online Bookstore</title>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background: #f7f1e8;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 25px;
    color: #4b2e1e;
}

.auth-container {
    width: 100%;
    max-width: 420px;
    background: #fff;
    padding: 40px 38px;
    border-radius: 14px;
    box-shadow: 0 8px 30px rgba(75, 46, 30, 0.10);
}

.logo {
    text-align: center;
    font-size: 27px;
    font-weight: bold;
    color: #7a4a2e;
    margin-bottom: 10px;
}

h2 {
    text-align: center;
    font-size: 25px;
    margin-bottom: 8px;
}

.subtitle {
    text-align: center;
    color: #8b776b;
    font-size: 14px;
    margin-bottom: 27px;
}

.form-group {
    margin-bottom: 20px;
}

label {
    display: block;
    margin-bottom: 8px;
    font-size: 14px;
    font-weight: 600;
}

input {
    width: 100%;
    padding: 14px;
    border: 1px solid #e6d5c3;
    border-radius: 7px;
    font-size: 14px;
    outline: none;
    background: #fffdfb;
}

input:focus {
    border-color: #7a4a2e;
    box-shadow: 0 0 0 2px rgba(122, 74, 46, 0.10);
}

/* Password toggle */
.password-box {
    position: relative;
}

.password-box input {
    padding-right: 65px;
}

.toggle-password {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    width: auto;
    margin: 0;
    padding: 5px;
    background: transparent;
    color: #7a4a2e;
    font-size: 12px;
    border: none;
    cursor: pointer;
}

.toggle-password:hover {
    background: transparent;
    color: #4b2e1e;
}

.login-button {
    width: 100%;
    padding: 14px;
    background: #7a4a2e;
    color: white;
    border: none;
    border-radius: 7px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    margin-top: 7px;
    transition: 0.2s;
}

.login-button:hover {
    background: #4b2e1e;
}

.message {
    padding: 11px;
    border-radius: 6px;
    font-size: 14px;
    margin-bottom: 18px;
    text-align: center;
}

.error {
    background: #fce8e6;
    color: #a12622;
}

.success {
    background: #e8f5e9;
    color: #286738;
}

.footer-text {
    text-align: center;
    margin-top: 23px;
    font-size: 14px;
    color: #75665e;
}

.footer-text a {
    color: #7a4a2e;
    font-weight: bold;
    text-decoration: none;
}

.footer-text a:hover {
    text-decoration: underline;
}

.back-home {
    display: block;
    text-align: center;
    margin-top: 18px;
    color: #8b776b;
    font-size: 13px;
    text-decoration: none;
}

.back-home:hover {
    color: #7a4a2e;
}

@media (max-width: 480px) {
    .auth-container {
        padding: 28px 22px;
    }
}
</style>
</head>

<body>

<div class="auth-container">

    <div class="logo">Online Bookstore</div>

    <h2>Welcome Back!</h2>
    <p class="subtitle">Login to continue exploring your favourite books.</p>

    <?php if ($successMessage !== ""): ?>
        <div class="message success">
            <?php echo htmlspecialchars($successMessage); ?>
        </div>
    <?php endif; ?>

    <?php if ($error !== ""): ?>
        <div class="message error">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email"
                   placeholder="Enter your email"
                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                   required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>

            <div class="password-box">
                <input type="password" id="password" name="password"
                       placeholder="Enter your password" required>

                <button type="button"
                        class="toggle-password"
                        onclick="togglePassword()">
                    <span id="eyeIcon">Show</span>
                </button>
            </div>
        </div>

        <button type="submit" class="login-button">Login</button>

    </form>

    <p class="footer-text">
        Don't have an account?
        <a href="register.php">Register</a>
    </p>

    <a href="index.php" class="back-home">← Back to Home</a>

</div>

<script>
function togglePassword() {
    const password = document.getElementById("password");
    const icon = document.getElementById("eyeIcon");

    if (password.type === "password") {
        password.type = "text";
        icon.textContent = "Hide";
    } else {
        password.type = "password";
        icon.textContent = "Show";
    }
}
</script>

</body>
</html>
