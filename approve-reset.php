<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: login.php"); // Redirect to login if not admin
    exit();
}

if (isset($_GET['id']) && isset($_GET['action'])) {
    $request_id = $_GET['id'];
    $action = $_GET['action'];

    // Database connection
    $conn = new mysqli('127.0.0.1', 'root', '', 'bionexg');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if ($action == 'approve') {
        // Get the username for the reset request
        $sql = "SELECT * FROM password_reset_requests WHERE id='$request_id'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $username = $row['username'];

            // Update password in the users table (this is an example, ideally you should generate a random password)
            $new_password = md5("new_password123");  // Example new password (in production, you'd generate this)
            $sql = "UPDATE users SET password='$new_password' WHERE username='$username'";

            if ($conn->query($sql) === TRUE) {
                // Mark the reset request as approved
                $conn->query("UPDATE password_reset_requests SET status='approved' WHERE id='$request_id'");
                echo "Password reset approved. The new password has been set.";
            }
        }
    } elseif ($action == 'reject') {
        // Mark the reset request as rejected
        $conn->query("UPDATE password_reset_requests SET status='rejected' WHERE id='$request_id'");
        echo "Password reset request rejected.";
    }

    $conn->close();
} else {
    echo "Invalid request.";
}
?>
