<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database configuration
$host = "localhost";
$user = "root";
$pass = "";
$db = "game_store";

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signUp'])) {
    // Verify passwords match
    if ($_POST['password'] !== $_POST['confirmPassword']) {
        $_SESSION['error'] = "Passwords don't match!";
        header("Location: register.php");
        exit();
    }

    // Get form data
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $raw_password = $_POST['password']; // Store the actual password
    
    // Check if email exists
    $check = $conn->prepare("SELECT id FROM users_data WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();
    
    if ($check->num_rows > 0) {
        $_SESSION['error'] = "Email already exists!";
        header("Location: register.php");
        exit();
    }

    // Insert new user with actual password
    $stmt = $conn->prepare("INSERT INTO users_data (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $raw_password);
    
    if ($stmt->execute()) {
        $_SESSION['email'] = $email;
        header("Location: home.php");
        exit();
    } else {
        $_SESSION['error'] = "Registration failed: " . $stmt->error;
        header("Location: register.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register - Game Store</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="../CSS/register.css" />
</head>
<body>

  <div class="game-logo">GAME STORE</div>
  
  <div class="form-wrapper">
    <div class="register-container">
      <h2>Create Account</h2>
      
      <?php if (isset($_SESSION['error'])): ?>
        <div class="error-message" style="color:red; padding:10px; margin:10px 0; background:#ffeeee;">
          <?php 
            echo $_SESSION['error']; 
            unset($_SESSION['error']);
          ?>
        </div>
      <?php endif; ?>
      
      <form method="POST" action="register.php">
        <div class="input-group">
          <label for="name">Full Name</label>
          <input type="text" id="name" name="name" placeholder="Your full name" required
                 value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
          <i class="fas fa-user icon"></i>
        </div>
        
        <div class="input-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" placeholder="Your email" required
                 value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
          <i class="fas fa-envelope icon"></i>
        </div>
        
        <div class="input-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="Create password" required>
          <i class="fas fa-lock icon"></i>
        </div>
        
        <div class="input-group">
          <label for="confirmPassword">Confirm Password</label>
          <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm password" required>
          <i class="fas fa-lock icon"></i>
        </div>
        
        <button type="submit" name="signUp" class="register-btn">Register</button>
        
        <p class="login-link">Already have an account? <a href="login.php">Login</a></p>
      </form>
    </div>
  </div>
  <script src="../JS/register.js"></script> 
</body>
</html>