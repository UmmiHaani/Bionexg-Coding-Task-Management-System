<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include "db_config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $task_name = trim($_POST['task_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status = $_POST['status'] ?? 'pending';
    $priority = $_POST['priority'] ?? 'medium';
    $deadline = $_POST['deadline'] ?? null;
    $assigned_to = (int)($_POST['assigned_to'] ?? 0); // user id

    if ($assigned_to <= 0) {
        die("Invalid assignee selected. <a href='add_task.php'>Go back</a>");
    }

    $sql = "INSERT INTO tasks (task_name, description, status, priority, deadline, assigned_to)
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        die("Prepare failed: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "sssssi", $task_name, $description, $status, $priority, $deadline, $assigned_to);
    $ok = mysqli_stmt_execute($stmt);

    if ($ok) {
        echo "New task created successfully. <a href='admin_dashboard.php'>Go back to dashboard</a>";
    } else {
        echo "Error: " . mysqli_stmt_error($stmt);
    }

    mysqli_stmt_close($stmt);
}
?>

<!-- Task Creation Form HTML -->
<form method="POST" action="add_task.php">
<link rel="stylesheet" href="style-add_task.css">

    <input type="text" name="task_name" placeholder="Task Name" required>
    <textarea name="description" placeholder="Description" required></textarea>
    <select name="status" required>
        <option value="pending">Pending</option>
        <option value="in progress">In Progress</option>
        <option value="completed">Completed</option>
    </select>
    <select name="priority" required>
        <option value="low">Low</option>
        <option value="medium">Medium</option>
        <option value="high">High</option>
    </select>
    <input type="date" name="deadline" required>
    <?php
    $employees = [];
    $res = mysqli_query($conn, "SELECT id, username FROM users WHERE role = 'employee' ORDER BY username ASC");
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $employees[] = $row;
        }
        mysqli_free_result($res);
    }
    ?>
    <select name="assigned_to" required>
        <option value="">-- Assign to employee --</option>
        <?php foreach ($employees as $emp): ?>
            <option value="<?php echo (int)$emp['id']; ?>">
                <?php echo htmlspecialchars($emp['username']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Add Task</button>
</form>
