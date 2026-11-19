<?php
require '../includes/db.php';
require '../includes/functions.php';
require '../includes/header_pages.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier']); // Can be username or email

    // Check if the user exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$identifier, $identifier]);
    $user = $stmt->fetch();

    if ($user) {
        // --- STEP 1: Generate a unique token ---
        $token = bin2hex(random_bytes(32)); 
        $user_id = $user['id'];
        $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token expires in 1 hour

        // --- STEP 2: Save the token to the password_reset table ---
        $stmt = $pdo->prepare("INSERT INTO password_reset (user_id, token, expires_at) VALUES (?, ?, ?)
                                ON DUPLICATE KEY UPDATE token = VALUES(token), expires_at = VALUES(expires_at)");
        $stmt->execute([$user_id, $token, $expires_at]);

        // --- STEP 3: Simulated Email/Notification (Required for security) ---
        // In a real application, you would send an email here with the link:
        // $resetLink = "http://yourdomain.com/pages/reset_password.php?token=" . $token;
        
        $message = "A password reset link has been processed. If a valid account exists, check your email for a link (Valid for 1 hour). 
                    <br><br> (For development, your token is: <strong>{$token}</strong>)"; 

    } else {
        // Use a generic message to prevent account fishing
        $message = "If a valid account exists, a password reset link has been processed. Check your email.";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <h2 class="mb-4">🔑 Forgot Password/Username</h2>
        <p>Enter your username or email address below and we'll send you a link to reset your password.</p>
        
        <?php if ($message): ?>
            <div class="alert alert-info"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST" action="forgot.php">
            <div class="mb-3">
                <label for="identifier" class="form-label">Username or Email</label>
                <input type="text" class="form-control" id="identifier" name="identifier" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Reset Password</button>
            <a href="login.php" class="btn btn-link">Back to Login</a>
        </form>
    </div>
</div>

<?php require '../includes/footer.php'; ?>