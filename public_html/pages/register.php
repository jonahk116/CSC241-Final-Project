<?php
session_start();
// CRITICAL: Require statements use '../' to access files in the root/includes folder
require '../includes/db.php';
require '../includes/functions.php'; // Include only if you use this file
require '../includes/header_pages.php'; // Assuming your header file is simply named header.php

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Sanitize and collect input, including the new firstname and lastname fields
    $firstname = sanitizeInput($_POST['firstname']);
    $lastname = sanitizeInput($_POST['lastname']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // 2. Input Validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (empty($firstname) || empty($lastname)) {
        $error = "First name and Last name are required.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // 3. Check if email already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            $error = "Email address is already registered.";
        } else {
            // 4. Securely Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            try {
                // 5. Insert new user using the EXACT column names: firstname, lastname, sPassword, date_created
                $stmt = $pdo->prepare("
                    INSERT INTO users (firstname, lastname, email, sPassword, date_created) 
                    VALUES (?, ?, ?, ?, NOW())
                ");
                
                $stmt->execute([
                    $firstname, 
                    $lastname, 
                    $email, 
                    $hashed_password
                ]);

                // 6. After successful registration, redirect to the login page
                header("Location: login.php?registered=1");
                exit();
                
            } catch (PDOException $e) {
                // Should only happen if the database connection or structure is wrong
                $error = "Registration failed due to a database error.";
                // You can temporarily use die("Error: " . $e->getMessage()); for debugging
            }
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">Register</div>
            <div class="card-body">
                <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="firstname">First Name</label>
                        <input type="text" name="firstname" class="form-control" required value="<?= htmlspecialchars($firstname ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="lastname">Last Name</label>
                        <input type="text" name="lastname" class="form-control" required value="<?= htmlspecialchars($lastname ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="email">Email</label>
                        <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($email ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="password">Password (min 6 characters)</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">Register</button>
                    <div class="mt-3 text-center">
                        <p>Already have an account? <a href="login.php">Login here</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>