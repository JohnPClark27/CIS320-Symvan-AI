<?php
// ===========================================
// EVENT DETAILS FOR CALENDAR POPUP (AJAX)
// ===========================================
session_start();
require_once 'db_connect.php';

$user_id = $_SESSION['user_id'] ?? null;
$userIdForQuery = $user_id ?? 0;

$eventId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($eventId <= 0) {
    echo "Invalid event.";
    exit;
}

// Fetch single event with org + enrolled flag
$sql = "
    SELECT 
        e.id,
        e.name,
        e.details,
        e.date,
        e.start_time,
        e.end_time,
        e.location,
        e.status,
        o.name AS org_name,
        CASE WHEN en.user_id IS NOT NULL THEN 1 ELSE 0 END AS enrolled
    FROM event e
    LEFT JOIN organization o ON e.organization_id = o.id
    LEFT JOIN enrollment en ON en.event_id = e.id AND en.user_id = ?
    WHERE e.id = ?
      AND e.status = 'Posted'
    LIMIT 1
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $userIdForQuery, $eventId);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();
$stmt->close();

if (!$event) {
    echo "Event not found or not posted.";
    exit;
}

$dateStr  = date("F j, Y", strtotime($event['date']));
$timeStr  = htmlspecialchars($event['start_time']);
if (!empty($event['end_time'])) {
    $timeStr .= " - " . htmlspecialchars($event['end_time']);
}
$location = htmlspecialchars($event['location']);
$orgName  = $event['org_name'] ? htmlspecialchars($event['org_name']) : "Unknown organization";
$details  = nl2br(htmlspecialchars($event['details']));
$isEnrolled = (bool)$event['enrolled'];
?>
<h2 style="margin-top:0;"><?= htmlspecialchars($event['name']); ?></h2>

<p><strong>Date:</strong> <?= $dateStr; ?></p>
<p><strong>Time:</strong> <?= $timeStr; ?></p>
<p><strong>Location:</strong> <?= $location; ?></p>
<p><strong>Organization:</strong> <?= $orgName; ?></p>
<p style="margin-top:1rem;"><strong>Details:</strong><br><?= $details; ?></p>

<hr>

<?php if (!$user_id): ?>

    <p>You must <a href="login.php">log in</a> to enroll in events.</p>

<?php else: ?>

    <?php if ($isEnrolled): ?>

        <p style="color:#28a745;margin-bottom:0.5rem;">You are enrolled in this event.</p>

        <!-- NEW BUTTON: Go to My Events to Unenroll -->
        <a href="myevents.php" class="btn btn-secondary btn-block" style="margin-top:0.5rem;">
            Unenroll (Go to My Events)
        </a>

    <?php else: ?>

        <!-- NEW BUTTON: Enroll redirects to enroll.php -->
        <a href="enroll.php?event_id=<?= $event['id']; ?>" 
           class="btn btn-primary btn-block" style="margin-top:0.5rem;">
            Enroll in this Event
        </a>

    <?php endif; ?>

<?php endif; ?>