<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$task_id = $_GET['id'];
$errors = [];

$sql = "SELECT tasks.*, users.name as assigned_to_name FROM tasks
        JOIN users ON tasks.assigned_to = users.id
        WHERE tasks.id = '$task_id'";
$result = $conn->query($sql);
$task = $result->fetch_assoc();

$comments_sql = "SELECT comments.*, users.name as user_name FROM comments
                 JOIN users ON comments.user_id = users.id
                 WHERE comments.task_id = '$task_id'
                 ORDER BY comments.created_at DESC";
$comments_result = $conn->query($comments_sql);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $comment = $_POST['comment'];
    $user_id = $_SESSION['user_id'];

    if (empty($comment)) {
        $errors[] = 'Comment cannot be empty.';
    }

    if (empty($errors)) {
        $sql = "INSERT INTO comments (task_id, user_id, comment) 
                VALUES ('$task_id', '$user_id', '$comment')";

        if ($conn->query($sql) === TRUE) {
            header('Location: task.php?id=' . $task_id);
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
    <title>Task Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

</head>
<body>
<?php include('header.php'); ?>

<div class="container">
<button onclick="window.history.back()" class="btn btn-secondary">Volver</button>
    <h1>Task Details</h1>
    <div class="card text-primary">
        <div class="card-body">
            <h5 class="card-title"><?php echo $task['name']; ?></h5>
            <p class="card-text"><strong>Description:</strong> <?php echo $task['description']; ?></p>
            <p class="card-text"><strong>Assigned To:</strong> <?php echo $task['assigned_to_name']; ?></p>
            <p class="card-text"><strong>Start Date:</strong> <?php echo $task['start_date']; ?></p>
            <p class="card-text"><strong>End Date:</strong> <?php echo $task['end_date']; ?></p>
            <p class="card-text"><strong>Status:</strong> <?php echo ucfirst($task['status']); ?></p>
        </div>
    </div>
    <br>
    <h2>Comments</h2>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <div class="list-group text-primary">
        <?php while ($comment = $comments_result->fetch_assoc()): ?>
            <div class="list-group-item">
                <p><strong><?php echo $comment['user_name']; ?>:</strong></p>
                <p><?php echo $comment['comment']; ?></p>
                <small class="text-muted"><?php echo $comment['created_at']; ?></small>
            </div>
        <?php endwhile; ?>
    </div>
    <br>
    <h3>Add a Comment</h3>
    <form method="POST" action="task.php?id=<?php echo $task_id; ?>">
        <div class="form-group">
            <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Add Comment</button>
    </form>
   
</div>

<?php include('footer.php'); ?>
</body>
</html>
