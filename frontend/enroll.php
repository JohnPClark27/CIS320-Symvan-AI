<?php
// ===========================================
// ENROLL IN EVENTS PAGE
// ===========================================
session_start();

// ===========================================
// DATABASE CONNECTION (XAMPP)
require_once 'db_connect.php';

// ===========================================
// HANDLE ENROLLMENT SUBMISSION
// ===========================================
$successMessage = "";
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["event_ids"])) {
    if (!isset($_SESSION["user_id"])) {
        $errorMessage = "Please log in before enrolling in events.";
    } else {
        $user_id = $_SESSION["user_id"];
        $event_ids = $_POST["event_ids"];

        $stmt = $conn->prepare("INSERT IGNORE INTO enrollment (user_id, event_id) VALUES (?, ?)");
        foreach ($event_ids as $event_id) {
            $stmt->bind_param("ii", $user_id, $event_id);
            $stmt->execute();
        }
        $stmt->close();

        $successMessage = "Successfully enrolled in selected events!";
    }
}

// ===========================================
// FETCH EVENTS FROM DATABASE
// ===========================================
$eventsQuery = "
    SELECT e.id, e.name, e.details, e.date, e.start_time, e.end_time, e.location, o.name AS org_name
    FROM event e
    LEFT JOIN organization o ON e.organization_id = o.id
    WHERE e.status = 'Posted'
    ORDER BY e.date ASC
";
$eventsResult = $conn->query($eventsQuery);
$events = $eventsResult->fetch_all(MYSQLI_ASSOC);

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
<!-- NAVBAR -->
<nav class="navbar">
    <div class="navbar-container">
        <a href="index.php" class="navbar-brand">Symvan</a>
        <ul class="navbar-menu">
            <li><a href="index.php">Home</a></li>
            <li><a href="calendar.php">Calendar</a></li>
            <li><a href="myevents.php">My Events</a></li>
            <li><a href="enroll.php" class="active">Enroll</a></li>
            <li><a href="organization.php">Organizations</a></li>
            <li><a href="create_event.php">Create Event</a></li>
            <li><a href="profile.php">Profile</a></li>
        </ul>
        <div class="user-session">
            <?php if (isset($_SESSION['email'])): ?>
                <span class="welcome-text">👋 <?= htmlspecialchars($_SESSION['email']); ?></span>
                <a href="logout.php" class="btn btn-outline btn-sm">Logout</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">Browse Events</h1>
        <p class="page-subtitle">Select events you'd like to attend</p>
    </div>

    <?php if (!empty($successMessage)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($successMessage); ?></div>
    <?php endif; ?>

    <?php if (!empty($errorMessage)): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($errorMessage); ?></div>
    <?php endif; ?>

    <form method="POST" action="enroll.php">
        <div class="event-grid">
            <?php if (!empty($events)): ?>
                <?php foreach ($events as $event): ?>
                    <div class="card event-card">
                        <input type="checkbox" name="event_ids[]" value="<?php echo $event['id']; ?>" class="event-checkbox">

                        <div class="card-header">
                            <h3 class="card-title"><?php echo htmlspecialchars($event['name']); ?></h3>
                            <div class="card-meta">
                                <span class="card-meta-item">📅 <?php echo date('M d, Y', strtotime($event['date'])); ?></span>
                                <span class="card-meta-item">🕐 <?php echo htmlspecialchars($event['start_time']); ?><?php if ($event['end_time']) echo ' - ' . htmlspecialchars($event['end_time']); ?></span>
                            </div>
                        </div>
                        <div class="card-body">
                            <p><strong>Location:</strong> <?php echo htmlspecialchars($event['location']); ?></p>
                            <p><?php echo htmlspecialchars($event['details']); ?></p>
                        </div>
                        <div class="card-footer">
                            <span>Hosted by <?php echo htmlspecialchars($event['org_name']); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No upcoming events available for enrollment.</p>
            <?php endif; ?>
        </div>

        <div class="text-center mt-lg">
            <button type="submit" class="btn btn-primary btn-block confirm-btn" style="max-width: 400px; margin: 0 auto;">
                Confirm Enrollment
            </button>
        </div>
    </form>
</div>
</body>
</html>
