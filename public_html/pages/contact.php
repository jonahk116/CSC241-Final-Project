<?php
require '../includes/db.php';
require '../includes/functions.php';
require '../includes/header_pages.php';

$msg = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $message = sanitizeInput($_POST['message']);

    $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
    if ($stmt->execute([$name, $email, $message])) {
        $msg = "<div class='alert alert-success'>Message sent successfully!</div>";
    } else {
        $msg = "<div class='alert alert-danger'>Error sending message.</div>";
    }
}
?>
<h2>Contact Us</h2>
<?= $msg ?>
<form method="POST">
    <div class="mb-3"><label>Name</label><input type="text" name="name" class="form-control" required></div>
    <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
    <div class="mb-3"><label>Message</label><textarea name="message" class="form-control" rows="5" required></textarea></div>
    <button type="submit" class="btn btn-primary">Send Message</button>
</form>
<?php require '../includes/footer.php'; ?>