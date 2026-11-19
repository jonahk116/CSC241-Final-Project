<?php
require '../includes/db.php';
require '../includes/functions.php';
require '../includes/header_pages.php';

$message = '';

try {
    // Select name details from 'users' (u) and stats from 'user_stats' (us).
    // Note the corrected column names: total_score, total_games, firstname, lastname.
    $stmt = $pdo->prepare("
        SELECT 
            u.firstname, 
            u.lastname, 
            us.total_score,
            us.total_games,
            us.highest_score
        FROM user_stats us
        INNER JOIN users u ON u.id = us.user_id 
        ORDER BY us.total_score DESC  /* Order by the highest total score */
        LIMIT 10 /* Show top 10 players */
    ");
    $stmt->execute();
    $leaderboard = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // This will display a full error message if any database issue remains
    die("<h1>Database Error:</h1><p>Could not fetch leaderboard data: " . $e->getMessage() . "</p>");
}
?>

<div class="row">
    <div class="col-lg-10 offset-lg-1">
        <h2 class="mb-4 text-primary">🏆 Top 10 Leaderboard</h2>
        <div class="card shadow-sm">
            <div class="card-body">
                <?php if (empty($leaderboard)): ?>
                    <div class="alert alert-info text-center">No scores found yet! Start playing to get on the board.</div>
                <?php else: ?>
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Player Name</th>
                                <th>Total Score</th>
                                <th>Highest Score</th>
                                <th>Games Played</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $rank = 1; foreach ($leaderboard as $player): ?>
                            <tr>
                                <td><?= $rank++ ?></td>
                                <td><?= htmlspecialchars($player['firstname'] . ' ' . $player['lastname']) ?></td>
                                <td><?= number_format($player['total_score']) ?></td>
                                <td><?= number_format($player['highest_score']) ?></td>
                                <td><?= $player['total_games'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php 
// CRITICAL: Footer also needs '../' to access the file in the root
require '../includes/footer.php'; 
?>