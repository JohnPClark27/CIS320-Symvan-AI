<?php
// ===========================================
// USER SIGNUP PAGE (NO STUDENT ID)
// ===========================================
session_start();

require_once 'audit.php';

// ===========================================
// DATABASE CONNECTION
// ===========================================
require_once 'db_connect.php';

// ===========================================
// HANDLE SIGNUP SUBMISSION
// ===========================================
$successMessage = "";
$errorMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm-password'];

    if (empty($fullname) || empty($email) || empty($password)) {
        $errorMessage = "Please fill out all fields.";
    } elseif ($password !== $confirm) {
        $errorMessage = "Passwords do not match.";
    } else {
        // Check if email already exists
        $check = $conn->prepare("SELECT id FROM user WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $errorMessage = "An account with this email already exists.";
        } else {
            // Hash password securely
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("
                INSERT INTO user (level, username, email, password_hash)
                VALUES ('User', ?, ?, ?)
            ");
            $stmt->bind_param("sss", $fullname, $email, $hashed);

            if ($stmt->execute()) {
                $successMessage = "Account created successfully! You can now log in.";
                log_audit($conn, $stmt->insert_id, "User account created", $stmt->insert_id);
                header("Location: login.php");
                exit();
            } else {
                $errorMessage = "Something went wrong. Please try again.";
            }

            $stmt->close();
        }

        $check->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Symvan</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="auth-page">
    <div class="auth-container">

        <div class="auth-logo">
            <h1>Symvan</h1>
            <p class="text-grey">Campus Event Management</p>
        </div>

        <div class="form-container">
            <h2 class="text-center mb-md">Create Account</h2>

            <?php if (!empty($errorMessage)): ?>
                <p class="error-message" style="color: red;">
                    <?php echo htmlspecialchars($errorMessage); ?>
                </p>
            <?php elseif (!empty($successMessage)): ?>
                <p class="success-message" style="color: green;">
                    <?php echo htmlspecialchars($successMessage); ?>
                </p>
            <?php endif; ?>

            <form action="signup.php" method="POST">
                <div class="form-group">
                    <label for="fullname" class="form-label">Full Name</label>
                    <input type="text" id="fullname" name="fullname" class="form-input" placeholder="John Doe" required>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="form-input" placeholder="your.email@university.edu" required>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-input" placeholder="Create a strong password" required>
                </div>

                <div class="form-group">
                    <label for="confirm-password" class="form-label">Confirm Password</label>
                    <input type="password" id="confirm-password" name="confirm-password" class="form-input" placeholder="Re-enter your password" required>
                </div>

                

                <button type="submit" class="btn btn-primary btn-block">
                    Create Account
                </button>
            </form>

            <div class="auth-links">
                <p>Already have an account? <a href="login.php">Sign in here</a></p>
            </div>
        </div>
    </div>
</div>
</body>
</html>