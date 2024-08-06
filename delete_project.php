<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$project_id = $_GET['id'];

// Obtener todas las tareas asociadas con el proyecto
$sql = "SELECT id FROM tasks WHERE project_id = '$project_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($task = $result->fetch_assoc()) {
        $task_id = $task['id'];

        // Eliminar los comentarios asociados con cada tarea
        $sql = "DELETE FROM comments WHERE task_id = '$task_id'";
        if ($conn->query($sql) !== TRUE) {
            echo 'Error deleting comments: ' . $conn->error;
            exit();
        }

        // Eliminar cada tarea
        $sql = "DELETE FROM tasks WHERE id = '$task_id'";
        if ($conn->query($sql) !== TRUE) {
            echo 'Error deleting tasks: ' . $conn->error;
            exit();
        }
    }
}

// Eliminar el proyecto
$sql = "DELETE FROM projects WHERE id = '$project_id'";
if ($conn->query($sql) === TRUE) {
    header('Location: dashboard.php');
    exit();
} else {
    echo 'Error deleting project: ' . $conn->error;
}
?>
