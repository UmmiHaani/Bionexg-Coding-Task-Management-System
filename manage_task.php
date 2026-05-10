<?php
include "db_config.php";
$id = $_GET["task_id"]; // Use task_id instead of id

// Handle task deletion
if (isset($_GET["delete"]) && isset($id)) {
  $delete_id = $_GET["delete"];
  $sql_delete = "DELETE FROM `tasks` WHERE id = ?";
  $stmt_delete = mysqli_prepare($conn, $sql_delete);
  mysqli_stmt_bind_param($stmt_delete, "i", $delete_id);
  $delete_result = mysqli_stmt_execute($stmt_delete);

  if ($delete_result) {
    header("Location: admin_dashboard.php?msg=Task deleted successfully");
    exit();
  } else {
    echo "Failed to delete task: " . mysqli_error($conn);
  }
}

// Check if the form is submitted to update the task
if (isset($_POST["submit"])) {
  $task_name = $_POST['task_name'];
  $description = $_POST['description'];
  $status = $_POST['status'];
  $priority = $_POST['priority'];
  $deadline = $_POST['deadline'];
  $assigned_to = (int)($_POST['assigned_to'] ?? 0); // user id

  // Update task query using prepared statements for security
  $sql = "UPDATE `tasks` SET `task_name`=?, `description`=?, `status`=?, `priority`=?, `deadline`=?, `assigned_to`=? WHERE id = ?";
  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, "ssssssi", $task_name, $description, $status, $priority, $deadline, $assigned_to, $id);
  $result = mysqli_stmt_execute($stmt);

  // If query is successful, redirect to admin dashboard
  if ($result) {
    header("Location: admin_dashboard.php?msg=Task updated successfully");
    exit();
  } else {
    echo "Failed: " . mysqli_error($conn);
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style-manage_task.css">
  <!-- Include Bootstrap, FontAwesome, Google Fonts, etc. -->

  <title>Manage Task</title>
</head>
<body>
  <!-- Add Navbar here -->

  <div class="container mt-5">
    <div class="text-center mb-4">
      <h3>Edit Task</h3>
      <p class="text-muted">Update the task details and click Update</p>
    </div>

    <?php
    // Fetch task details for the edit form
    $sql = "SELECT * FROM `tasks` WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $task = mysqli_fetch_assoc($result);
    ?>

    <div class="container d-flex justify-content-center">
      <form action="" method="post" style="width:50vw; min-width:300px;">
        <div class="row mb-3">
          <div class="col">
            <label class="form-label">Task Name:</label>
            <input type="text" class="form-control" name="task_name" value="<?php echo htmlspecialchars($task['task_name']); ?>" required>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Description:</label>
          <textarea class="form-control" name="description" required><?php echo htmlspecialchars($task['description']); ?></textarea>
        </div>

        <div class="mb-3">
          <label class="form-label">Status:</label>
          <select class="form-control" name="status" required>
            <option value="pending" <?php echo ($task['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
            <option value="in progress" <?php echo ($task['status'] == 'in progress') ? 'selected' : ''; ?>>In Progress</option>
            <option value="completed" <?php echo ($task['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Priority:</label>
          <select class="form-control" name="priority" required>
            <option value="low" <?php echo ($task['priority'] == 'low') ? 'selected' : ''; ?>>Low</option>
            <option value="medium" <?php echo ($task['priority'] == 'medium') ? 'selected' : ''; ?>>Medium</option>
            <option value="high" <?php echo ($task['priority'] == 'high') ? 'selected' : ''; ?>>High</option>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Deadline:</label>
          <input type="date" class="form-control" name="deadline" value="<?php echo $task['deadline']; ?>" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Assigned to:</label>
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
          <select class="form-control" name="assigned_to" required>
            <option value="">-- Assign to employee --</option>
            <?php foreach ($employees as $emp): ?>
              <option value="<?php echo (int)$emp['id']; ?>" <?php echo ((int)$task['assigned_to'] === (int)$emp['id']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($emp['username']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <button type="submit" class="btn btn-success" name="submit">Update</button>
          <a href="admin_dashboard.php" class="btn btn-danger">Cancel</a>
        </div>
      </form>
      
      <!-- Delete Button -->
      <div class="mt-4">
        <a href="manage_task.php?task_id=<?php echo $id; ?>&delete=<?php echo $id; ?>" class="btn btn-danger">Delete Task</a>
      </div>
    </div>
  </div>

  <!-- Bootstrap -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>
</html>
