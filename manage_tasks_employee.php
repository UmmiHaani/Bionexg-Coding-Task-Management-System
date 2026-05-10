<?php
session_start();

// Redirect if the user is not an employee
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'employee') {
    header("Location: login.php");
    exit();
}

include "db_config.php";
$task_id = $_GET['task_id'];  // Retrieve task ID from query parameter

// Fetch task details for the employee
$sql = "SELECT * FROM tasks WHERE id = ? AND assigned_to = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $task_id, $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$task = mysqli_fetch_assoc($result);

// Handle task update
if (isset($_POST['submit'])) {
    $status = $_POST['status'];
    $sql_update = "UPDATE tasks SET status = ? WHERE id = ? AND assigned_to = ?";
    $stmt_update = mysqli_prepare($conn, $sql_update);
    mysqli_stmt_bind_param($stmt_update, "sii", $status, $task_id, $_SESSION['user_id']);
    $update_result = mysqli_stmt_execute($stmt_update);

    if ($update_result) {
        header("Location: employee_dashboard.php?msg=Task updated successfully");
        exit();
    } else {
        echo "Failed to update task: " . mysqli_error($conn);
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Task - Employee</title>
    <link rel="stylesheet" href="style-employee_dashboard.css">
</head>
<body>
    <div class="container mt-5">
        <h3>Edit Task</h3>
        <p class="text-muted">Update the task details and click Update</p>

        <form method="POST">
            <div class="mb-3">
                <label for="task_name" class="form-label">Task Name</label>
                <input type="text" class="form-control" id="task_name" value="<?php echo htmlspecialchars($task['task_name']); ?>" disabled>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="pending" <?php echo ($task['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="in_progress" <?php echo ($task['status'] == 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
                    <option value="completed" <?php echo ($task['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                </select>
            </div>

            <div>
                <button type="submit" name="submit" class="btn btn-success">Update Status</button>
                <a href="employee_dashboard.php" class="btn btn-danger">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
