<?php
// ===========================================
// MY EVENTS PAGE
// ===========================================
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ===========================================
// DATABASE CONNECTION
// ===========================================
require_once 'db_connect.php';

// ===========================================
// FETCH USER'S UPCOMING ENROLLED EVENTS
// ===========================================
$user_id = $_SESSION['user_id'];

$query = "
    SELECT 
        e.id, e.name, e.details, e.date, e.start_time, e.end_time, e.location, 
        o.name AS org_name
    FROM enrollment en
    INNER JOIN event e ON en.event_id = e.id
    LEFT JOIN organization o ON e.organization_id = o.id
    WHERE en.user_id = ?
      AND e.date >= CURDATE()       -- ✅ Only future or current events
    ORDER BY e.date ASC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$events = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Events - Symvan</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- ===========================================
     NAVIGATION BAR
     =========================================== -->
<nav class="navbar">
    <div class="navbar-container">
        <a href="index.php" class="navbar-brand">Symvan</a>
        <ul class="navbar-menu">
            <li><a href="index.php">Home</a></li>
            <li><a href="myevents.php" class="active">My Events</a></li>
            <li><a href="enroll.php">Browse Events</a></li>
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

<!-- ===========================================
     MAIN CONTENT
     =========================================== -->
<div class="container">
    <div class="page-header">
        <h1 class="page-title">My Enrolled Events</h1>
        <p class="page-subtitle">Here are your upcoming events</p>
    </div>

    <?php if (empty($events)): ?>
        <div class="card" style="background:#fff3cd;border:1px solid #ffeeba;color:#856404;text-align:center;">
            <p>You haven’t enrolled in any upcoming events yet.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-2">
            <?php foreach ($events as $event): ?>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><?= htmlspecialchars($event['name']) ?></h3>
                        <div class="card-meta">
                            <span class="card-meta-item">📅 <?= date('M d, Y', strtotime($event['date'])) ?></span>
                            <span class="card-meta-item">🕐 
                                <?= htmlspecialchars($event['start_time']) ?>
                                <?php if ($event['end_time']) echo ' - ' . htmlspecialchars($event['end_time']); ?>
                            </span>
                            <span class="card-meta-item">📍 <?= htmlspecialchars($event['location']) ?></span>
                        </div>
                    </div>

                    <div class="card-body">
                        <p><?= htmlspecialchars($event['details']) ?></p>
                        <p><strong>Organization:</strong> <?= htmlspecialchars($event['org_name']) ?></p>
                    </div>

                    <div class="card-footer" style="display:flex;justify-content:space-between;align-items:center;">
                        <span class="text-grey">Enrolled</span>
                        <form action="cancel_enrollment.php" method="POST" onsubmit="return confirm('Cancel your enrollment for this event?');">
                            <input type="hidden" name="event_id" value="<?= $event['id']; ?>">
                            <button type="submit" class="btn btn-secondary btn-sm">Cancel Enrollment</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
