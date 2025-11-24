<?php
session_start();
require_once 'db_connect.php';

require_once 'audit.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$successMessage = "";
$errorMessage   = "";

// ===========================================
// FETCH ORGANIZATIONS WHERE USER IS AN ADMIN
// ===========================================
$adminOrgs = [];
$orgStmt = $conn->prepare("
    SELECT o.id, o.name 
    FROM organization o
    INNER JOIN member m ON o.id = m.organization_id
    WHERE m.user_id = ? AND m.permission_level = 'Admin'
    ORDER BY o.name ASC
");
$orgStmt->bind_param("i", $user_id);
$orgStmt->execute();
$adminOrgs = $orgStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$orgStmt->close();

// ===========================================
// HANDLE EVENT CREATION
// ===========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['event-title'] ?? '');
    $details     = trim($_POST['event-description'] ?? '');
    $date        = $_POST['event-date'] ?? '';
    $start_time  = $_POST['event-time'] ?? '';
    $end_time    = $_POST['event-end-time'] ?? null;
    $location    = trim($_POST['event-location'] ?? '');
    $status      = $_POST['event-status'] ?? 'Draft';
    $organization_id = intval($_POST['organization_id'] ?? 0);

    // Ensure the user is an admin of the selected organization
    $isAdminCheck = $conn->prepare("
        SELECT 1 FROM member 
        WHERE user_id = ? AND organization_id = ? AND permission_level = 'Admin'
        LIMIT 1
    ");
    $isAdminCheck->bind_param("ii", $user_id, $organization_id);
    $isAdminCheck->execute();
    $isAdmin = $isAdminCheck->get_result()->num_rows > 0;
    $isAdminCheck->close();

    if (!$isAdmin) {
        $errorMessage = "⚠️ You are not authorized to create events for that organization.";
    } elseif ($name && $details && $date && $start_time && $location) {
        // Check if event name already exists for the organization on the same date
        $checkStmt = $conn->prepare("
            SELECT id FROM event 
            WHERE name = ? AND organization_id = ? AND date = ?
            LIMIT 1
        ");
        $checkStmt->bind_param("sis", $name, $organization_id, $date);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            $errorMessage = "⚠️ An event with that name already exists for the selected organization on that date.";
        } else {
            $stmt = $conn->prepare("
                INSERT INTO event (name, details, date, start_time, end_time, location, organization_id, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("ssssssis", $name, $details, $date, $start_time, $end_time, $location, $organization_id, $status);
        }
        $checkStmt->close();
        
        if ($stmt->execute()) {
            $successMessage = "✅ Event created successfully!";
            log_audit($conn, $user_id, "Created event '$name' for organization ID $organization_id", $organization_id);
        } else {
            $errorMessage = "❌ Failed to create event. Try again.";
        }

        $stmt->close();
    } else {
        $errorMessage = "⚠️ Please fill out all required fields.";
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

    <!-- ===================================
         NAVIGATION BAR
         =================================== -->
    <?php $activePage = 'create_event'; ?>
    <?php include 'components/navbar.php'; ?>
    <?php include 'components/footer.php'; ?>

<!-- ===================================
     MAIN CONTENT
     =================================== -->
<div class="container">
    <div class="page-header">
        <h1 class="page-title">Create a New Event</h1>
        <p class="page-subtitle">
            Only organization admins can create events. Choose your organization below.
        </p>
    </div>

    <?php if (!empty($successMessage)): ?>
        <div class="card" style="text-align:center; margin-bottom: 1rem; background-color:#d4edda; border:1px solid #28a745; color:#155724;">
            <?= htmlspecialchars($successMessage) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($errorMessage)): ?>
        <div class="card" style="text-align:center; margin-bottom: 1rem; background-color:#f8d7da; border:1px solid #dc3545; color:#721c24;">
            <?= htmlspecialchars($errorMessage) ?>
        </div>
    <?php endif; ?>

    <?php if (empty($adminOrgs)): ?>
        <div class="card" style="text-align:center; background-color:#fff3cd; border:1px solid #ffeeba; color:#856404;">
            <p>⚠️ You are not an admin of any organization.<br>
            Join or become an admin in an organization before creating events.</p>
            <a href="organization.php" class="btn btn-primary mt-md">Go to Organizations</a>
        </div>
    <?php else: ?>

    <div class="form-container" style="max-width: 700px;">
        <form action="create_event.php" method="POST">

            <div class="form-group">
                <label for="organization_id" class="form-label">Organization *</label>
                <select name="organization_id" id="organization_id" class="form-select" required>
                    <option value="">Select organization</option>
                    <?php foreach ($adminOrgs as $org): ?>
                        <option value="<?= $org['id'] ?>"><?= htmlspecialchars($org['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

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

            <div class="form-group">
                <label for="event-status" class="form-label">Event Status *</label>
                <select name="event-status" id="event-status" class="form-select" required>
                    <option value="Draft">Draft (Save for later)</option>
                    <option value="Posted">Posted (Publish now)</option>
                </select>
            </div>

            <div class="grid grid-2 mt-lg">
                <a href="index.php" class="btn btn-secondary btn-block">Cancel</a>
                <button type="submit" class="btn btn-primary btn-block">Create Event</button>
            </div>
        </form>
    </div>
    <?php endif; ?>
</div>
</body>
</html>
