<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$task_id = $_GET['id'];

// Obtener el ID del proyecto antes de eliminar la tarea
$sql = "SELECT project_id FROM tasks WHERE id = '$task_id'";
$result = $conn->query($sql);
$task = $result->fetch_assoc();

if ($task) {
    $project_id = $task['project_id'];

    // Eliminar los comentarios asociados con la tarea
    $sql = "DELETE FROM comments WHERE task_id = '$task_id'";
    if ($conn->query($sql) !== TRUE) {
        echo 'Error deleting comments: ' . $conn->error;
        exit();
    }

    // Eliminar la tarea
    $sql = "DELETE FROM tasks WHERE id = '$task_id'";
    if ($conn->query($sql) === TRUE) {
        header('Location: project.php?id=' . $project_id);
        exit();
    } else {
        echo 'Error deleting task: ' . $conn->error;
    }
} else {
    echo 'Task not found.';
}
?>

