<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];

    // Database connection
    $conn = new mysqli('127.0.0.1', 'root', '', 'bionexg');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the user exists
    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        // User exists, send reset request to admin
        $sql = "INSERT INTO password_reset_requests (username, status) VALUES ('$username', 'pending')";
        if ($conn->query($sql) === TRUE) {
            // Send notification to admin (For simplicity, we'll just show a message)
            echo "Your password reset request has been sent to the admin for approval.";

            // Send email to admin for approval (optional)
            $adminEmail = "admin@example.com";  // Replace with the actual admin email
            $subject = "Password Reset Request for User: $username";
            $message = "User $username has requested a password reset. Please review and approve or reject the request.";
            $headers = "From: no-reply@example.com";  // Replace with the sender email

            // Uncomment the following line to send the email to the admin
            // mail($adminEmail, $subject, $message, $headers);
        } else {
            echo "Error submitting password reset request: " . $conn->error;
        }
    } else {
        echo "User does not exist.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="styles-forgot-password.css">  <!-- Link to your CSS file -->
</head>
<body>
    <div class="container">
        <h2>Forgot Password</h2>
        <form action="forgot-password.php" method="POST">
            <input type="text" name="username" placeholder="Enter your username" required>
            <button type="submit">Submit Request</button>
        </form>

        <!-- Back to Login Link -->
        <div class="back-to-login">
            <a href="login.php">Back to Login</a>
        </div>
    </div>
</body>
</html>
