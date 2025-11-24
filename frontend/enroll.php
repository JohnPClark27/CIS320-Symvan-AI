<?php
// ===========================================
// ENROLL IN EVENTS PAGE
// ===========================================
session_start();
require_once 'db_connect.php';
require_once 'audit.php';

$user_id = $_SESSION["user_id"] ?? null;
$successMessage = "";
$errorMessage = "";

// ===========================================
// READ FILTERS (FROM GET OR POST, WITH DEFAULTS)
// ===========================================
$filter_mode = $_GET['filter_mode'] ?? $_POST['filter_mode'] ?? 'all';
$valid_modes = ['all', 'my_orgs', 'admin_orgs', 'specific_org'];
if (!in_array($filter_mode, $valid_modes, true)) {
    $filter_mode = 'all';
}

$filter_org_id = null;
if (isset($_GET['org_id'])) {
    $filter_org_id = (int) $_GET['org_id'];
} elseif (isset($_POST['org_id'])) {
    $filter_org_id = (int) $_POST['org_id'];
}

// If not logged in, org-based filters don't make sense → fall back to "all"
if (!$user_id && $filter_mode !== 'all') {
    $filter_mode = 'all';
    $filter_org_id = null;
}

// ===========================================
// HANDLE ENROLLMENT / UNENROLLMENT
// ===========================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!$user_id) {
        $errorMessage = "Please log in before enrolling in events.";
    } elseif (isset($_POST["event_ids"])) {
        // --- Enroll in an event (single ID wrapped in array) ---
        $event_ids = $_POST["event_ids"];
        $stmt = $conn->prepare("INSERT IGNORE INTO enrollment (user_id, event_id) VALUES (?, ?)");
        foreach ($event_ids as $event_id) {
            $event_id = (int) $event_id;
            $stmt->bind_param("ii", $user_id, $event_id);
            $stmt->execute();
            log_audit($conn, $user_id, "Enrolled in event ID $event_id", $event_id);
        }
        $stmt->close();
        $successMessage = "Successfully enrolled in the event!";
    } elseif (isset($_POST["unenroll_id"])) {
        // --- Unenroll from an event ---
        $unenroll_id = (int)$_POST["unenroll_id"];
        $stmt = $conn->prepare("DELETE FROM enrollment WHERE user_id = ? AND event_id = ?");
        $stmt->bind_param("ii", $user_id, $unenroll_id);
        $stmt->execute();
        $stmt->close();
        log_audit($conn, $user_id, "Unenrolled from event ID $unenroll_id", $unenroll_id);
        $successMessage = "You have been unenrolled from the event.";
    }
}

// ===========================================
// FETCH ORGANIZATIONS THE USER BELONGS TO
// ===========================================
$userOrganizations = [];
if ($user_id) {
    $orgQuery = "
        SELECT o.id, o.name, m.permission_level
        FROM member m
        JOIN organization o ON o.id = m.organization_id
        WHERE m.user_id = ?
        ORDER BY o.name ASC
    ";
    $stmtOrg = $conn->prepare($orgQuery);
    $stmtOrg->bind_param("i", $user_id);
    $stmtOrg->execute();
    $userOrganizations = $stmtOrg->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmtOrg->close();
}

// Treat null as 0 for binding
$userParam = $user_id ?? 0;

// ===========================================
// FETCH EVENTS WITH FILTERS
// ===========================================
if ($filter_mode === 'my_orgs') {

    $eventsQuery = "
        SELECT 
            e.id, e.name, e.details, e.date, e.start_time, e.end_time, e.location,
            o.name AS org_name,
            CASE WHEN en.user_id IS NOT NULL THEN 1 ELSE 0 END AS enrolled
        FROM event e
        LEFT JOIN organization o ON e.organization_id = o.id
        LEFT JOIN enrollment en ON en.event_id = e.id AND en.user_id = ?
        INNER JOIN member m_filter ON m_filter.organization_id = e.organization_id 
            AND m_filter.user_id = ?
        WHERE e.status = 'Posted' AND e.date >= CURDATE()
        ORDER BY e.date ASC
    ";
    $stmt = $conn->prepare($eventsQuery);
    $stmt->bind_param("ii", $userParam, $userParam);

} elseif ($filter_mode === 'admin_orgs') {

    $eventsQuery = "
        SELECT 
            e.id, e.name, e.details, e.date, e.start_time, e.end_time, e.location,
            o.name AS org_name,
            CASE WHEN en.user_id IS NOT NULL THEN 1 ELSE 0 END AS enrolled
        FROM event e
        LEFT JOIN organization o ON e.organization_id = o.id
        LEFT JOIN enrollment en ON en.event_id = e.id AND en.user_id = ?
        INNER JOIN member m_filter ON m_filter.organization_id = e.organization_id 
            AND m_filter.user_id = ?
            AND m_filter.permission_level = 'Admin'
        WHERE e.status = 'Posted' AND e.date >= CURDATE()
        ORDER BY e.date ASC
    ";
    $stmt = $conn->prepare($eventsQuery);
    $stmt->bind_param("ii", $userParam, $userParam);

} elseif ($filter_mode === 'specific_org' && $filter_org_id) {

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
          AND e.organization_id = ?
        ORDER BY e.date ASC
    ";
    $stmt = $conn->prepare($eventsQuery);
    $stmt->bind_param("ii", $userParam, $filter_org_id);

} else {

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
    $stmt->bind_param("i", $userParam);
}

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

