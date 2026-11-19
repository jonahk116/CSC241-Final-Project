<?php
// 1. FORCE ERROR REPORTING (To see why it breaks)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Require files
require '../includes/db.php';
// require '../includes/functions.php'; // Only if you have this
require '../includes/header_pages.php'; 

// 3. Check Session
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    echo '<script>window.location.href = "login.php";</script>';
    exit;
}

$user_id = $_SESSION['user_id'];
$user = null;
$stats = null;

try {
    // 4. Fetch User Data (Using CORRECT columns: firstname, lastname)
    // We do NOT use 'username' here because your table doesn't have it.
    $stmt = $pdo->prepare("SELECT firstname, lastname, email, date_created FROM users WHERE id = ?"); 
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("Error: User found in session but not in database. ID: " . htmlspecialchars($user_id));
    }

    // 5. Fetch Stats (Wrapped in Try/Catch in case table is missing)
    try {
        $stmt = $pdo->prepare("
            SELECT total_score, games_played, highest_score, correct_answers, incorrect_answers 
            FROM user_stats 
            WHERE user_id = ?
        ");
        $stmt->execute([$user_id]);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // If stats table fails, just ignore it for now
        $stats = false; 
    }

    // Default stats if empty
    if (!$stats) {
        $stats = [
            'total_score' => 0, 'games_played' => 0, 'highest_score' => 0,
            'correct_answers' => 0, 'incorrect_answers' => 0
        ];
    }

} catch (PDOException $e) {
    // This will print the exact database error if one happens
    die("<h3>Database Error:</h3> " . $e->getMessage());
}
?>

<div class="row">
    <div class="col-lg-8 offset-lg-2">
        <h2 class="mb-4 text-primary">
            👤 User Profile: <?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?>
        </h2>
        
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                Account Details
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> <?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><strong>Member Since:</strong> <?php echo date("F j, Y", strtotime($user['date_created'])); ?></p>
            </div>
        </div>

        <h3 class="mb-3 mt-5 text-secondary">📊 Game Statistics</h3>
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card text-center bg-light">
                    <div class="card-body">
                        <h5 class="card-title">Total Score</h5>
                        <p class="display-4 text-success"><?php echo number_format($stats['total_score']); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card text-center bg-light">
                    <div class="card-body">
                        <h5 class="card-title">Games Played</h5>
                        <p class="display-4 text-info"><?php echo number_format($stats['games_played']); ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-5 text-center">
             <a href="../game.php" class="btn btn-warning btn-lg shadow">Start a New Game</a>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>