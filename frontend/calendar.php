<?php
// ===========================================
// EVENT CALENDAR PAGE
// ===========================================
session_start();
require_once 'db_connect.php';

$user_id = $_SESSION['user_id'] ?? null;
$userIdForQuery = $user_id ?? 0;
// Toggle between enrolled-only and all events
$view = isset($_GET['view']) ? $_GET['view'] : 'all';
$showEnrolledOnly = ($view === 'enrolled');


// -------------------------------------------
// Determine month/year
// -------------------------------------------
$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
$year  = isset($_GET['year'])  ? (int)$_GET['year']  : (int)date('Y');

if ($month < 1) { $month = 12; $year--; }
if ($month > 12) { $month = 1; $year++; }

$firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
$totalDays       = (int)date('t', $firstDayOfMonth);
$startDayIndex   = (int)date('w', $firstDayOfMonth);

// Prev/Next month
$prevMonth = $month - 1;
$prevYear  = $year;
if ($prevMonth < 1) { $prevMonth = 12; $prevYear--; }

$nextMonth = $month + 1;
$nextYear  = $year;
if ($nextMonth > 12) { $nextMonth = 1; $nextYear++; }

// -------------------------------------------
// Fetch events for the month
// -------------------------------------------
// SQL: conditionally filter by enrolled events only
$sql = "
    SELECT 
        e.id,
        e.name,
        e.details,
        e.date,
        e.start_time,
        e.end_time,
        e.location,
        o.name AS org_name,
        CASE WHEN en.user_id IS NOT NULL THEN 1 ELSE 0 END AS enrolled
    FROM event e
    LEFT JOIN organization o ON e.organization_id = o.id
    LEFT JOIN enrollment en ON en.event_id = e.id AND en.user_id = ?
    WHERE e.status = 'Posted'
      AND e.date >= CURDATE()
      AND MONTH(e.date) = ?
      AND YEAR(e.date)  = ?
";

// add enrolled filter if needed
if ($showEnrolledOnly) {
    $sql .= " AND en.user_id IS NOT NULL ";
}

$sql .= " ORDER BY e.date ASC, e.start_time ASC ";


$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $userIdForQuery, $month, $year);
$stmt->execute();
$result = $stmt->get_result();
$eventsRaw = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$eventsByDay = [];
foreach ($eventsRaw as $event) {
    $day = (int)date('j', strtotime($event['date']));
    $eventsByDay[$day][] = $event;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Calendar - Symvan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">

    <!-- INLINE STYLES JUST FOR THIS CALENDAR (OVERRIDES ANY CONFLICTS) -->
    <style>
        .symcalendar-wrapper {
            max-width: 1000px;
            margin: 2rem auto;
        }

        .symcalendar-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.08);
            font-size: 0.95rem;
        }

        .symcalendar-table th {
            background: var(--cardinal-red);
            color: #fff;
            padding: 10px;
            text-align: center;
            font-weight: 600;
            border: 1px solid #d8d8d8;
        }

        .symcalendar-table td {
            height: 110px;
            padding: 6px;
            border: 1px solid #eee;
            vertical-align: top;
            background: #fff;
        }

        .symcalendar-day-number {
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 4px;
            color: var(--medium-grey);
        }

        .symcalendar-event {
            display: block;
            padding: 3px 6px;
            margin-top: 3px;
            font-size: 12px;
            border-radius: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
        }

        .symcalendar-event.not-enrolled {
            background: var(--cardinal-red);
            color: #fff;
        }

        .symcalendar-event.enrolled {
            background: #e6ffe9;
            color: #155724;
            border: 1px solid #28a745;
        }

        /* Make the month header match your cards */
        .symcalendar-wrapper .calendar-header {
            margin-bottom: 1rem;
        }

        /* POPUP OVERLAY */
        #symcalendar-popup {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.6);
            display: none;
            z-index: 2000;
        }

        #symcalendar-popup .popup-box {
            background: #fff;
            max-width: 520px;
            margin: 80px auto;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 6px 16px rgba(0,0,0,0.2);
            position: relative;
        }

        #symcalendar-popup .popup-close {
            position: absolute;
            top: 10px;
            right: 14px;
            font-size: 18px;
            cursor: pointer;
        }

        #symcalendar-popup-content {
            max-height: 70vh;
            overflow-y: auto;
        }

        .btn.active {
            opacity: 1;
            box-shadow: 0 0 0 2px var(--cardinal-red);
        }

        .btn.btn-primary:hover,
        .btn.btn-secondary:hover {
            color: white !important;
        }

    </style>

    <script>
        function openEventPopup(id) {
            fetch("calendar_event_details.php?id=" + id)
                .then(r => r.text())
                .then(html => {
                    document.getElementById("symcalendar-popup-content").innerHTML = html;
                    document.getElementById("symcalendar-popup").style.display = "block";
                })
                .catch(err => {
                    console.error(err);
                    alert("Error loading event details.");
                });
        }

        function closeEventPopup() {
            document.getElementById("symcalendar-popup").style.display = "none";
        }
    </script>
