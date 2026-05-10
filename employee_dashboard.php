<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'employee') {
    header("Location: login.php");
}

echo "<div class='container'><h1>Employee Dashboard</h1>";
echo "Welcome, Employee. <a href='logout.php' class='btn logout-btn'>Logout</a>";

$conn = new mysqli('127.0.0.1', 'root', '', 'bionexg');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch password reset status for the employee
$user_id = $_SESSION['user_id'];
$password_reset_sql = "SELECT * FROM password_reset_requests WHERE username = (SELECT username FROM users WHERE id = '$user_id') ORDER BY request_date DESC LIMIT 1";
$password_reset_result = $conn->query($password_reset_sql);
$password_reset_message = "";

if ($password_reset_result->num_rows > 0) {
    $password_reset_row = $password_reset_result->fetch_assoc();
    if ($password_reset_row['status'] == 'approved') {
        $password_reset_message = "Your password has been reset. Please use the new password.";
    } elseif ($password_reset_row['status'] == 'rejected') {
        $password_reset_message = "Your password reset request was rejected.";
    }
}

// Filter by task status, priority, or deadline
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$priority_filter = isset($_GET['priority']) ? $_GET['priority'] : '';
$deadline_filter = isset($_GET['deadline']) ? $_GET['deadline'] : '';

$sql = "SELECT * FROM tasks WHERE assigned_to=" . $_SESSION['user_id'];

if ($status_filter) {
    $sql .= " AND status='$status_filter'";
}
if ($priority_filter) {
    $sql .= " AND priority='$priority_filter'";
}
if ($deadline_filter) {
    $sql .= " AND deadline='$deadline_filter'";
}

$result = $conn->query($sql);
?>

<div class="dashboard">
    <!-- Display Password Reset Status -->
    <?php if ($password_reset_message != ""): ?>
        <div class="alert alert-info">
            <?php echo $password_reset_message; ?>
        </div>
    <?php endif; ?>

    <link rel="stylesheet" href="style-employee_dashboard.css">
    
    <!-- Filter Form -->
    <h2>Filter Tasks</h2>
    <form method="get" action="">
        <label for="status">Status:</label>
        <select name="status" id="status">
            <option value="">All</option>
            <option value="pending" <?php echo ($status_filter == 'pending') ? 'selected' : ''; ?>>Pending</option>
            <option value="in_progress" <?php echo ($status_filter == 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
            <option value="completed" <?php echo ($status_filter == 'completed') ? 'selected' : ''; ?>>Completed</option>
        </select>
        
        <label for="priority">Priority:</label>
        <select name="priority" id="priority">
            <option value="">All</option>
            <option value="low" <?php echo ($priority_filter == 'low') ? 'selected' : ''; ?>>Low</option>
            <option value="medium" <?php echo ($priority_filter == 'medium') ? 'selected' : ''; ?>>Medium</option>
            <option value="high" <?php echo ($priority_filter == 'high') ? 'selected' : ''; ?>>High</option>
        </select>

        <label for="deadline">Deadline:</label>
        <input type="date" name="deadline" id="deadline" value="<?php echo $deadline_filter; ?>">

        <button type="submit" class="btn filter-btn">Filter</button>
    </form>

    <h2>Your Tasks</h2>
    <table class="task-table">
        <tr>
            <th>Task Name</th>
            <th>Description</th>
            <th>Status</th>
            <th>Priority</th>
            <th>Deadline</th>
            <th>Actions</th>
        </tr>
        <?php while ($task = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $task['task_name']; ?></td>
                <td><?php echo $task['description']; ?></td>
                <td><?php echo $task['status']; ?></td>
                <td><?php echo $task['priority']; ?></td>
                <td><?php echo $task['deadline']; ?></td>
                <td><a href="manage_task.php?task_id=<?php echo $task['id']; ?>" class="btn manage-btn">Manage</a></td>
            </tr>
        <?php } ?>
    </table>
</div>

<?php $conn->close(); ?>
