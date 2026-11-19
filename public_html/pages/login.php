<?php
session_start();
// CRITICAL: Require statements use '../' to access files in the root/includes folder
require '../includes/db.php';
require '../includes/functions.php'; // Include only if you use this file
require '../includes/header_pages.php'; // Assuming your header file is simply named header.php

$error = '';
$email = ''; // Initialize to prevent PHP notices if form not submitted

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Sanitize and collect input
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];

    // 2. Prepare to fetch user data and the hashed password (sPassword)
    $stmt = $pdo->prepare("
        SELECT id, firstname, lastname, sPassword 
        FROM users 
        WHERE email = ?
    ");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // 3. Verify user exists and check the password hash
    // We check the input password against the sPassword column
    if ($user && password_verify($password, $user['sPassword'])) {
        // Successful login: Regenerate session and set required variables
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['firstname'] = $user['firstname']; 
        $_SESSION['lastname'] = $user['lastname'];   
        
        echo '<script>window.location.href = "profile.php";</script>';
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-success text-white">Login</div>
            <div class="card-body">
                <?php 
                // Display success message from registration if redirected here
                if(isset($_GET['registered']) && $_GET['registered'] == 1): ?>
                    <div class="alert alert-success">Registration successful! Please log in.</div>
                <?php endif; ?>

                <?php if($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="email">Email</label>
                        <input type="email" name="email" class="form-control" required aria-label="Email" id="email" value="<?= htmlspecialchars($email) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="password">Password</label>
                        <input type="password" name="password" class="form-control" required aria-label="Password" id="password">
                    </div>
                    <button type="submit" class="btn btn-success w-100">Login</button>
                    <div class="mt-2 text-center">
                        <a href="forgot.php" class="d-block text-center small mt-3">Forgot Password/Username?</a>
                        <p class="mt-3">Don't have an account? <a href="register.php">Register here</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>