<?php $activePage = 'enroll'; ?>
<?php include 'components/navbar.php'; ?>
<?php include 'components/footer.php'; ?>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">Browse Events</h1>
        <p class="page-subtitle">Enroll or unenroll instantly, with filters by organization.</p>
    </div>

    <?php if ($successMessage): ?>
        <div class="card success-card"><?= htmlspecialchars($successMessage) ?></div>
    <?php elseif ($errorMessage): ?>
        <div class="card error-card"><?= htmlspecialchars($errorMessage) ?></div>
    <?php endif; ?>

    <!-- ===========================================
         CLEAN INLINE FILTER BAR (OPTION 1)
         =========================================== -->
    <form method="GET" action="enroll.php" class="filter-bar">
        <div class="filter-group">
            <label>Show</label>
            <select name="filter_mode" onchange="toggleOrgDropdown()">
                <option value="all" <?= $filter_mode === 'all' ? 'selected' : '' ?>>All events</option>
                <option value="my_orgs" <?= $filter_mode === 'my_orgs' ? 'selected' : '' ?>>My organizations</option>
                <option value="admin_orgs" <?= $filter_mode === 'admin_orgs' ? 'selected' : '' ?>>Admin organizations</option>
                <option value="specific_org" <?= $filter_mode === 'specific_org' ? 'selected' : '' ?>>Specific organization</option>
            </select>
        </div>

        <div class="filter-group <?= $filter_mode !== 'specific_org' ? 'disabled' : '' ?>">
            <label>Organization</label>
            <select name="org_id" id="org_id" <?= $filter_mode !== 'specific_org' ? 'disabled' : '' ?>>
                <option value="">-- Select --</option>
                <?php foreach ($userOrganizations as $org): ?>
                    <option value="<?= $org['id'] ?>" <?= ($filter_org_id === (int)$org['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($org['name']) ?> (<?= $org['permission_level'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button class="btn btn-primary">Filter</button>
    </form>

    <style>
        .filter-bar {
            display: flex;
            align-items: flex-end;
            gap: 1.2rem;
            background: #fff;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
        }
        .filter-group {
            display: flex;
            flex-direction: column;
        }
        .filter-group.disabled {
            opacity: 0.5;
            pointer-events: none;
        }
        .filter-group label {
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        .filter-group select {
            padding: 6px;
            border-radius: 6px;
        }
        .success-card {
            background:#d4edda;border:1px solid #28a745;color:#155724;text-align:center;margin-bottom:1rem;padding:.75rem;border-radius:8px;
        }
        .error-card {
            background:#f8d7da;border:1px solid #dc3545;color:#721c24;text-align:center;margin-bottom:1rem;padding:.75rem;border-radius:8px;
        }
    </style>

    <script>
        function toggleOrgDropdown() {
            const mode = document.querySelector('select[name="filter_mode"]').value;
            const orgSelect = document.getElementById('org_id');
            orgSelect.disabled = (mode !== 'specific_org');
        }
    </script>

    <!-- ===========================================
         EVENT GRID
         =========================================== -->
    <div class="event-grid">
        <?php if (!empty($events)): ?>
            <?php foreach ($events as $event): ?>
                <div class="card event-card" style="<?= $event['enrolled'] ? 'border:2px solid #28a745;' : '' ?>">
                    <div class="card-header">
                        <h3 class="card-title"><?= htmlspecialchars($event['name']) ?></h3>
                        <div class="card-meta">
                            <span class="card-meta-item">📅 <?= date('M d, Y', strtotime($event['date'])) ?></span>
                            <span class="card-meta-item">🕐 <?= htmlspecialchars($event['start_time']) ?><?php if ($event['end_time']) echo ' - ' . htmlspecialchars($event['end_time']); ?></span>
                        </div>
                    </div>

                    <div class="card-body">
                        <p><strong>Location:</strong> <?= htmlspecialchars($event['location']) ?></p>
                        <p><?= htmlspecialchars($event['details']) ?></p>
                    </div>

                    <div class="card-footer">
                        <span>Hosted by <?= htmlspecialchars($event['org_name']) ?></span>

                        <?php if ($event['enrolled']): ?>
                            <form method="POST" action="enroll.php" style="display:inline;">
                                <input type="hidden" name="unenroll_id" value="<?= $event['id'] ?>">
                                <input type="hidden" name="filter_mode" value="<?= htmlspecialchars($filter_mode) ?>">
                                <?php if ($filter_org_id): ?>
                                    <input type="hidden" name="org_id" value="<?= (int)$filter_org_id ?>">
                                <?php endif; ?>
                                <button class="btn btn-outline btn-sm" style="margin-left:10px;">Unenroll</button>
                            </form>
                        <?php else: ?>
                            <form method="POST" action="enroll.php" style="display:inline;">
                                <input type="hidden" name="event_ids[]" value="<?= $event['id'] ?>">
                                <input type="hidden" name="filter_mode" value="<?= htmlspecialchars($filter_mode) ?>">
                                <?php if ($filter_org_id): ?>
                                    <input type="hidden" name="org_id" value="<?= (int)$filter_org_id ?>">
                                <?php endif; ?>
                                <button class="btn btn-primary btn-sm" style="margin-left:10px;">Enroll</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No upcoming events match your filter.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
