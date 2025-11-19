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
            $successMessage = "Task added successfully!";
        } else {
            $errorMessage = "Failed to add task.";
        }
        $stmt->close();
    } else {
        $errorMessage = "Please fill out the required fields.";
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

    // Update audit_log
    log_audit($conn, $user_id, 'Deleted task', $delete_id);
    
    $delStmt->close();
    $successMessage = "Task deleted successfully!";
}

// ===========================================
// FETCH EVENT DETAILS (FOR STATUS / DESCRIPTION CONTROL)
// ===========================================
$currentEvent = null;
if ($selectedEventId) {
    $stmt = $conn->prepare("
        SELECT e.id, e.name, e.status, e.details
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
// FETCH ATTENDEE COUNT
// ===========================================
$attendeeCount = 0;

if ($selectedEventId) {
    $countStmt = $conn->prepare("
        SELECT COUNT(*) AS total
        FROM enrollment
        WHERE event_id = ?
    ");
    $countStmt->bind_param("i", $selectedEventId);
    $countStmt->execute();
    $countResult = $countStmt->get_result()->fetch_assoc();
    $attendeeCount = $countResult['total'] ?? 0;
    $countStmt->close();
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

    <style>
        /* Sidebar styling */
        .sidebar-right {
            width: 280px;
            background: #fff;
            padding: 1.2rem;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.10);
            height: fit-content;
            position: sticky;
            top: 100px;
        }
        .sidebar-section {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #eee;
        }
    </style>
</head>

<body>
    <!-- ===================================
         NAVIGATION BAR
         =================================== -->
    <?php $activePage = 'planning'; ?>
    <?php include 'components/navbar.php'; ?>

<div class="container" style="display:flex; gap:2rem;">
    
    <!-- LEFT SIDE (KANBAN + ADD TASK) -->
    <div style="flex-grow:1;">

        <div class="page-header">
            <h1 class="page-title">Event Planning Board</h1>
            <p class="page-subtitle">Plan and organize tasks for your events.</p>
        </div>

        <?php if ($successMessage): ?>
            <div class="alert alert-success"><?= htmlspecialchars($successMessage) ?></div>
        <?php elseif ($errorMessage): ?>
            <div class="alert alert-error"><?= htmlspecialchars($errorMessage) ?></div>
        <?php endif; ?>

        <!-- KANBAN BOARD -->
        <a id="tasks"></a>
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

                                        <!-- UPDATE STATUS -->
                                        <form action="update_task.php?event_id=<?= $selectedEventId ?>#tasks" method="POST" class="mt-sm">
                                            <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                            <input type="hidden" name="event_id" value="<?= $selectedEventId ?>">
                                            <select name="status" class="form-input" onchange="this.form.submit()">
                                                <option value="To Do" <?= $task['status'] == 'To Do' ? 'selected' : '' ?>>To Do</option>
                                                <option value="In Progress" <?= $task['status'] == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                                                <option value="Completed" <?= $task['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                            </select>
                                        </form>

                                        <!-- DELETE TASK -->
                                        <form action="planning.php?event_id=<?= $selectedEventId ?>#tasks" method="POST" class="mt-sm" 
                                            onsubmit="return confirm('Delete this task?');">
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
                    <h3 class="mb-md text-red">Add Task</h3>
                    <form action="planning.php?event_id=<?= $selectedEventId ?>#tasks" method="POST">
                        <input type="hidden" name="event_id" value="<?= $selectedEventId ?>">

                        <div class="form-group">
                            <label class="form-label">Task Title *</label>
                            <input type="text" name="task-title" class="form-input" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <input type="text" name="task-desc" class="form-input">
                        </div>

                        <button class="btn btn-primary btn-block">Add Task</button>
                    </form>
                </div>
            </div>

        <?php else: ?>
            <div class="card" style="text-align:center;background:#fff3cd;border:1px solid #ffeeba;">
                Select an event from the sidebar to view its tasks.
            </div>
        <?php endif; ?>

    </div>

    <!-- RIGHT SIDEBAR -->
    <div class="sidebar-right">

        <!-- EVENT SELECTION -->
        <div class="sidebar-section">
            <h3 class="text-red mb-sm">Select Event</h3>
            <form method="GET" action="planning.php">
                <select name="event_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Choose Event --</option>
                    <?php foreach ($events as $event): ?>
                        <option value="<?= $event['id'] ?>" <?= $selectedEventId == $event['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($event['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>

        <!-- EVENT STATUS CONTROL -->
        <?php if ($currentEvent): ?>
            <div class="sidebar-section">
                <h3 class="text-red mb-sm">Status</h3>
                <p><strong>Current:</strong>
                    <span style="color:<?= $currentEvent['status'] == 'Posted' ? 'green' : 'orange' ?>">
                        <?= htmlspecialchars($currentEvent['status']) ?>
                    </span>
                </p>
                <p>
                    <strong>Attendees:</strong>
                        <span style="font-weight:600; color:#333;">
                            <?= $attendeeCount ?>
                        </span>
                </p>

                <form action="update_event_status.php" method="POST">
                    <input type="hidden" name="event_id" value="<?= $currentEvent['id'] ?>">
                    <select name="status" class="form-select mb-sm">
                        <option value="Posted" <?= $currentEvent['status'] == 'Posted' ? 'selected' : '' ?>>Posted</option>
                        <option value="Draft" <?= $currentEvent['status'] == 'Draft' ? 'selected' : '' ?>>Draft</option>
                    </select>
                    <button class="btn btn-primary btn-block">Update</button>
                </form>
            </div>

            <!-- EVENT DETAILS EDITOR -->
            <div class="sidebar-section">
                <h3 class="text-red mb-sm">Edit Event Description</h3>

                <form action="update_event_description.php" method="POST">
                    <input type="hidden" name="event_id" value="<?= $currentEvent['id'] ?>">

                    <textarea 
                        name="details" 
                        class="form-textarea" 
                        required 
                        style="min-height:120px;"
                    ><?= htmlspecialchars($currentEvent['details'] ?? '') ?></textarea>

                    <button class="btn btn-primary btn-block mt-sm">Save Description</button>
                </form>
            </div>

            <!-- DELETE EVENT -->
            <div class="sidebar-section">
                <h3 class="text-red mb-sm">Delete Event</h3>

                <form 
                    action="delete_event.php" 
                    method="POST"
                    onsubmit="return confirm('⚠️ Are you sure you want to delete this event? This cannot be undone.');"
                >
                    <input type="hidden" name="event_id" value="<?= $currentEvent['id'] ?>">

                    <button class="btn btn-secondary btn-block" style="background:#A01729;">
                        🗑️ Delete Event
                    </button>
                </form>
            </div>
        <?php endif; ?>

        <!-- CHATBOT BUTTON -->
        <div class="sidebar-section">
            <a href="chatbot.php<?= $selectedEventId ? '?event_id='.$selectedEventId : '' ?>" 
               class="btn btn-primary btn-block">
                🤖 Open Chatbot
            </a>
        </div>

    </div>

</div>
</body>
</html>
