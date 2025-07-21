<?php
// Database connection (embedded)
$host = 'localhost';
$dbname = 'game_store';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Get search term if exists
$searchTerm = $_GET['search'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Store</title>
    <link rel="stylesheet" href="../CSS/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <!-- Hamburger Menu -->
    <div class="menu-container">
        <div class="hamburger-menu" onclick="toggleMenu()">
            <div class="bar"></div>
            <div class="bar"></div>
            <div class="bar"></div>
        </div>
        <div class="menu-content" id="menuContent">
            <a href="home.php">Home</a>
            <a href="products.php">Products</a>
            <a href="http://localhost/GAME_STORE/HTML/logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="container">
        <h1 class="title">Game Store</h1>

        <div class="search-section">
            <input type="text" id="searchInput" placeholder="Search games..." value="<?= htmlspecialchars($searchTerm) ?>">
            <button class="search-btn" onclick="handleSearch()">
                <i class="fas fa-search"></i>
            </button>
        </div>

        <div class="button-section">
            <button onclick="goToPage('products.php?category=all')">All Games</button>
            <button onclick="goToPage('products.php?category=Action')">Action</button>
            <button onclick="goToPage('products.php?category=Adventure')">Adventure</button>
            <button onclick="goToPage('products.php?category=Strategy')">Strategy</button>
        </div>
    </div>

    <div class="sr-only">Game Store background image</div>
    <script src="../JS/home.js"></script>
    <script>
        function handleSearch() {
            const searchTerm = document.getElementById('searchInput').value.trim();
            if (searchTerm) {
                document.querySelector('.container').style.animation = 'fadeOut 0.5s ease-out forwards';
                setTimeout(() => {
                    window.location.href = `products.php?search=${encodeURIComponent(searchTerm)}`;
                }, 500);
            } else {
                const searchSection = document.querySelector('.search-section');
                searchSection.style.animation = 'none';
                setTimeout(() => {
                    searchSection.style.animation = 'shake 0.5s';
                }, 10);
            }
        }

        function goToPage(url) {
            const button = event.currentTarget;
            button.style.transform = 'scale(0.95)';
            button.style.opacity = '0.8';
            document.querySelector('.container').style.animation = 'fadeOut 0.5s ease-out forwards';
            setTimeout(() => {
                window.location.href = url;
            }, 500);
        }
    </script>
</body>
</html>