<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = md5($_POST['password']);  // MD5 encryption (consider using password_hash for better security)
    
    // Database connection
    $conn = new mysqli('127.0.0.1', 'root', '', 'bionexg');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if username already exists
    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        echo "Username already exists. Please choose a different username.";
    } else {
        // Insert new employee record
        $sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', 'employee')";
        if ($conn->query($sql) === TRUE) {
            echo "Registration successful! You can now <a href='login.php'>login</a>.";
        } else {
            echo "Error: " . $conn->error;
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Employee</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="register-container">
        <h1>Register as New Employee</h1>
        <form action="register_employee.php" method="POST" class="register-form">
            <input type="text" name="username" placeholder="Username" required class="input-field">
            <input type="password" name="password" placeholder="Password" required class="input-field">
            <button type="submit" class="btn">Register</button>
        </form>
    </div>
</body>
</html>
