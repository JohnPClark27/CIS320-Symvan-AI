<?php
// ===========================================
// EVENT PLANNING BOARD
// ===========================================
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ===========================================
// DATABASE CONNECTION
// ===========================================
require_once 'db_connect.php';

// ===========================================
// HANDLE TASK CREATION
// ===========================================
$successMessage = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task-title'])) {
    $title = trim($_POST['task-title']);
    $desc  = trim($_POST['task-desc']);
    $user_id = $_SESSION['user_id'];
    $status = 'To Do';

    if (!empty($title)) {
        $stmt = $conn->prepare("INSERT INTO task (title, description, status, created_by) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $title, $desc, $status, $user_id);
        $stmt->execute();
        $stmt->close();
        $successMessage = "Task added successfully!";
    }
}

// ===========================================
// FETCH TASKS BY STATUS
// ===========================================
$tasks = [
    "To Do" => [],
    "In Progress" => [],
    "Completed" => []
];

$result = $conn->query("SELECT * FROM task ORDER BY created_at ASC");
while ($row = $result->fetch_assoc()) {
    $tasks[$row['status']][] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Planning Board - Symvan</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar">
    <div class="navbar-container">
        <a href="index.php" class="navbar-brand">Symvan</a>
        <ul class="navbar-menu">
            <li><a href="index.php">Home</a></li>
            <li><a href="myevents.php">My Events</a></li>
            <li><a href="enroll.php">Enroll</a></li>
            <li><a href="create-event.php">Create Event</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="contact.php">Contact</a></li>
        </ul>
    </div>
</nav>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">Event Planning Board</h1>
        <p class="page-subtitle">Track your event tasks and progress</p>
    </div>

    <?php if (!empty($successMessage)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($successMessage); ?></div>
    <?php endif; ?>

    <!-- Kanban Board -->
    <div class="kanban-board">
        <?php foreach (["To Do", "In Progress", "Completed"] as $column): ?>
            <div class="kanban-column">
                <div class="kanban-header"><?php echo htmlspecialchars($column); ?></div>
                <div class="kanban-tasks">
                    <?php foreach ($tasks[$column] as $task): ?>
                        <div class="kanban-task">
                            <div class="kanban-task-title"><?php echo htmlspecialchars($task['title']); ?></div>
                            <div class="kanban-task-desc"><?php echo htmlspecialchars($task['description']); ?></div>
                            <form action="update_task.php" method="POST" class="mt-sm">
                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                <select name="status" class="form-input" onchange="this.form.submit()">
                                    <option value="To Do" <?php if ($task['status'] == 'To Do') echo 'selected'; ?>>To Do</option>
                                    <option value="In Progress" <?php if ($task['status'] == 'In Progress') echo 'selected'; ?>>In Progress</option>
                                    <option value="Completed" <?php if ($task['status'] == 'Completed') echo 'selected'; ?>>Completed</option>
                                </select>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Quick Add Task Section -->
    <div class="mt-lg">
        <div class="card" style="max-width: 600px; margin: 0 auto;">
            <h3 class="mb-md text-red">Quick Add Task</h3>
            <form action="planning.php" method="POST">
                <div class="form-group">
                    <label for="task-title" class="form-label">Task Title</label>
                    <input type="text" id="task-title" name="task-title" class="form-input" required placeholder="Enter task name">
                </div>
                <div class="form-group">
                    <label for="task-desc" class="form-label">Description</label>
                    <input type="text" id="task-desc" name="task-desc" class="form-input" placeholder="Brief description">
                </div>
                <button type="submit" class="btn btn-primary btn-block">Add to To Do</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
