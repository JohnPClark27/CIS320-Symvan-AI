<?php
// ===========================================
// ENROLL IN EVENTS PAGE
// ===========================================
session_start();
require_once 'db_connect.php';

$user_id = $_SESSION["user_id"] ?? null;
$successMessage = "";
$errorMessage = "";

// ===========================================
// HANDLE ENROLLMENT / UNENROLLMENT
// ===========================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!$user_id) {
        $errorMessage = "Please log in before enrolling in events.";
    } elseif (isset($_POST["event_ids"])) {
        // --- Enroll selected events ---
        $event_ids = $_POST["event_ids"];
        $stmt = $conn->prepare("INSERT IGNORE INTO enrollment (user_id, event_id) VALUES (?, ?)");
        foreach ($event_ids as $event_id) {
            $stmt->bind_param("ii", $user_id, $event_id);
            $stmt->execute();
        }
        $stmt->close();
        $successMessage = "Successfully enrolled in selected events!";
    } elseif (isset($_POST["unenroll_id"])) {
        // --- Unenroll from an event ---
        $unenroll_id = intval($_POST["unenroll_id"]);
        $stmt = $conn->prepare("DELETE FROM enrollment WHERE user_id = ? AND event_id = ?");
        $stmt->bind_param("ii", $user_id, $unenroll_id);
        $stmt->execute();
        $stmt->close();
        $successMessage = "You have been unenrolled from the event.";
    }
}

// ===========================================
// FETCH ONLY UPCOMING POSTED EVENTS
// ===========================================
$eventsQuery = "
    SELECT 
        e.id, e.name, e.details, e.date, e.start_time, e.end_time, e.location, 
        o.name AS org_name,
        CASE WHEN en.user_id IS NOT NULL THEN 1 ELSE 0 END AS enrolled
    FROM event e
    LEFT JOIN organization o ON e.organization_id = o.id
    LEFT JOIN enrollment en ON en.event_id = e.id AND en.user_id = ?
    WHERE e.status = 'Posted'
      AND e.date >= CURDATE()
    ORDER BY e.date ASC
";
$stmt = $conn->prepare($eventsQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$events = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enroll in Events - Symvan</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- ===================================
     NAVIGATION BAR
     =================================== -->
<nav class="navbar">
    <div class="navbar-container">

        <!-- LEFT - Brand -->
        <a href="index.php" class="navbar-brand">Symvan</a>

        <!-- CENTER - Menu -->
        <ul class="navbar-center-menu">
            <li><a href="index.php" >Home</a></li>
            <li><a href="myevents.php">My Events</a></li>
            <li><a href="enroll.php" class="active">Browse Events</a></li>
            <li><a href="organization.php">Organizations</a></li>
            <li><a href="create_event.php">Create Event</a></li>
            <li><a href="planning.php" >Planning</a></li>
            <li><a href="profile.php">Profile</a></li>
        </ul>

        <!-- RIGHT - User session -->
        <div class="navbar-right">
            <?php if (isset($_SESSION['email'])): ?>
                <span class="navbar-email">👋 <?= htmlspecialchars($_SESSION['email']) ?></span>
                <a href="logout.php" class="btn btn-outline btn-sm">Logout</a>
            <?php endif; ?>
        </div>

    </div>
</nav>

<!-- ===========================================
     MAIN CONTENT
     =========================================== -->
<div class="container">
    <div class="page-header">
        <h1 class="page-title">Browse Events</h1>
        <p class="page-subtitle">Select events you'd like to attend</p>
    </div>

    <?php if ($successMessage): ?>
        <div class="card" style="background:#d4edda;border:1px solid #28a745;color:#155724;text-align:center;margin-bottom:1rem;">
            <?= htmlspecialchars($successMessage) ?>
        </div>
    <?php elseif ($errorMessage): ?>
        <div class="card" style="background:#f8d7da;border:1px solid #dc3545;color:#721c24;text-align:center;margin-bottom:1rem;">
            <?= htmlspecialchars($errorMessage) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="enroll.php">
        <div class="event-grid">
            <?php if (!empty($events)): ?>
                <?php foreach ($events as $event): ?>
                    <div class="card event-card" style="<?= $event['enrolled'] ? 'border:2px solid #28a745;' : '' ?>">
                        <?php if (!$event['enrolled']): ?>
                            <input type="checkbox" name="event_ids[]" value="<?= $event['id'] ?>" class="event-checkbox">
                        <?php endif; ?>

                        <div class="card-header">
                            <h3 class="card-title"><?= htmlspecialchars($event['name']) ?></h3>
                            <div class="card-meta">
                                <span class="card-meta-item">📅 <?= date('M d, Y', strtotime($event['date'])) ?></span>
                                <span class="card-meta-item">🕐 
                                    <?= htmlspecialchars($event['start_time']) ?>
                                    <?php if ($event['end_time']) echo ' - ' . htmlspecialchars($event['end_time']); ?>
                                </span>
                            </div>
                        </div>

                        <div class="card-body">
                            <p><strong>Location:</strong> <?= htmlspecialchars($event['location']) ?></p>
                            <p><?= htmlspecialchars($event['details']) ?></p>
                        </div>

                        <div class="card-footer">
                            <span>Hosted by <?= htmlspecialchars($event['org_name']) ?></span>
                            <?php if ($event['enrolled']): ?>
                                <form action="enroll.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="unenroll_id" value="<?= $event['id'] ?>">
                                    <button type="submit" class="btn btn-outline btn-sm" style="margin-left:10px;">Unenroll</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No upcoming events available for enrollment.</p>
            <?php endif; ?>
        </div>

        <?php if (!empty($events)): ?>
            <div class="text-center mt-lg">
                <button type="submit" class="btn btn-primary btn-block confirm-btn" style="max-width:400px;margin:0 auto;">
                    Confirm Enrollment
                </button>
            </div>
        <?php endif; ?>
    </form>
</div>
</body>
</html>
