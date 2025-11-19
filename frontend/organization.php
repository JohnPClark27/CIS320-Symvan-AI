<?php
session_start();
require_once 'db_connect.php';

// Redirect to login if user is not signed in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// ============================================
// Handle joining an organization
// ============================================
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['organization_id'])) {
    $org_id = intval($_POST['organization_id']);
    $role = $_POST['role']; // "Member" or "Admin"

    // Check if already a member
    $check_stmt = $conn->prepare("SELECT id FROM member WHERE user_id = ? AND organization_id = ?");
    $check_stmt->bind_param("ii", $user_id, $org_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $message = "⚠️ You are already part of this organization.";
    } else {
        if ($role === "Admin") {
            $entered_password = $_POST['admin_password'] ?? '';

            $stmt = $conn->prepare("SELECT password FROM organization WHERE id = ?");
            $stmt->bind_param("i", $org_id);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();

            if ($res && $res['password'] === $entered_password) {
                $insert = $conn->prepare("INSERT INTO member (user_id, organization_id, permission_level) VALUES (?, ?, 'Admin')");
                $insert->bind_param("ii", $user_id, $org_id);
                $insert->execute();
                $message = "✅ Joined as admin successfully!";
            } else {
                $message = "❌ Incorrect admin password.";
            }
        } else {
            $insert = $conn->prepare("INSERT INTO member (user_id, organization_id, permission_level) VALUES (?, ?, 'Member')");
            $insert->bind_param("ii", $user_id, $org_id);
            $insert->execute();
            $message = "✅ Joined as member successfully!";
        }
    }
}

// ============================================
// Handle leaving an organization
// ============================================
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['leave_id'])) {
    $org_id = intval($_POST['leave_id']);

    $delete_stmt = $conn->prepare("DELETE FROM member WHERE user_id = ? AND organization_id = ?");
    $delete_stmt->bind_param("ii", $user_id, $org_id);
    $delete_stmt->execute();

    if ($delete_stmt->affected_rows > 0) {
        $message = "✅ You have left the organization successfully.";
    } else {
        $message = "⚠️ You were not part of that organization.";
    }
}

// ============================================
// Fetch all organizations
// ============================================
$orgs = [];
$result = $conn->query("SELECT id, name, description FROM organization ORDER BY name ASC");
if ($result && $result->num_rows > 0) {
    $orgs = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organizations - Symvan</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- ===================================
         NAVIGATION BAR
         =================================== -->
    <?php $activePage = 'organization'; ?>
    <?php include 'components/navbar.php'; ?>

<!-- ===================================
     PAGE CONTENT
     =================================== -->
<div class="container">
    <div class="page-header">
        <h1 class="page-title">Join or Manage Organizations</h1>
        <p class="page-subtitle">
            Join as a member instantly, or enter an admin password to join as an administrator.
        </p>
    </div>

    <?php if ($message): ?>
        <div class="card" style="margin-bottom: 1.5rem; text-align:center;">
            <p><?= htmlspecialchars($message) ?></p>
        </div>
    <?php endif; ?>

    <div class="event-grid">
        <?php foreach ($orgs as $org): ?>
            <?php
                // Check if user is already a member
                $isMemberStmt = $conn->prepare("SELECT permission_level FROM member WHERE user_id = ? AND organization_id = ?");
                $isMemberStmt->bind_param("ii", $user_id, $org['id']);
                $isMemberStmt->execute();
                $isMemberResult = $isMemberStmt->get_result();
                $isMember = $isMemberResult->num_rows > 0 ? $isMemberResult->fetch_assoc()['permission_level'] : null;
            ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><?= htmlspecialchars($org['name']) ?></h3>
                </div>
                <div class="card-body">
                    <p><?= htmlspecialchars($org['description']) ?></p>
                </div>
                <div class="card-footer">
                    <?php if ($isMember): ?>
                        <p><strong>You are a <?= htmlspecialchars($isMember) ?></strong></p>
                        <form method="POST">
                            <input type="hidden" name="leave_id" value="<?= $org['id'] ?>">
                            <button type="submit" class="btn btn-secondary btn-block">
                                Leave Organization
                            </button>
                        </form>
                    <?php else: ?>
                        <form method="POST" style="width:100%;">
                            <input type="hidden" name="organization_id" value="<?= $org['id'] ?>">
                            <div style="display:flex; flex-direction:column; gap:0.5rem;">
                                <button type="submit" name="role" value="Member" class="btn btn-primary btn-block">
                                    Join as Member
                                </button>
                                <input type="password" name="admin_password" placeholder="Admin password (if applicable)" class="form-input">
                                <button type="submit" name="role" value="Admin" class="btn btn-outline btn-block">
                                    Join as Admin
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>
