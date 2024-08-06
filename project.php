<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$project_id = $_GET['id'];

$sql = "SELECT * FROM projects WHERE id = '$project_id'";
$result = $conn->query($sql);
$project = $result->fetch_assoc();

$sql_tasks = "SELECT tasks.*, users.name AS assigned_user FROM tasks LEFT JOIN users ON tasks.assigned_to = users.id WHERE project_id = '$project_id'";
$tasks_result = $conn->query($sql_tasks);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Project Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
<?php include('header.php'); ?>

<div class="container">
    <h1>Project Details</h1>
    <h2><?php echo $project['name']; ?></h2>
    <p><?php echo $project['description']; ?></p>
    <p><strong>Start Date:</strong> <?php echo $project['start_date']; ?></p>
    <p><strong>End Date:</strong> <?php echo $project['end_date']; ?></p>
    <p><strong>Status:</strong> <?php echo $project['status']; ?></p>
    <a href="edit_project.php?id=<?php echo $project_id; ?>" class="btn btn-primary">Edit Project</a>
    <a href="Gantt.php?id=<?php echo $project_id; ?>" class="btn btn-warning">Gantt</a>
    <a href="kanban_view.php?id=<?php echo $project_id; ?>" class="btn btn-info">Kanban</a>
    <button onclick="window.history.back()" class="btn btn-secondary">Volver</button>
    <!-- <a href="delete_project.php?id=<?php echo $project_id; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this project?')">Delete Project</a> -->
    <hr>
    <h3>Tasks</h3>
    <a href="create_task.php?project_id=<?php echo $project_id; ?>" class="btn btn-success">Create Task</a>
    <table class="table table-bordered mt-3">
    <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Status</th>
                <th>Progress (%)</th>
                <th>Assigned To</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($task = $tasks_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $task['name']; ?></td>
                    <td><?php echo $task['description']; ?></td>
                    <td><?php echo $task['start_date']; ?></td>
                    <td><?php echo $task['end_date']; ?></td>
                    <td><?php echo $task['status']; ?></td>
                    <td><?php echo $task['progress']; ?></td>
                    <td><?php echo $task['assigned_user']; ?></td>
                    <td>
                    <a href="task.php?id=<?php echo $task['id']; ?>" class="btn btn-info btn-sm">View</a> <!-- Enlace a los detalles de la tarea -->
                        <a href="edit_task.php?id=<?php echo $task['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                        <a href="delete_task.php?id=<?php echo $task['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this task?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    
</div>

<!-- <?php include('footer.php'); ?> -->
</body>
</html>
