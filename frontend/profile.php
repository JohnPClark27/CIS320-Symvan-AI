<?php
// ===========================================
// PROFILE PAGE
// ===========================================
session_start();

require_once 'audit.php'; // Include audit function

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ===========================================
// DATABASE CONNECTION
// ===========================================
require_once 'db_connect.php';

$user_id = $_SESSION['user_id'];
// ===========================================
// FETCH USER + PROFILE DATA
// ===========================================
$userQuery = $conn->prepare("SELECT username, email FROM user WHERE id = ?");
$userQuery->bind_param("i", $user_id);
$userQuery->execute();
$user = $userQuery->get_result()->fetch_assoc();
$userQuery->close();

// Fetch profile info if exists
$profileQuery = $conn->prepare("SELECT * FROM user_profile WHERE user_id = ?");
$profileQuery->bind_param("i", $user_id);
$profileQuery->execute();
$profile = $profileQuery->get_result()->fetch_assoc();
$profileQuery->close();

// ===========================================
// HANDLE PROFILE UPDATE
// ===========================================
$successMessage = "";
$errorMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $phone = trim($_POST['phone']);
    $major = trim($_POST['major']);
    $year = trim($_POST['year']);
    $graduation = $_POST['graduation'];
    $interests = isset($_POST['interests']) ? implode(", ", $_POST['interests']) : "";

    $stmt = $conn->prepare("
        INSERT INTO user_profile (user_id, phone, major, year, graduation_month, interests)
        VALUES (?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            phone = VALUES(phone),
            major = VALUES(major),
            year = VALUES(year),
            graduation_month = VALUES(graduation_month),
            interests = VALUES(interests)
    ");
    $stmt->bind_param("isssss", $user_id, $phone, $major, $year, $graduation, $interests);
    $stmt->execute();

    // Update audit_log
    log_audit($conn, $user_id, 'Updated profile information', $user_id);

    $stmt->close();

    $successMessage = "Profile updated successfully!";
}

// ===========================================
// HANDLE PASSWORD UPDATE
// ===========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    $current = $_POST['current-password'];
    $newpass = $_POST['new-password'];
    $confirm = $_POST['confirm-new-password'];

    $stmt = $conn->prepare("SELECT password_hash FROM user WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($hash);
    $stmt->fetch();
    $stmt->close();

    if (!password_verify($current, $hash)) {
        $errorMessage = "Current password is incorrect.";
    } elseif ($newpass !== $confirm) {
        $errorMessage = "New passwords do not match.";
    } else {
        $newHash = password_hash($newpass, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE user SET password_hash = ? WHERE id = ?");
        $update->bind_param("si", $newHash, $user_id);
        $update->execute();

        // Update audit_log
        log_audit($conn, $user_id, 'Updated account password', $user_id);

        $update->close();
        $successMessage = "Password updated successfully!";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Symvan</title>
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
            <li><a href="enroll.php">Enroll</a></li>
            <li><a href="organization.php">Organizations</a></li>
            <li><a href="create_event.php">Create Event</a></li>
            <li><a href="planning.php" >Planning</a></li>
            <li><a href="profile.php" class="active">Profile</a></li>
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

<div class="container">
    <div class="profile-container">
        <div class="page-header">
            <h1 class="page-title">My Profile</h1>
            <p class="page-subtitle">Manage your account and preferences</p>
        </div>

        <?php if ($successMessage): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($successMessage); ?></div>
        <?php elseif ($errorMessage): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>

        <!-- Personal Information -->
        <div class="profile-section">
            <h3 class="profile-section-title">Personal Information</h3>
            <form action="profile.php" method="POST">
                <div class="form-group">
                    <label for="full-name" class="form-label">Full Name</label>
                    <input type="text" id="full-name" class="form-input" value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" class="form-input" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="tel" id="phone" name="phone" class="form-input" value="<?php echo htmlspecialchars($profile['phone'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="major" class="form-label">Major</label>
                    <input type="text" id="major" name="major" class="form-input" value="<?php echo htmlspecialchars($profile['major'] ?? ''); ?>">
                </div>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label for="year" class="form-label">Year</label>
                        <select id="year" name="year" class="form-select">
                            <?php
                            $years = ['Freshman', 'Sophomore', 'Junior', 'Senior', 'Graduate'];
                            $selectedYear = $profile['year'] ?? '';
                            foreach ($years as $yr) {
                                $sel = ($yr === $selectedYear) ? 'selected' : '';
                                echo "<option value='$yr' $sel>$yr</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="graduation" class="form-label">Expected Graduation</label>
                        <input type="month" id="graduation" name="graduation" class="form-input" value="<?php echo htmlspecialchars($profile['graduation_month'] ?? ''); ?>">
                    </div>
                </div>

                <p class="form-label mt-md">Event Interests</p>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--spacing-sm);">
                    <?php
                    $allInterests = ['Academic','Career Development','Social','Sports & Recreation','Arts & Culture','Technology','Leadership','Community Service'];
                    $selected = explode(", ", $profile['interests'] ?? '');
                    foreach ($allInterests as $i) {
                        $checked = in_array($i, $selected) ? 'checked' : '';
                        echo "<label><input type='checkbox' name='interests[]' class='form-checkbox' value='$i' $checked> $i</label>";
                    }
                    ?>
                </div>

                <button type="submit" name="update_profile" class="btn btn-primary btn-block mt-md">Save Changes</button>
            </form>
        </div>

        <!-- Account Settings -->
        <div class="profile-section">
            <h3 class="profile-section-title">Change Password</h3>
            <form action="profile.php" method="POST">
                <div class="form-group">
                    <label for="current-password" class="form-label">Current Password</label>
                    <input type="password" id="current-password" name="current-password" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="new-password" class="form-label">New Password</label>
                    <input type="password" id="new-password" name="new-password" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="confirm-new-password" class="form-label">Confirm New Password</label>
                    <input type="password" id="confirm-new-password" name="confirm-new-password" class="form-input" required>
                </div>

                <button type="submit" name="update_password" class="btn btn-primary btn-block">Update Password</button>
            </form>
        </div>

        <!-- Logout -->
        <div class="grid grid mt-md">
            <a href="logout.php" class="btn btn-secondary btn-block">Logout</a>
        </div>
    </div>
</div>
</body>
</html>