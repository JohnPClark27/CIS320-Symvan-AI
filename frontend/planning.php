<?php
// ===========================================
// EVENT-SPECIFIC PLANNING BOARD (KANBAN)
// ===========================================
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'db_connect.php';
$user_id = $_SESSION['user_id'];

$successMessage = "";
$errorMessage = "";

// ===========================================
// FETCH EVENTS ADMIN CAN MANAGE
// ===========================================
$events = [];
$eventStmt = $conn->prepare("
    SELECT e.id, e.name 
    FROM event e
    INNER JOIN organization o ON e.organization_id = o.id
    INNER JOIN member m ON m.organization_id = o.id
    WHERE m.user_id = ? AND m.permission_level = 'Admin'
    ORDER BY e.date DESC
");
$eventStmt->bind_param("i", $user_id);
$eventStmt->execute();
$events = $eventStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$eventStmt->close();

// ===========================================
// DETERMINE SELECTED EVENT
// ===========================================
$selectedEventId = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;

// ===========================================
// HANDLE TASK CREATION
// ===========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task-title'])) {
    $title = trim($_POST['task-title']);
    $desc  = trim($_POST['task-desc']);
    $status = 'To Do';
    $event_id = intval($_POST['event_id']);

    if ($title && $event_id) {
        $stmt = $conn->prepare("
            INSERT INTO task (title, description, status, created_by, event_id, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->bind_param("sssii", $title, $desc, $status, $user_id, $event_id);
        if ($stmt->execute()) {
            $successMessage = "✅ Task added successfully!";
        } else {
            $errorMessage = "❌ Failed to add task.";
        }
        $stmt->close();
    } else {
        $errorMessage = "⚠️ Please fill out the required fields.";
    }
}

// ===========================================
// HANDLE TASK DELETION
// ===========================================
if (isset($_POST['delete_task_id'])) {
    $delete_id = intval($_POST['delete_task_id']);
    $delStmt = $conn->prepare("DELETE FROM task WHERE id = ? AND created_by = ?");
    $delStmt->bind_param("ii", $delete_id, $user_id);
    $delStmt->execute();
    $delStmt->close();
    $successMessage = "🗑️ Task deleted successfully!";
}

// ===========================================
// FETCH EVENT DETAILS (for status control)
// ===========================================
$currentEvent = null;
if ($selectedEventId) {
    $stmt = $conn->prepare("
        SELECT e.id, e.name, e.status
        FROM event e
        INNER JOIN organization o ON e.organization_id = o.id
        INNER JOIN member m ON m.organization_id = o.id
        WHERE e.id = ? AND m.user_id = ? AND m.permission_level = 'Admin'
        LIMIT 1
    ");
    $stmt->bind_param("ii", $selectedEventId, $user_id);
    $stmt->execute();
    $currentEvent = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// ===========================================
// FETCH TASKS FOR SELECTED EVENT
// ===========================================
$tasks = [
    "To Do" => [],
    "In Progress" => [],
    "Completed" => []
];

if ($selectedEventId) {
    $stmt = $conn->prepare("
        SELECT * FROM task 
        WHERE event_id = ? 
        ORDER BY created_at ASC
    ");
    $stmt->bind_param("i", $selectedEventId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        if (isset($tasks[$row['status']])) {
            $tasks[$row['status']][] = $row;
        }
    }
    $stmt->close();
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
            <li><a href="organization.php">Organizations</a></li>
            <li><a href="create_event.php">Create Event</a></li>
            <li><a href="profile.php">Profile</a></li>
        </ul>
        <div class="user-session">
            <?php if (isset($_SESSION['email'])): ?>
                <span class="welcome-text">👋 <?= htmlspecialchars($_SESSION['email']) ?></span>
                <a href="logout.php" class="btn btn-outline btn-sm">Logout</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">Event Planning Board</h1>
        <p class="page-subtitle">Select an event to plan its tasks and manage its posting status.</p>
    </div>

    <!-- CHATBOT SHORTCUT BUTTON -->
    <div style="text-align:center; margin-bottom:1.5rem;">
     <a href="chatbot.php" class="btn btn-primary" style="padding:0.6rem 1.2rem;">
          Open Event Chatbot
      </a>
    </div>


    <?php if ($successMessage): ?>
        <div class="card" style="background:#d4edda; border:1px solid #28a745; color:#155724; text-align:center; margin-bottom:1rem;">
            <?= htmlspecialchars($successMessage) ?>
        </div>
    <?php elseif ($errorMessage): ?>
        <div class="card" style="background:#f8d7da; border:1px solid #dc3545; color:#721c24; text-align:center; margin-bottom:1rem;">
            <?= htmlspecialchars($errorMessage) ?>
        </div>
    <?php endif; ?>

    <!-- EVENT SELECTION DROPDOWN -->
    <form method="GET" action="planning.php" class="form-group" style="max-width:400px; margin:auto; margin-bottom:2rem;">
        <label for="event_id" class="form-label">Choose an Event</label>
        <select name="event_id" id="event_id" class="form-select" onchange="this.form.submit()">
            <option value="">-- Select Event --</option>
            <?php foreach ($events as $event): ?>
                <option value="<?= $event['id'] ?>" <?= $selectedEventId == $event['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($event['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <!-- EVENT STATUS CONTROL -->
    <?php if ($currentEvent): ?>
        <div class="card" style="max-width:600px;margin:0 auto 2rem auto;text-align:center;">
            <h3 class="mb-md text-red"><?= htmlspecialchars($currentEvent['name']) ?></h3>
            <p><strong>Current Status:</strong>
                <span style="color:<?= $currentEvent['status'] == 'Posted' ? 'green' : 'orange' ?>">
                    <?= htmlspecialchars($currentEvent['status']) ?>
                </span>
            </p>
            <form action="update_event_status.php" method="POST" style="margin-top:1rem;">
                <input type="hidden" name="event_id" value="<?= $currentEvent['id'] ?>">
                <select name="status" class="form-select" style="max-width:250px;margin:auto;">
                    <option value="Posted" <?= $currentEvent['status'] == 'Posted' ? 'selected' : '' ?>>Posted</option>
                    <option value="Draft" <?= $currentEvent['status'] == 'Draft' ? 'selected' : '' ?>>Draft</option>
                </select>
                <button type="submit" class="btn btn-primary mt-sm">Update Status</button>
            </form>
        </div>
    <?php endif; ?>

    <!-- KANBAN BOARD -->
    <?php if ($selectedEventId): ?>
        <div class="kanban-board">
            <?php foreach (["To Do", "In Progress", "Completed"] as $column): ?>
                <div class="kanban-column">
                    <div class="kanban-header"><?= htmlspecialchars($column) ?></div>
                    <div class="kanban-tasks">
                        <?php if (empty($tasks[$column])): ?>
                            <p class="text-grey text-center">No tasks yet.</p>
                        <?php else: ?>
                            <?php foreach ($tasks[$column] as $task): ?>
                                <div class="kanban-task">
                                    <div class="kanban-task-title"><?= htmlspecialchars($task['title']) ?></div>
                                    <div class="kanban-task-desc"><?= htmlspecialchars($task['description']) ?></div>

                                    <!-- STATUS UPDATE FORM -->
                                    <form action="update_task.php" method="POST" class="mt-sm">
                                        <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                        <input type="hidden" name="event_id" value="<?= $selectedEventId ?>">
                                        <select name="status" class="form-input" onchange="this.form.submit()">
                                            <option value="To Do" <?= $task['status'] == 'To Do' ? 'selected' : '' ?>>To Do</option>
                                            <option value="In Progress" <?= $task['status'] == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                                            <option value="Completed" <?= $task['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                        </select>
                                    </form>

                                    <!-- DELETE FORM -->
                                    <form action="planning.php?event_id=<?= $selectedEventId ?>" method="POST" class="mt-sm" onsubmit="return confirm('Are you sure you want to delete this task?');">
                                        <input type="hidden" name="delete_task_id" value="<?= $task['id'] ?>">
                                        <button type="submit" class="btn btn-outline btn-block">Delete</button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- ADD TASK FORM -->
        <div class="mt-lg">
            <div class="card" style="max-width:600px; margin:0 auto;">
                <h3 class="mb-md text-red">Add Task for This Event</h3>
                <form action="planning.php?event_id=<?= $selectedEventId ?>" method="POST">
                    <input type="hidden" name="event_id" value="<?= $selectedEventId ?>">
                    <div class="form-group">
                        <label for="task-title" class="form-label">Task Title *</label>
                        <input type="text" id="task-title" name="task-title" class="form-input" required placeholder="Enter task name">
                    </div>
                    <div class="form-group">
                        <label for="task-desc" class="form-label">Description</label>
                        <input type="text" id="task-desc" name="task-desc" class="form-input" placeholder="Brief description (optional)">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Add Task</button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <div class="card" style="text-align:center; background-color:#fff3cd; border:1px solid #ffeeba; color:#856404;">
            <p>⚠️ Select an event from the dropdown above to view or manage its tasks.</p>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
