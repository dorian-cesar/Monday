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
    WHERE project_id = '$project_id'  ORDER BY tasks.id ASC;"
    ;
$tasks_result = $conn->query($sql);
$tasks = [];

while ($task = $tasks_result->fetch_assoc()) {
    $tasks[] = $task;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Gantt Chart - <?php echo $project['name']; ?></title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['gantt']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Task ID');
            data.addColumn('string', 'Task Name');
            data.addColumn('string', 'Resource');
            data.addColumn('date', 'Start Date');
            data.addColumn('date', 'End Date');
            data.addColumn('number', 'Duration');
            data.addColumn('number', 'Percent Complete');
            data.addColumn('string', 'Dependencies');

            data.addRows([
                <?php
                $prev_task_id = null;
                foreach ($tasks as $task):
                    $dependency = $prev_task_id ? "'$prev_task_id'" : 'null';
                ?>
                    ['<?php echo $task['id']; ?>', '<?php echo $task['name']; ?>', '<?php echo $task['assigned_user']; ?>', new Date('<?php echo $task['start_date']; ?>'), new Date('<?php echo $task['end_date']; ?>'), null, <?php echo $task['progress']; ?>, <?php echo $dependency; ?>],
                <?php
                  //  $prev_task_id = $task['id'];
                endforeach;
                ?>
            ]);

            var options = {
                height: 400,
                gantt: {
                    trackHeight: 30
                }
            };

            var chart = new google.visualization.Gantt(document.getElementById('chart_div'));

            chart.draw(data, options);
        }
    </script>
</head>
<body>
<?php include('header.php'); ?>

<div class="container">
    <h1>Gantt Chart - <?php echo $project['name']; ?></h1>
    <button onclick="window.history.back()" class="btn btn-secondary">Volver</button>
    <div id="chart_div" style="margin-top: 20px;"></div>
  
</div>

<!-- <?php include('footer.php'); ?> -->
</body>
</html>
