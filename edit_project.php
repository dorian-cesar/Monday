<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$project_id = $_GET['id'];
$errors = [];

$sql = "SELECT * FROM projects WHERE id = '$project_id'";
$result = $conn->query($sql);
$project = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = $_POST['status'];

    if (empty($name)) {
        $errors[] = 'Project name is required.';
    }

    if (empty($errors)) {
        $sql = "UPDATE projects 
                SET name = '$name', description = '$description', start_date = '$start_date', end_date = '$end_date', status = '$status'
                WHERE id = '$project_id'";

        if ($conn->query($sql) === TRUE) {
            header('Location: project.php?id=' . $project_id);
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
    <title>Edit Project</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
<?php include('header.php'); ?>

<div class="container">
<button onclick="window.history.back()" class="btn btn-secondary">Volver</button>
    <h1>Edit Project</h1>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <form method="POST" action="edit_project.php?id=<?php echo $project_id; ?>">
        <div class="form-group">
            <label for="name">Project Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo $project['name']; ?>" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" id="description" name="description"><?php echo $project['description']; ?></textarea>
        </div>
        <div class="form-group">
            <label for="start_date">Start Date</label>
            <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $project['start_date']; ?>" required>
        </div>
        <div class="form-group">
            <label for="end_date">End Date</label>
            <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $project['end_date']; ?>" required>
        </div>
        <div class="form-group">
            <label for="status">Status</label>
            <select class="form-control" id="status" name="status" required>
                <option value="pending" <?php if ($project['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                <option value="in_progress" <?php if ($project['status'] == 'in_progress') echo 'selected'; ?>>In Progress</option>
                <option value="completed" <?php if ($project['status'] == 'completed') echo 'selected'; ?>>Completed</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update Project</button>
    </form>
    
</div>

<!-- <?php include('footer.php'); ?> -->
</body>
</html>
