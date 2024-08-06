<?php
include('config.php');


    $task_id = $_GET['id'];
    $status = $_GET['status'];
echo
    $sql = "UPDATE tasks SET status = '$status' WHERE id = '$task_id'";
    if ($conn->query($sql) === TRUE) {
        echo 'success';
    } else {
        echo 'error';
    }


?>
