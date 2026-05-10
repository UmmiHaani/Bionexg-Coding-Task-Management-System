<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = md5($_POST['password']);  // MD5 encryption (consider using password_hash for better security)
    
    // Database connection
    $conn = new mysqli('127.0.0.1', 'root', '', 'bionexg');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['user_id'] = $user['id'];

        if ($user['role'] == 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: employee_dashboard.php");
        }
    } else {
        $error_message = "Invalid username or password.";
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <h1>Login</h1>
        <form action="login.php" method="POST" class="login-form">
            <input type="text" name="username" placeholder="Username" required class="input-field">
            <input type="password" name="password" placeholder="Password" required class="input-field">
            <div class="forgot-password">
            <a href="forgot-password.php">Forgot your password?</a>
        </div>
            <button type="submit" class="btn">Login</button>
        </form>

        <?php if (isset($error_message)) { echo "<p class='error-message'>$error_message</p>"; } ?>

        <div class="register-option">
            <p>Are you an employee? <a href="register_employee.php" class="register-link">Register here</a> for new employee</p>
        </div>
    </div>
</body>
</html>
