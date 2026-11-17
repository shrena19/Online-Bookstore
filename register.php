
<?php
session_start();
include 'db.php';

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $address = trim($_POST["address"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";

    if (
        $name === "" || $email === "" || $phone === "" ||
        $address === "" || $password === "" || $confirmPassword === ""
    ) {
        $message = "Please fill in all fields.";
        $messageType = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $messageType = "error";

    } elseif (!preg_match('/^[0-9+\-\s()]{7,20}$/', $phone)) {
        $message = "Please enter a valid phone number.";
        $messageType = "error";

    } elseif ($password !== $confirmPassword) {
        $message = "Passwords do not match.";
        $messageType = "error";

    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters.";
        $messageType = "error";

    } else {

        // Check whether the email already exists
        $check = $conn->prepare(
            "SELECT id FROM users WHERE email = ?"
        );

        $check->bind_param("s", $email);
        $check->execute();

        $result = $check->get_result();

        if ($result->num_rows > 0) {

            $message = "This email is already registered.";
            $messageType = "error";

        } else {

            // Securely hash the password
            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            // Every public registration receives the customer role
            $role = "customer";

            // Insert customer information into database
            $stmt = $conn->prepare(
                "INSERT INTO users
                (name, email, phone, address, password, role, created_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW())"
            );

            $stmt->bind_param(
                "ssssss",
                $name,
                $email,
                $phone,
                $address,
                $hashedPassword,
                $role
            );

            if ($stmt->execute()) {
                header("Location: login.php?registered=1");
                exit();
            } else {
                $message = "Registration failed. Please try again.";
                $messageType = "error";
            }

            $stmt->close();
        }

        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Account | Online Bookstore</title>

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
    max-width: 440px;
    background: #fff;
    padding: 38px;
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
    margin-bottom: 25px;
}

.form-group {
    margin-bottom: 17px;
}

label {
    display: block;
    margin-bottom: 7px;
    font-size: 14px;
    font-weight: 600;
}

input,
textarea {
    width: 100%;
    padding: 13px 14px;
    border: 1px solid #e6d5c3;
    border-radius: 7px;
    font-size: 14px;
    outline: none;
    background: #fffdfb;
    font-family: Arial, sans-serif;
}

textarea {
    resize: vertical;
    min-height: 75px;
}

input:focus,
textarea:focus {
    border-color: #7a4a2e;
    box-shadow: 0 0 0 2px rgba(122, 74, 46, 0.10);
}

/* Password show/hide */
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
    border: none;
    font-size: 12px;
    font-weight: bold;
    cursor: pointer;
}

.toggle-password:hover {
    background: transparent;
    color: #4b2e1e;
}

/* Create account button */
.submit-button {
    width: 100%;
    padding: 14px;
    background: #7a4a2e;
    color: white;
    border: none;
    border-radius: 7px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    margin-top: 8px;
    transition: 0.2s;
}

.submit-button:hover {
    background: #4b2e1e;
}

.message {
    padding: 11px;
    border-radius: 6px;
    font-size: 14px;
    margin-bottom: 17px;
    text-align: center;
}

.error {
    background: #fce8e6;
    color: #a12622;
}

.footer-text {
    text-align: center;
    margin-top: 22px;
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
    margin-top: 17px;
    color: #8b776b;
    font-size: 13px;
    text-decoration: none;
}

.back-home:hover {
    color: #7a4a2e;
}

@media (max-width: 480px) {
    .auth-container {
        padding: 27px 22px;
    }
}
</style>
</head>

<body>

<div class="auth-container">

    <div class="logo">Online Bookstore</div>

    <h2>Create Account</h2>
    <p class="subtitle">Join us and discover your next favourite book.</p>

    <?php if ($message !== ""): ?>
        <div class="message <?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">

        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name"
                   placeholder="Enter your full name"
                   value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                   required>
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email"
                   placeholder="Enter your email"
                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                   required>
        </div>

        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone"
                   placeholder="Enter your phone number"
                   value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                   required>
        </div>

        <div class="form-group">
            <label for="address">Address</label>
            <textarea id="address" name="address"
                      placeholder="Enter your full address"
                      required><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label for="password">Password</label>

            <div class="password-box">
                <input type="password" id="password" name="password"
                       placeholder="Create a password" required>

                <button type="button"
                        class="toggle-password"
                        onclick="togglePassword('password', 'eye1')">
                    <span id="eye1">Show</span>
                </button>
            </div>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>

            <div class="password-box">
                <input type="password" id="confirm_password"
                       name="confirm_password"
                       placeholder="Confirm your password" required>

                <button type="button"
                        class="toggle-password"
                        onclick="togglePassword('confirm_password', 'eye2')">
                    <span id="eye2">Show</span>
                </button>
            </div>
        </div>

        <button type="submit" class="submit-button">
            Create Account
        </button>

    </form>

    <p class="footer-text">
        Already have an account?
        <a href="login.php">Login</a>
    </p>

    <a href="index.php" class="back-home">← Back to Home</a>

</div>

<script>
function togglePassword(inputId, iconId) {

    const password = document.getElementById(inputId);
    const icon = document.getElementById(iconId);

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
