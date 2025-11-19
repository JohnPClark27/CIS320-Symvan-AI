<?php
// ===========================================
// USER LOGIN PAGE (SECURE HASHED PASSWORD)
// ===========================================

session_start();

require_once 'audit.php';

// ===========================================
// DATABASE CONNECTION (XAMPP)
// ===========================================
require_once 'db_connect.php';


// ===========================================
// HANDLE LOGIN SUBMISSION
// ===========================================
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        // Look up user by email
        $stmt = $conn->prepare("
            SELECT id, email, password_hash, username
            FROM `user`
            WHERE LOWER(TRIM(email)) = LOWER(TRIM(?))
            LIMIT 1
        ");

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $userData = $result->fetch_assoc();

            // ✅ Verify hashed password
            if (password_verify($password, $userData['password_hash'])) {

                // Successful login — set session variables
                $_SESSION['user_id'] = $userData['id'];
                $_SESSION['email'] = $userData['email'];
                $_SESSION['username'] = $userData['username'];

                // Log audit
                log_audit($conn, $userData['id'], "User logged in", $userData['id']);

                header("Location: index.php");
                exit();

            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "Email not found.";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Symvan</title>
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
            <h2 class="text-center mb-md">Welcome Back</h2>

            <?php if (!empty($error)): ?>
                <p class="error-message" style="color: red;">
                    <?php echo htmlspecialchars($error); ?>
                </p>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required 
                        class="form-input" 
                        placeholder="your.email@university.edu">
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required 
                        class="form-input" 
                        placeholder="Enter your password">
                </div>

                <button type="submit" class="btn btn-primary btn-block">Sign In</button>
            </form>

            <div class="auth-links">
                <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
            </div>

        </div>
    </div>
</div>

</body>

<?php if (isset($_GET['deleted'])): ?>
    <div class="alert alert-success" style="text-align:center;">
        Your account has been deleted successfully.
    </div>
<?php endif; ?>

</html>
