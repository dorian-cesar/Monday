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

// Obtener las tareas del proyecto
$sql_tasks = "SELECT id, name, description, start_date, end_date FROM tasks WHERE project_id = '$project_id'";
$tasks_result = $conn->query($sql_tasks);

if ($tasks_result === FALSE) {
    die('Error: ' . $conn->error);
}

$tasks = [];
while ($task = $tasks_result->fetch_assoc()) {
    $tasks[] = [
        'id' => isset($task['id']) ? $task['id'] : null,
        'text' => isset($task['name']) ? $task['name'] : '',
        'start_date' => isset($task['start_date']) ? $task['start_date'] : '',
        'duration' => isset($task['end_date']) && isset($task['start_date']) ? (new DateTime($task['end_date']))->diff(new DateTime($task['start_date']))->days : 0,
       // 'progress' => isset($task['progress']) ? $task['progress'] / 100 : 0,
        'parent' => 0 // Considera todas las tareas como tareas de nivel superior para simplificación
    ];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Project Gantt View</title>
    <link href="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.css" rel="stylesheet">
    <script src="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.js"></script>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        #gantt_here {
            width: 100%;
            height: 100vh;
        }
    </style>
</head>
<body>
    <div id="gantt_here"></div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Initialize Gantt
            gantt.config.xml_date = "%Y-%m-%d %H:%i";
            gantt.init("gantt_here");

            // Load tasks
            gantt.parse({
                data: <?php echo json_encode($tasks); ?>
            });
        });
    </script>
</body>
</html>
