
<?php
session_start();
$conn = new mysqli("localhost", "root", "", "onlinebookstore");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Fetch featured books dynamically from database
$result = $conn->query("SELECT * FROM books WHERE featured = 1 ORDER BY id DESC");

$books = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Online Bookstore</title>
<link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">

<style>
body {
    margin:0;
    font-family:Poppins,sans-serif;
    background:#f7f1e8;
    color:#4b2e1e;
}

.header {
    background:#e6d5c3;
    padding-bottom:60px;
}

nav {
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:20px 8%;
}

.nav-links li {
    display:inline-block;
    margin:0 15px;
}

.nav-links a {
    text-decoration:none;
    color:#4b2e1e;
    font-weight:500;
}

.nav-links a:hover {
    color:#c9a14a;
}

.main {
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:50px 8%;
}

.main-tag span {
    color:#7a4a2e;
}

.main-btn {
    display:inline-block;
    background:#7a4a2e;
    color:#fff;
    padding:10px 25px;
    border-radius:25px;
    text-decoration:none;
}

.services,.featured {
    padding:70px 8%;
}

.service-box,.featured-container {
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(150px,1fr));
    gap:25px;
}

.service-card,.book-card {
    background:#fffaf3;
    padding:25px;
    border-radius:15px;
    text-align:center;
    box-shadow:0 6px 18px rgba(75,46,30,.15);
}

.service-card i {
    font-size:35px;
    color:#7a4a2e;
}

.book-card img {
    width:120px;
    height:auto;
}

.price {
    color:#7a4a2e;
    font-weight:bold;
}

.book-btn {
    display:inline-block;
    margin-top:10px;
    padding:8px 20px;
    background:#7a4a2e;
    color:#fff;
    border-radius:20px;
    text-decoration:none;
}

.about {
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:70px 8%;
}

.about-img img {
    width:400px;
    border-radius:15px;
}

.about-text {
    max-width:600px;
}

.about-btn {
    display:inline-block;
    margin-top:15px;
    padding:10px 25px;
    background:#7a4a2e;
    color:#fff;
    border-radius:25px;
    text-decoration:none;
}

.center-text {
    text-align:center;
    margin-bottom:40px;
}

@media(max-width:900px) {
    .main,.about {
        flex-direction:column;
        text-align:center;
    }

    .about-img img {
        width:100%;
        margin-bottom:20px;
    }
}
</style>
</head>

<body>

<section class="header">
<nav>
    <div class="logo">
        <img src="img/logo.png" alt="Bookstore Logo" width="180">
    </div>

    <ul class="nav-links">
        <li><a href="#">Home</a></li>
        <li><a href="login.php">Login</a></li>
        <li><a href="admin-login.php">Admin</a></li>
        <li><a href="cart.php">Cart (<?= array_sum($_SESSION['cart']) ?>)</a></li>
        <a href="logout.php" class="btn-logout">Logout</a>
    </ul>
</nav>

<div class="main">
    <div class="main-tag">
        <h1>Welcome to <br><span>Book Store</span></h1>
        <p>Find the perfect book for every moment easy, fast, and affordable.</p>
        <a href="#whyShop" class="main-btn">Why Shop With Us</a>
    </div>

    <div class="main-img">
        <img src="img/book self.png" alt="Bookshelf" width="300">
    </div>
</div>
</section>

<section class="services" id="whyShop">
<div class="service-box">

    <div class="service-card">
        <i class='bx bxs-truck'></i>
        <h3>Fast Delivery</h3>
        <p>Get your books delivered quickly and safely to your doorstep.</p>
    </div>

    <div class="service-card">
        <i class='bx bxs-headphone'></i>
        <h3>24/7 Support</h3>
        <p>Our team is always ready to assist you anytime, anywhere.</p>
    </div>

    <div class="service-card">
        <i class='bx bxs-discount'></i>
        <h3>Best Deals</h3>
        <p>Enjoy exclusive offers and discounts on your favorite titles.</p>
    </div>

    <div class="service-card">
        <i class='bx bxs-lock-alt'></i>
        <h3>Secure Payment</h3>
        <p>Your payment is safe with our secure transaction system.</p>
    </div>

</div>
</section>

<section class="about" id="about">
<div class="about-img">
    <img src="img/book about.png" alt="About Bookstore">
</div>

<div class="about-text">
    <h2>About Our Store</h2>
    <p>We are passionate about books and readers. Our mission is to make reading easy, accessible, and enjoyable for everyone. From fiction to academic titles, you’ll find everything you need right here.</p>
    <a href="#" class="about-btn">Read More</a>
</div>
</section>

<section class="featured" id="featured">
    <div class="center-text">
        <h2>Featured <span>Books</span></h2>
        <p>Explore our top picks and bestsellers this month</p>
    </div>

    <div class="featured-container">
        <?php foreach ($books as $book): ?>
        <div class="book-card">

            <img src="<?= htmlspecialchars($book['image']) ?>"
                 alt="<?= htmlspecialchars($book['title']) ?>">

            <h3><?= htmlspecialchars($book['title']) ?></h3>

            <p>by <?= htmlspecialchars($book['author']) ?></p>

            <p class="price">
                NRP <?= number_format($book['price'],0) ?>
            </p>

            <a href="book.php?id=<?= $book['id'] ?>" class="book-btn">
                View Details
            </a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="add_to_cart.php?book_id=<?= $book['id'] ?>" class="book-btn">
                    Add to Cart
                </a>
            <?php else: ?>
                <a href="login.php?message=login_first" class="book-btn">
                    Login to Add to Cart
                </a>
            <?php endif; ?>

        </div>
        <?php endforeach; ?>
    </div>
</section>

<?php include 'footer.php'; ?>

</body>
</html>
