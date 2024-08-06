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



function renderTasks($tasks, $status)
{
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
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

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
        <button onclick="window.history.back()" class="btn btn-secondary">Volver</button>
        <div class="kanban-board">
            <div class="kanban-column bg-primary">
                <h4>pending</h4>
                <?php echo renderTasks($tasks, 'pending'); ?>
            </div>
            <div class="kanban-column bg-warning">
                <h4>in_progress</h4>
                <?php echo renderTasks($tasks, 'in_progress'); ?>
            </div>
            <div class="kanban-column bg-info">
                <h4>completed</h4>
                <?php echo renderTasks($tasks, 'completed'); ?>
            </div>
        </div>

    </div>

    <script>
        $(function() {
    $(".kanban-task").draggable({
        revert: "invalid",
        start: function() {
            $(this).css("z-index", 1000);
        },
        stop: function() {
            $(this).css("z-index", "");
        }
    });

    $(".kanban-column").droppable({
        accept: ".kanban-task",
        drop: function(event, ui) {
            var task = ui.draggable;
            var newStatus = $(this).find("h4").text();
            var taskId = task.data("id");

            // Update task status in the database
            $.ajax({
                url: 'update_task_status.php',
                method: 'GET',
                data: {
                    id: taskId,
                    status: newStatus
                },
                success: function(response) {
                    if (response == 'success') {
                        task.appendTo($(this)).css({
                            top: '0',
                            left: '0'
                        });
                    } else {
                      //  alert('Failed to update task status');
                      location.reload();
                    }
                }.bind(this)
            });
        }
    });
});
    </script>


</body>

</html>