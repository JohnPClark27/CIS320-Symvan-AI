<?php
// ===========================================
// USER EVENT CREATION PAGE
// ===========================================

// (Optional) Require login later
session_start();

// ===========================================
// DATABASE CONNECTION (XAMPP)
// ===========================================
require_once 'db_connect.php';


// ===========================================
// HANDLE FORM SUBMISSION - CREATE EVENT
// ===========================================
$successMessage = "";
$errorMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name       = $_POST['event-title'] ?? null;
    $details    = $_POST['event-description'] ?? null;
    $date       = $_POST['event-date'] ?? null;
    $start_time = $_POST['event-time'] ?? null;
    $end_time   = $_POST['event-end-time'] ?? null;
    $location   = $_POST['event-location'] ?? null;

    $organization_id = 1; // TODO: Replace with real org once implemented

    if ($name && $details && $date && $start_time && $location) {

        $stmt = $conn->prepare("
            INSERT INTO event (name, details, date, start_time, end_time, location, organization_id)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param("ssssssi", $name, $details, $date, $start_time, $end_time, $location, $organization_id);

        if ($stmt->execute()) {
            $successMessage = "Event created successfully!";
        } else {
            $errorMessage = "Failed to create event. Try again.";
        }

        $stmt->close();

    } else {
        $errorMessage = "Please fill out all required fields.";
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event - Symvan</title>
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
            <li><a href="enroll.php">Enroll</a></li>
            <li><a href="organization.php">Organizations</a></li>
            <li><a href="create-event.php" class="active">Create Event</a></li>
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

    <?php if (!empty($successMessage)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($successMessage); ?></div>
    <?php endif; ?>

    <?php if (!empty($errorMessage)): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($errorMessage); ?></div>
    <?php endif; ?>

    <div class="page-header">
        <h1 class="page-title">Create New Event</h1>
        <p class="page-subtitle">Fill out the details for your campus event</p>
    </div>

    <div class="form-container" style="max-width: 700px;">
        <form action="create-event.php" method="POST">

            <div class="form-group">
                <label for="event-title" class="form-label">Event Title *</label>
                <input type="text" id="event-title" name="event-title" class="form-input" required>
            </div>

            <div class="form-group">
                <label for="event-description" class="form-label">Event Description *</label>
                <textarea id="event-description" name="event-description" class="form-textarea" required></textarea>
            </div>

            <div class="grid grid-2">
                <div class="form-group">
                    <label for="event-date" class="form-label">Event Date *</label>
                    <input type="date" id="event-date" name="event-date" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="event-time" class="form-label">Start Time *</label>
                    <input type="time" id="event-time" name="event-time" class="form-input" required>
                </div>
            </div>

            <div class="form-group">
                <label for="event-end-time" class="form-label">End Time (Optional)</label>
                <input type="time" id="event-end-time" name="event-end-time" class="form-input">
            </div>

            <div class="form-group">
                <label for="event-location" class="form-label">Location *</label>
                <input type="text" id="event-location" name="event-location" class="form-input" required>
            </div>

            <div class="grid grid-2" style="margin-top: var(--spacing-lg);">
                <a href="index.php" class="btn btn-secondary btn-block">Cancel</a>
                <button type="submit" class="btn btn-primary btn-block">Create Event</button>
            </div>

        </form>
    </div>
</div>

</body>
</html>
