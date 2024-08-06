<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$project_id = $_GET['id'];

// Obtener el proyecto
$sql = "SELECT * FROM projects WHERE id = '$project_id'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die('Project not found');
}

$project = $result->fetch_assoc();

// Obtener las tareas del proyecto con el nombre del usuario asignado
$sql = "
    SELECT tasks.id, tasks.name, tasks.description, tasks.start_date, tasks.end_date, tasks.status, tasks.progress, users.name AS assigned_user 
    FROM tasks 
    LEFT JOIN users ON tasks.assigned_to = users.id 
    WHERE project_id = '$project_id'
    ORDER BY tasks.id ASC";
$tasks_result = $conn->query($sql);
$tasks = [];

while ($task = $tasks_result->fetch_assoc()) {
    $tasks[] = $task;
}



function renderTasks($tasks, $status) {
    $output = '';
    foreach ($tasks as $task) {
        if ($task['status'] == $status) {
            $output .= '<div class="kanban-task" data-id="' . $task['id'] . '">';
            $output .= '<h5>' . $task['name'] . '</h5>';
            $output .= '<p>Assigned to: ' . $task['assigned_user'] . '</p>';
            $output .= '<p>Progress: ' . $task['progress'] . '%</p>';
            $output .= '</div>';
        }
    }
    return $output;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Kanban Board - <?php echo $project['name']; ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .kanban-board {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
            color: darkblue;
        }
        .kanban-column {
            width: 30%;
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 10px;
        }
        .kanban-column h4 {
            text-align: center;
            margin-bottom: 10px;
        }
        .kanban-task {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
            padding: 10px;
        }
        .kanban-task h5 {
            margin: 0;
        }
    </style>
</head>
<body>
<?php include('header.php'); ?>

<div class="container">
    <h1>Kanban Board - <?php echo $project['name']; ?></h1>
    <div class="kanban-board">
        <div class="kanban-column">
            <h4>To Do</h4>
            <?php echo renderTasks($tasks, 'pending'); ?>
        </div>
        <div class="kanban-column">
            <h4>In Progress</h4>
            <?php echo renderTasks($tasks, 'in_progress'); ?>
        </div>
        <div class="kanban-column">
            <h4>Done</h4>
            <?php echo renderTasks($tasks, 'completed'); ?>
        </div>
    </div>

</div>


</body>
</html>


?>
