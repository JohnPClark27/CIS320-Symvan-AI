<?php

session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ===========================================
// DATABASE CONNECTION (XAMPP)
// ===========================================
require_once 'db_connect.php';

$user_id = $_SESSION['user_id'];
// ===========================================
// FETCH STATS
// ===========================================

// Total number of events
$totalEventsQuery = $conn->query("SELECT COUNT(*) AS total FROM event");
$totalEvents = $totalEventsQuery->fetch_assoc()["total"];

// Total participants (placeholder until attendees table or join exists)
$totalParticipantsQuery = $conn->query("SELECT COUNT(*) AS total FROM enrollment");
$totalParticipants = $totalParticipantsQuery->fetch_assoc()["total"];

// Upcoming events this week
$upcomingWeekQuery = $conn->query("
    SELECT COUNT(*) AS total 
    FROM event 
    WHERE date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) AND status = 'Posted'
");
$upcomingWeek = $upcomingWeekQuery->fetch_assoc()["total"];

$enrolledEventsQuery = $conn->query("SELECT COUNT(*) AS total FROM enrollment WHERE user_id = " . intval($user_id));
$enrolledEvents = $enrolledEventsQuery->fetch_assoc()["total"];

// ===========================================
// FETCH ALL UPCOMING EVENTS
// ===========================================
$eventsQuery = "
    SELECT e.id, e.name, e.details, e.date, e.start_time, e.end_time, e.location, e.attendees, o.name AS org_name
    FROM event e
    LEFT JOIN organization o ON e.organization_id = o.id
    WHERE e.date >= CURDATE() AND e.status = 'Posted' AND date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
    ORDER BY e.date ASC
";

$eventsResult = $conn->query($eventsQuery);
$events = $eventsResult->fetch_all(MYSQLI_ASSOC);
// Update attendees count for each event
foreach ($events as $event) {
    $eventID = $event['id'];
    $attendeesQuery = $conn->query("SELECT COUNT(*) AS total FROM enrollment WHERE event_id = " . intval($eventID));
    $attendeesCount = $attendeesQuery->fetch_assoc()["total"];
    $conn->query("UPDATE event SET attendees = " . intval($attendeesCount) . " WHERE id = " . intval($eventID));

}


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Symvan</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>


    <!-- ===================================
         NAVIGATION BAR
         =================================== -->
    <?php $activePage = 'index'; ?>
    <?php include 'components/navbar.php'; ?>

    <!-- ===================================
         DASHBOARD PAGE
         =================================== -->
    <div class="container">
        <!-- Welcome Section -->
        <div class="page-header">
            <h1 class="page-title">Welcome to Symvan</h1>
            <p class="page-subtitle">Your campus event management hub</p>
        </div>

        <!-- Quick Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $enrolledEvents; ?></div>
                <div class="stat-label">Enrolled Events</div>
            </div>

            <div class="stat-card">
                <div class="stat-number"><?php echo $upcomingWeek; ?></div>
                <div class="stat-label">Upcoming This Week</div>
            </div>

            <div class="stat-card">
                <div class="stat-number"><?php echo $totalEvents; ?></div>
                <div class="stat-label">Events Created</div>
            </div>

            <div class="stat-card">
                <div class="stat-number"><?php echo $totalParticipants; ?></div>
                <div class="stat-label">Total Participants</div>
            </div>
        </div>

        <!-- Upcoming Events Section -->
        <section>
            <h2 class="mb-md">Upcoming Events</h2>
            <div class="grid grid-2">
                <?php if (!empty($events)): ?>
                    <?php foreach ($events as $event): ?>
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"><?php echo htmlspecialchars($event['name']); ?></h3>
                                <div class="card-meta">
                                    <span class="card-meta-item">📅 <?php echo date('M d, Y', strtotime($event['date'])); ?></span>
                                    <span class="card-meta-item">🕐 <?php echo htmlspecialchars($event['start_time']); ?><?php if ($event['end_time']) echo ' - ' . htmlspecialchars($event['end_time']); ?></span>
                                    <span class="card-meta-item">📍 <?php echo htmlspecialchars($event['location']); ?></span>
                                    <?php if (!empty($event['org_name'])): ?>
                                        <span class="card-meta-item">🏛 <?php echo htmlspecialchars($event['org_name']); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="card-body">
                                <p><?php echo htmlspecialchars($event['details']); ?></p>
                            </div>
                            <div class="card-footer">
                                <span><?php echo htmlspecialchars($event['attendees']); ?> attendees</span>
                                <a href="enroll.php" class="btn btn-primary">Enroll</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No upcoming events found.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- Quick Actions -->
        <section class="mt-lg">
            <h2 class="mb-md">Quick Actions</h2>
            <div class="grid grid-3">
                <a href="enroll.php" class="btn btn-primary" style="padding: var(--spacing-lg);">
                    Browse All Events
                </a>
                <a href="create-event.php" class="btn btn-outline" style="padding: var(--spacing-lg);">
                    Create New Event
                </a>
                <a href="planning.php" class="btn btn-secondary" style="padding: var(--spacing-lg);">
                    Planning Board
                </a>
            </div>
        </section>
    </div>
</body>
</html>