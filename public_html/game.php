<?php
// CRITICAL: Force error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
// NOTE: Assuming this file is in the ROOT directory based on 'includes/header_root.php'
require 'includes/db.php';
require 'includes/header_root.php'; 

// --- 1. Enforce Login and Redirect ---
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    // Use JavaScript redirect workaround
    echo '<script>window.location.href = "pages/login.php";</script>';
    exit;
}

$user_id = $_SESSION['user_id'];
$categories = []; // Array to hold all game data

try {
    // --- 2. Fetch 5 Random Categories ---
    // Assuming the categories table uses 'category_id' and 'category_name'
    $stmt = $pdo->prepare("
        SELECT category_id, category_name 
        FROM categories 
        ORDER BY RAND() 
        LIMIT 5
    ");
    $stmt->execute();
    $categories_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // --- 3. Fetch Questions for Each Category ---
    foreach ($categories_data as $category) {
        $category_id = $category['category_id'];
        
        // **CRITICAL FIX:** Reverting to the confirmed column name: `answer_varchar`
        $stmt_q = $pdo->prepare("
            SELECT question_id, question_text, `answer_text`, point_value 
            FROM questions 
            WHERE category_id = ? 
            ORDER BY point_value ASC 
            LIMIT 5
        ");
        $stmt_q->execute([$category_id]);
        $questions = $stmt_q->fetchAll(PDO::FETCH_ASSOC);

        // Store the category name and its questions
        $categories[] = [
            'name' => $category['category_name'], 
            'questions' => $questions
        ];
    }

} catch (PDOException $e) {
    // Display fatal database error clearly
    die("<h1>Database Error:</h1><p>Failed to load game data. Check your categories and questions tables.</p><p>Error: " . $e->getMessage() . "</p>");
}
?>

<div class="container">
    <div class="text-center mb-4">
        <h1 class="display-3 text-info">Jeopardy!</h1>
        <h2 class="text-secondary" id="score">Score: $0</h2>
    </div>

    <?php if (empty($categories)): ?>
        <div class="alert alert-warning text-center">
            The game board cannot be built. Please ensure your 'categories' and 'questions' tables contain data.
        </div>
    <?php else: ?>
        <div class="row row-cols-5 g-2 jeopardy-board">
            
            <?php foreach ($categories as $cat): ?>
                <div class="col">
                    <div class="card bg-primary text-white text-center mb-2 p-2 category-header">
                        <h5 class="card-title mb-0"><?php echo htmlspecialchars($cat['name']); ?></h5>
                    </div>

                    <?php 
                    $rendered_values = []; 
                    foreach ($cat['questions'] as $question): 
                        if (in_array($question['point_value'], $rendered_values)) continue; 
                        $rendered_values[] = $question['point_value'];
                    ?>
                        <div class="card text-center text-white bg-info mb-2 jeopardy-tile question-block" 
                             data-question-id="<?php echo $question['question_id']; ?>"
                             data-value="<?php echo $question['point_value']; ?>"
                             style="cursor: pointer;">
                            <div class="card-body d-flex align-items-center justify-content-center p-1">
                                <h4 class="card-text mb-0">$<?php echo number_format($question['point_value']); ?></h4>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>

        </div> <?php endif; ?>

</div> <div class="modal fade" id="questionModal" tabindex="-1" aria-labelledby="questionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="questionModalLabel">
                    Question for: <span id="questionValue">$0</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="qText" class="lead text-center"></p>
                <div id="feedback" class="mt-3 text-center"></div>
                <div class="form-group mt-3">
                    <label for="userAnswer">Your Answer:</label>
                    <input type="text" id="userAnswer" class="form-control" placeholder="What is...">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="submitAnswer()">Submit Answer</button>
            </div>
        </div>
    </div>
</div>

<div class="text-center mt-5">
    <button id="endGameBtn" class="btn btn-danger btn-lg">End Game & Save Score</button>
</div>

<?php 
// Include JavaScript logic (Path corrected for root access: /js/game_logic.js)
echo '<script src="/js/game.js"></script>';

require 'includes/footer.php'; 
?>