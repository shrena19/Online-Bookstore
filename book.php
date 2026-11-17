
<?php
session_start();
include 'db.php';

// Check whether book ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = (int) $_GET['id'];

// Fetch book details
$stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$book = $result->fetch_assoc();

if (!$book) {
    die("Book not found!");
}

// Prepare image path
$image = $book['image'];

if (!empty($image)) {
    if (!filter_var($image, FILTER_VALIDATE_URL)) {
        if (strpos($image, 'uploads/') !== 0) {
            $image = 'uploads/' . $image;
        }
    }
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo e($book['title']); ?> - Online Bookstore</title>

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
        }

        header {
            background: #4b2e1e;
            color: white;
            padding: 20px 7%;
        }

        header h2 {
            font-size: 25px;
        }

        .container {
            max-width: 1050px;
            margin: 60px auto;
            padding: 30px;
            background: white;
            border-radius: 12px;
            display: flex;
            gap: 50px;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        .book-image {
            flex: 1;
            text-align: center;
        }

        .book-image img {
            width: 100%;
            max-width: 320px;
            height: 420px;
            object-fit: contain;
            border-radius: 8px;
        }

        .book-info {
            flex: 1.3;
        }

        .book-info h1 {
            font-size: 32px;
            margin-bottom: 15px;
        }

        .author {
            color: #777;
            font-size: 17px;
            margin-bottom: 20px;
        }

        .price {
            color: #7a4a2e;
            font-size: 26px;
            font-weight: bold;
            margin-bottom: 25px;
        }

        .description {
            font-size: 16px;
            line-height: 1.8;
            color: #555;
            margin-bottom: 30px;
        }

        .back-btn {
            display: inline-block;
            background: #7a4a2e;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 6px;
        }

        .back-btn:hover {
            background: #4b2e1e;
        }

        footer {
            background: #4b2e1e;
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: 60px;
        }

        @media(max-width: 700px) {
            .container {
                flex-direction: column;
                margin: 25px 15px;
                gap: 25px;
            }

            .book-info h1 {
                font-size: 25px;
            }
        }
    </style>
</head>

<body>

<header>
    <h2>Online Bookstore</h2>
</header>

<div class="container">

    <div class="book-image">
        <?php if (!empty($image)): ?>
            <img src="<?php echo e($image); ?>"
                 alt="<?php echo e($book['title']); ?>">
        <?php else: ?>
            <p>Book image not available</p>
        <?php endif; ?>
    </div>

    <div class="book-info">

        <h1><?php echo e($book['title']); ?></h1>

        <p class="author">
            By <?php echo e($book['author']); ?>
        </p>

        <p class="price">
            NPR <?php echo number_format((float)$book['price'], 2); ?>
        </p>

        <h3>Book Description</h3>

        <p class="description">
            <?php echo nl2br(e($book['description'])); ?>
        </p>

        <a href="index.php" class="back-btn">
            ← Back to Books
        </a>

    </div>

</div>

<footer>
    <p>© 2026 Online Bookstore. All Rights Reserved.</p>
</footer>

</body>
</html>
