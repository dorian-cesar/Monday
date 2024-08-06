
<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM projects WHERE user_id='$user_id'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body >
<?php include('header.php'); ?>

<div class="container" >
    <h1>Projects</h1>
    <a href="create_project.php" class="btn btn-primary">Create Project</a>
    <button onclick="window.history.back()" class="btn btn-secondary">Volver</button>
    <table class="table mt-3">
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['description']; ?></td>
                <td><?php echo $row['status']; ?></td>
                <td>
                    <a href="project.php?id=<?php echo $row['id']; ?>" class="btn btn-info">View</a>
                    <a href="edit_project.php?id=<?php echo $row['id']; ?>" class="btn btn-warning">Edit</a>
                    <!-- <a href="delete_project.php?id=<?php echo $row['id']; ?>" class="btn btn-danger">Delete</a> -->
                    <a href="delete_project.php?id=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this project?')">Delete Project</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
  
</div>

<?php include('footer.php'); ?>
</body>
</html>
