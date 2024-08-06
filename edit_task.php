<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$task_id = $_GET['id'];
$errors = [];

$sql = "SELECT * FROM tasks WHERE id = '$task_id'";
$result = $conn->query($sql);
$task = $result->fetch_assoc();

$sql_users = "SELECT * FROM users";
$users_result = $conn->query($sql_users);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = $_POST['status'];
    $progress = intval($_POST['progress']);
    $assigned_to = $_POST['assigned_to'];

    if (empty($name)) {
        $errors[] = 'Task name is required.';
    }

    if (empty($errors)) {
        $sql = "UPDATE tasks 
                SET progress='$progress',  name = '$name', description = '$description', start_date = '$start_date', end_date = '$end_date', status = '$status', assigned_to = '$assigned_to'
                WHERE id = '$task_id'";

        if ($conn->query($sql) === TRUE) {
            header('Location: task.php?id=' . $task_id);
            exit();
        } else {
            $errors[] = 'Error: ' . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Task</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
<?php include('header.php'); ?>

<div class="container">
<button onclick="window.history.back()" class="btn btn-secondary">Volver</button>
    <h1>Edit Task</h1>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <form method="POST" action="edit_task.php?id=<?php echo $task_id; ?>">
        <div class="form-group">
            <label for="name">Task Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo $task['name']; ?>" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" id="description" name="description"><?php echo $task['description']; ?></textarea>
        </div>
        <div class="form-group">
            <label for="start_date">Start Date</label>
            <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $task['start_date']; ?>" required>
        </div>
        <div class="form-group">
            <label for="end_date">End Date</label>
            <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $task['end_date']; ?>" required>
        </div>
        <div class="form-group">
            <label for="status">Status</label>
            <select class="form-control" id="status" name="status" required>
                <option value="pending" <?php if ($task['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                <option value="in_progress" <?php if ($task['status'] == 'in_progress') echo 'selected'; ?>>In Progress</option>
                <option value="completed" <?php if ($task['status'] == 'completed') echo 'selected'; ?>>Completed</option>
            </select>
        </div>
        <div class="form-group">
            <label for="progress">Progress (%)</label>
            <input type="number" name="progress" id="progress" class="form-control" value="<?php echo $task['progress']; ?>" min="0" max="100" required>
        </div>
        <div class="form-group">
            <label for="assigned_to">Assigned To</label>
            <select class="form-control" id="assigned_to" name="assigned_to" required>
                <?php while ($user = $users_result->fetch_assoc()): ?>
                    <option value="<?php echo $user['id']; ?>" <?php if ($task['assigned_to'] == $user['id']) echo 'selected'; ?>>
                        <?php echo $user['name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update Task</button>
    </form>
   
</div>

<!-- <?php include('footer.php'); ?> -->
</body>
</html>
