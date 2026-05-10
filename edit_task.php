<?php
include('db_config');

if (isset($_GET['id'])) {
    $task_id = $_GET['id'];
    
    // Fetch task data
    $query = "SELECT * FROM tasks WHERE id = $task_id";
    $result = mysqli_query($conn, $query);
    $task = mysqli_fetch_assoc($result);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $task_name = mysqli_real_escape_string($conn, $_POST['task_name']);
    $task_description = mysqli_real_escape_string($conn, $_POST['task_description']);

    $update_query = "UPDATE tasks SET task_name = '$task_name', task_description = '$task_description' WHERE id = $task_id";
    
    if (mysqli_query($conn, $update_query)) {
        header("Location: index.php"); // Redirect back to the main page
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container">
    <h1>Edit Task</h1>
    
    <form action="edit_task.php?id=<?php echo $task['id']; ?>" method="POST">
        <input type="text" name="task_name" value="<?php echo $task['task_name']; ?>" required>
        <textarea name="task_description" required><?php echo $task['task_description']; ?></textarea>
        <input type="submit" value="Update Task">
    </form>
</div>

</body>
</html>