</head>

<body>

    <!-- ===================================
         NAVIGATION BAR
         =================================== -->
    <?php $activePage = 'calendar'; ?>
    <?php include 'components/navbar.php'; ?>
    <?php include 'components/footer.php'; ?>


<!-- PAGE CONTENT -->
<div class="container">

    <div class="page-header">
        <h1 class="page-title">Event Calendar</h1>
        <p class="page-subtitle">Browse upcoming posted events in a month-view format.</p>
    </div>

    <div class="symcalendar-wrapper card">

    <!-- VIEW TOGGLE BUTTONS -->
<div style="margin-bottom: 1rem; display:flex; gap:10px;">
    <a href="calendar.php?view=all" 
       class="btn btn-secondary <?= ($view === 'all') ? 'active' : '' ?>">
       Show All Events
    </a>

    <a href="calendar.php?view=enrolled" 
       class="btn btn-primary <?= ($view === 'enrolled') ? 'active' : '' ?>">
       Show My Enrolled Events
    </a>
</div>


        <!-- MONTH HEADER -->
        <div class="calendar-header">
            <a class="btn btn-secondary" href="calendar.php?month=<?= $prevMonth ?>&year=<?= $prevYear ?>">&laquo; Prev</a>
            <h2 class="calendar-month"><?= date("F Y", $firstDayOfMonth); ?></h2>
            <a class="btn btn-secondary" href="calendar.php?month=<?= $nextMonth ?>&year=<?= $nextYear ?>">Next &raquo;</a>
        </div>

        <!-- MONTH TABLE -->
        <table class="symcalendar-table">
            <thead>
                <tr>
                    <th>Sun</th><th>Mon</th><th>Tue</th>
                    <th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th>
                </tr>
            </thead>
            <tbody>
            <tr>

            <?php
            // Empty starting cells
            for ($i = 0; $i < $startDayIndex; $i++) {
                echo "<td></td>";
            }

            for ($day = 1; $day <= $totalDays; $day++) {

                echo "<td>";
                echo "<div class='symcalendar-day-number'>$day</div>";

                if (!empty($eventsByDay[$day])) {
                    foreach ($eventsByDay[$day] as $event) {
                        $class = $event['enrolled'] ? 'enrolled' : 'not-enrolled';
                        $title = htmlspecialchars($event['name']);
                        echo "<span class='symcalendar-event $class' onclick='openEventPopup(".$event['id'].")'>$title</span>";
                    }
                }

                echo "</td>";

                if ( ($startDayIndex + $day) % 7 === 0 ) {
                    echo "</tr><tr>";
                }
            }

            // Ending empty cells
            $endCells = (7 - (($startDayIndex + $totalDays) % 7)) % 7;
            for ($i = 0; $i < $endCells; $i++) {
                echo "<td></td>";
            }
            ?>

            </tr>
            </tbody>
        </table>

    </div>
</div>

<!-- POPUP OVERLAY -->
<div id="symcalendar-popup">
    <div class="popup-box">
        <span class="popup-close" onclick="closeEventPopup()">✖</span>
        <div id="symcalendar-popup-content"></div>
    </div>
</div>

</body>
</html>