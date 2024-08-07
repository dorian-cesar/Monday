<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$project_id = $_GET['project_id'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $assigned_to = $_POST['assigned_to'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = $_POST['status'];

    if (empty($name)) {
        $errors[] = 'Task name is required.';
    }

    if (empty($errors)) {
        $sql = "INSERT INTO tasks (project_id, name, description, assigned_to, start_date, end_date, status) 
                VALUES ('$project_id', '$name', '$description', '$assigned_to', '$start_date', '$end_date', '$status')";

        if ($conn->query($sql) === TRUE) {
            header('Location: project.php?id=' . $project_id);
            exit();
        } else {
            $errors[] = 'Error: ' . $conn->error;
        }
    }
}

$users_sql = "SELECT id, name FROM users";
$users_result = $conn->query($users_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Task</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
<?php include('header.php'); ?>

<div class="container">
    <h1>Create Task</h1>
    <button onclick="window.history.back()" class="btn btn-secondary">Volver</button>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <form method="POST" action="create_task.php?project_id=<?php echo $project_id; ?>">
        <div class="form-group">
            <label for="name">Task Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" id="description" name="description"></textarea>
        </div>
        <div class="form-group">
            <label for="assigned_to">Assign To</label>
            <select class="form-control" id="assigned_to" name="assigned_to" required>
                <?php while($user = $users_result->fetch_assoc()): ?>
                    <option value="<?php echo $user['id']; ?>"><?php echo $user['name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="start_date">Start Date</label>
            <input type="date" class="form-control" id="start_date" name="start_date" required>
        </div>
        <div class="form-group">
            <label for="end_date">End Date</label>
            <input type="date" class="form-control" id="end_date" name="end_date" required>
        </div>
        <div class="form-group">
            <label for="status">Status</label>
            <select class="form-control" id="status" name="status" required>
                <option value="pending">Pending</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Create Task</button>
    </form>

</div>

<?php include('footer.php'); ?>
</body>
</html>
