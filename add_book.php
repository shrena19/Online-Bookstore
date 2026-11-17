
<?php
session_start();
require_once 'db.php';

// Admin authentication
if (
    !isset($_SESSION['admin_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
) {
    header("Location: /Online-Bookstore/admin-login.php");
    exit();
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $price = $_POST['price'] ?? '';
    $description = trim($_POST['description'] ?? '');
    $featured = isset($_POST['featured']) ? 1 : 0;

    $image_name = null;
    $uploaded_path = null;

    // Validate book information
    if (
        $title === "" ||
        $author === "" ||
        !is_numeric($price) ||
        $price < 0
    ) {
        $error = "Please enter valid book details.";
    } else {

        // Handle image upload
        if (
            isset($_FILES['image']) &&
            $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE
        ) {

            if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                $error = "Image upload failed. Please try again.";

            } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
                $error = "Image size must be less than 2 MB.";

            } else {

                $allowed_types = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/webp' => 'webp'
                ];

                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime = $finfo->file($_FILES['image']['tmp_name']);

                if (!isset($allowed_types[$mime])) {
                    $error = "Only JPG, PNG and WEBP images are allowed.";

                } else {

                    // Upload directory inside Online-Bookstore
                    $upload_dir = __DIR__ . '/uploads/';

                    if (
                        !is_dir($upload_dir) &&
                        !mkdir($upload_dir, 0755, true)
                    ) {
                        $error = "Unable to create uploads folder.";

                    } else {

                        $image_name = bin2hex(random_bytes(16))
                            . "." . $allowed_types[$mime];

                        $uploaded_path = $upload_dir . $image_name;

                        if (!move_uploaded_file(
                            $_FILES['image']['tmp_name'],
                            $uploaded_path
                        )) {
                            $error = "Unable to save the uploaded image.";
                            $image_name = null;
                            $uploaded_path = null;
                        }
                    }
                }
            }
        }

        // Insert book into database
        if ($error === "") {

            $stmt = $conn->prepare(
                "INSERT INTO books
                (title, author, price, image, description, featured)
                VALUES (?, ?, ?, ?, ?, ?)"
            );

            if (!$stmt) {
                $error = "Database error: " . $conn->error;

                if ($uploaded_path && file_exists($uploaded_path)) {
                    unlink($uploaded_path);
                }

            } else {

                $price = (float)$price;

                $stmt->bind_param(
                    "ssdssi",
                    $title,
                    $author,
                    $price,
                    $image_name,
                    $description,
                    $featured
                );

                if ($stmt->execute()) {
                    $success = "Book added successfully!";
                    $_POST = [];
                } else {
                    $error = "Database error: " . $stmt->error;

                    // Remove image if database insertion fails
                    if ($uploaded_path && file_exists($uploaded_path)) {
                        unlink($uploaded_path);
                    }
                }

                $stmt->close();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Add New Book | Online Bookstore</title>

<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background: #f7f1e8;
    color: #4b2e1e;
    padding: 30px 15px;
}

.container {
    max-width: 650px;
    margin: 20px auto;
    background: white;
    padding: 35px;
    border-radius: 12px;
    box-shadow: 0 4px 18px rgba(75,46,30,0.08);
}

h1 {
    margin-bottom: 10px;
    color: #4b2e1e;
}

.subtitle {
    color: #8b776b;
    margin-bottom: 25px;
}

.form-group {
    margin-bottom: 20px;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
}

input, textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #dfd0c1;
    border-radius: 7px;
    font-size: 15px;
    font-family: inherit;
}

input:focus, textarea:focus {
    outline: none;
    border-color: #7a4a2e;
}

textarea {
    min-height: 120px;
    resize: vertical;
}

.featured {
    display: flex;
    align-items: center;
    gap: 10px;
}

.featured input {
    width: auto;
}

.featured label {
    margin: 0;
}

button {
    width: 100%;
    padding: 14px;
    background: #7a4a2e;
    color: white;
    border: none;
    border-radius: 7px;
    font-size: 16px;
    cursor: pointer;
}

button:hover {
    background: #4b2e1e;
}

.message {
    padding: 12px;
    border-radius: 6px;
    margin-bottom: 20px;
}

.error {
    background: #ffe5e5;
    color: #a12626;
}

.success {
    background: #e4f5e8;
    color: #24743b;
}

.back {
    display: inline-block;
    margin-bottom: 20px;
    color: #7a4a2e;
    text-decoration: none;
}

.note {
    font-size: 12px;
    color: #8b776b;
    margin-top: 6px;
}

</style>
</head>

<body>

<div class="container">

<a href="admin_dashboard.php" class="back">
    ← Back to Dashboard
</a>

<h1>Add New Book</h1>

<p class="subtitle">
    Enter book information to add it to your bookstore.
</p>

<?php if ($error): ?>
    <div class="message error">
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="message success">
        <?php echo htmlspecialchars($success); ?>
        <br><br>
        <a href="manage_books.php">View all books</a>
    </div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">

    <div class="form-group">
        <label>Book Title</label>

        <input
            type="text"
            name="title"
            value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>"
            placeholder="Enter book title"
            required
        >
    </div>

    <div class="form-group">
        <label>Author</label>

        <input
            type="text"
            name="author"
            value="<?php echo htmlspecialchars($_POST['author'] ?? ''); ?>"
            placeholder="Enter author name"
            required
        >
    </div>

    <div class="form-group">
        <label>Price (NPR)</label>

        <input
            type="number"
            name="price"
            value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>"
            step="0.01"
            min="0"
            placeholder="Enter book price"
            required
        >
    </div>

    <div class="form-group">
        <label>Book Description</label>

        <textarea
            name="description"
            placeholder="Write book description"
        ><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
    </div>

    <div class="form-group">
        <label>Book Cover Image</label>

        <input
            type="file"
            name="image"
            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
        >

        <p class="note">
            Allowed: JPG, PNG, WEBP. Maximum size: 2 MB.
        </p>
    </div>

    <div class="form-group featured">

        <input
            type="checkbox"
            name="featured"
            id="featured"
            value="1"
            <?php echo isset($_POST['featured']) ? 'checked' : ''; ?>
        >

        <label for="featured">Mark as Featured Book</label>

    </div>

    <button type="submit">Add Book</button>

</form>

</div>

</body>
</html>
