<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: login.php"); // Redirect to login if not admin
    exit();
}

echo "<div class='container'><h1>Admin Dashboard</h1>";
echo "Welcome, Bionexg Admin. <a href='logout.php' class='btn logout-btn'>Logout</a>";

// Database connection
$conn = new mysqli('127.0.0.1', 'root', '', 'bionexg');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle password reset request approval or rejection
if (isset($_GET['id']) && isset($_GET['action'])) {
    $request_id = $_GET['id'];
    $action = $_GET['action'];

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
}

// Filter task query
$task_status = isset($_GET['task_status']) ? $_GET['task_status'] : '';
$task_priority = isset($_GET['task_priority']) ? $_GET['task_priority'] : '';
$task_sql = "SELECT * FROM tasks WHERE 1";

// Add filtering conditions if set
if ($task_status != '') {
    $task_sql .= " AND status = '$task_status'";
}
if ($task_priority != '') {
    $task_sql .= " AND priority = '$task_priority'";
}
$tasks_result = $conn->query($task_sql);

// Filter password reset requests query
$password_status = isset($_GET['password_status']) ? $_GET['password_status'] : 'pending';
$password_reset_sql = "SELECT * FROM password_reset_requests WHERE status = '$password_status'";
$password_reset_result = $conn->query($password_reset_sql);
?>

<div class="dashboard">
    <!-- Add New Task Button -->
    <a href="add_task.php" class="btn add-task-btn">Add New Task</a>
    <link rel="stylesheet" href="style-admin.css">

    <!-- Filter Tasks -->
    <h2>Filter Tasks</h2>
    <form method="GET" action="admin_dashboard.php">
        <label for="task_status">Status:</label>
        <select name="task_status" id="task_status">
            <option value="">--Select Status--</option>
            <option value="pending" <?php echo $task_status == 'pending' ? 'selected' : ''; ?>>Pending</option>
            <option value="in_progress" <?php echo $task_status == 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
            <option value="completed" <?php echo $task_status == 'completed' ? 'selected' : ''; ?>>Completed</option>
        </select>

        <label for="task_priority">Priority:</label>
        <select name="task_priority" id="task_priority">
            <option value="">--Select Priority--</option>
            <option value="low" <?php echo $task_priority == 'low' ? 'selected' : ''; ?>>Low</option>
            <option value="medium" <?php echo $task_priority == 'medium' ? 'selected' : ''; ?>>Medium</option>
            <option value="high" <?php echo $task_priority == 'high' ? 'selected' : ''; ?>>High</option>
        </select>

        <button type="submit" class="btn filter-btn">Filter</button>
    </form>

    <!-- Task List -->
    <h2>Task List</h2>
    <table class="task-table">
        <tr>
            <th>Task Name</th>
            <th>Description</th>
            <th>Status</th>
            <th>Priority</th>
            <th>Deadline</th>
            <th>Assigned to</th>
            <th>Actions</th>
        </tr>
        <?php while ($task = $tasks_result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $task['task_name']; ?></td>
                <td><?php echo $task['description']; ?></td>
                <td><?php echo $task['status']; ?></td>
                <td><?php echo $task['priority']; ?></td>
                <td><?php echo $task['deadline']; ?></td>
                <td><?php echo $task['assigned_to']; ?></td>
                <td><a href="manage_task.php?task_id=<?php echo $task['id']; ?>" class="btn manage-btn">Manage</a></td>
            </tr>
        <?php } ?>
    </table>
    
    <!-- Password Reset Requests -->
    <h2>Password Reset Requests</h2>
    <?php if ($password_reset_result->num_rows > 0): ?>
        <table class="password-reset-table">
            <tr>
                <th>Username</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $password_reset_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['username']; ?></td>
                    <td>
                        <a href="admin_dashboard.php?id=<?php echo $row['id']; ?>&action=approve" class="btn approve-btn">Approve</a> | 
                        <a href="admin_dashboard.php?id=<?php echo $row['id']; ?>&action=reject" class="btn reject-btn">Reject</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No password reset requests.</p>
    <?php endif; ?>
</div>

<?php
$conn->close();
?>
