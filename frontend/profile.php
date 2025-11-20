<?php
// ===========================================
// PROFILE PAGE
// ===========================================
session_start();

require_once 'audit.php';

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
// FETCH USER DATA
// ===========================================
$userQuery = $conn->prepare("SELECT username, email FROM user WHERE id = ?");
$userQuery->bind_param("i", $user_id);
$userQuery->execute();
$user = $userQuery->get_result()->fetch_assoc();
$userQuery->close();

// Fetch profile info
$profileQuery = $conn->prepare("SELECT * FROM user_profile WHERE user_id = ?");
$profileQuery->bind_param("i", $user_id);
$profileQuery->execute();
$profile = $profileQuery->get_result()->fetch_assoc();
$profileQuery->close();

$successMessage = "";
$errorMessage = "";

// ===========================================
// HANDLE PROFILE UPDATE
// ===========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {

    $phone = trim($_POST['phone']);
    $major = trim($_POST['major']);
    $year  = trim($_POST['year']);
    $graduation_year = trim($_POST['graduation_year']);
    $interests = isset($_POST['interests']) ? implode(", ", $_POST['interests']) : "";

    $stmt = $conn->prepare("
        INSERT INTO user_profile (user_id, phone, major, year, graduation_year, interests)
        VALUES (?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            phone = VALUES(phone),
            major = VALUES(major),
            year = VALUES(year),
            graduation_year = VALUES(graduation_year),
            interests = VALUES(interests)
    ");

    $stmt->bind_param("isssis", $user_id, $phone, $major, $year, $graduation_year, $interests);
    $stmt->execute();
    $stmt->close();

    log_audit($conn, $user_id, "Updated profile information",null);

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
        $update->close();
        $successMessage = "Password updated successfully!";

        log_audit($conn, $user_id, "Changed account password",null);
    }
}

// ===========================================
// HANDLE ACCOUNT DELETE
// ===========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {

    // PROFILE
    $stmt = $conn->prepare("DELETE FROM user_profile WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // ENROLLMENTS
    $stmt = $conn->prepare("DELETE FROM enrollment WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // MEMBERSHIPS
    $stmt = $conn->prepare("DELETE FROM member WHERE user_id = ?");
    $stmt->bind_bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // TASKS
    $stmt = $conn->prepare("DELETE FROM task WHERE created_by = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // USER ACCOUNT
    $stmt = $conn->prepare("DELETE FROM user WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    session_destroy();

    header("Location: login.php?deleted=1");
    exit();
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
    <?php $activePage = 'profile'; ?>
    <?php include 'components/navbar.php'; ?>

<div class="container">
    <div class="profile-container">

        <div class="page-header">
            <h1 class="page-title">My Profile</h1>
            <p class="page-subtitle">Manage your account and preferences</p>
        </div>

        <?php if ($successMessage): ?>
            <div class="alert alert-success"><?= htmlspecialchars($successMessage) ?></div>
        <?php elseif ($errorMessage): ?>
            <div class="alert alert-error"><?= htmlspecialchars($errorMessage) ?></div>
        <?php endif; ?>

        <!-- ===========================
             PERSONAL INFORMATION
        ============================ -->
        <div class="profile-section">
            <h3 class="profile-section-title">Personal Information</h3>

            <form action="profile.php" method="POST">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-input" value="<?= htmlspecialchars($user['username']); ?>" readonly>
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-input" value="<?= htmlspecialchars($user['email']); ?>" readonly>
                </div>

                <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <input type="tel" name="phone" class="form-input" value="<?= htmlspecialchars($profile['phone'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Major</label>
                    <input type="text" name="major" class="form-input" value="<?= htmlspecialchars($profile['major'] ?? ''); ?>">
                </div>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Year</label>
                        <select name="year" class="form-select">
                            <?php
                            $years = ['Freshman','Sophomore','Junior','Senior','Graduate'];
                            $selectedYear = $profile['year'] ?? '';
                            foreach ($years as $yr) {
                                $sel = ($yr === $selectedYear) ? 'selected' : '';
                                echo "<option value='$yr' $sel>$yr</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Graduation Year</label>
                        <input type="number" name="graduation_year" class="form-input" min="1900" max="2100"
                            value="<?= htmlspecialchars($profile['graduation_year'] ?? ''); ?>">
                    </div>
                </div>

                <p class="form-label mt-md">Event Interests</p>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:var(--spacing-sm);">

                    <?php
                    $all = ['Academic','Career Development','Social','Sports & Recreation','Arts & Culture','Technology','Leadership','Community Service'];
                    $selected = explode(", ", $profile['interests'] ?? '');
                    foreach ($all as $i) {
                        $checked = in_array($i,$selected) ? 'checked' : '';
                        echo "<label><input type='checkbox' name='interests[]' value='$i' $checked> $i</label>";
                    }
                    ?>
                </div>

                <button type="submit" name="update_profile" class="btn btn-primary btn-block mt-md">Save Changes</button>
            </form>
        </div>

        <!-- ===========================
             CHANGE PASSWORD
        ============================ -->
        <div class="profile-section">
            <h3 class="profile-section-title">Change Password</h3>

            <form action="profile.php" method="POST">

                <div class="form-group">
                    <label class="form-label">Current Password</label>
                    <input type="password" name="current-password" class="form-input" required>
                </div>

                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input type="password" name="new-password" class="form-input" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="confirm-new-password" class="form-input" required>
                </div>

                <button type="submit" name="update_password" class="btn btn-primary btn-block">Update Password</button>
            </form>
        </div>

        <!-- ===========================
             DELETE ACCOUNT (DANGER ZONE)
        ============================ -->
        <div class="profile-section" 
            style="
                margin-top:2rem;
                padding:1.5rem;
                border:2px solid #cc0000;
                background:#ffe6e6;
                border-radius:10px;
            ">

            <h3 class="profile-section-title" 
                style="color:#cc0000; font-weight:800; margin-bottom:0.5rem;">
                ⚠️ Warning
            </h3>

            <p style="color:#660000; font-size:1rem; margin-bottom:1rem;">
                Deleting your account is <strong>permanent</strong>. All of your profile data,  
                memberships, enrollments, tasks, and login access will be erased forever.
            </p>

            <form action="profile.php" method="POST"
                onsubmit="return confirm('⚠️ Are you absolutely sure? This action cannot be undone.');">

                <button type="submit" name="delete_account"
                    class="btn btn-danger btn-block"
                    style="
                        background:#cc0000;
                        border:none;
                        font-size:1.1rem;
                        padding:0.75rem;
                        font-weight:700;
                    ">
                    Delete My Account Permanently
                </button>
            </form>
        </div>

    </div>
</div>

</body>
</html>