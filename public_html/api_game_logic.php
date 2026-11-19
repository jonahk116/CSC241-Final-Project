<?php
// api_game_logic.php (Place in the root directory)
session_start();
require 'includes/db.php'; // Correct path from root

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request.']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated.']);
    exit;
}

$action = $_POST['action'];
$user_id = $_SESSION['user_id'];
$response = [];

try {
    switch ($action) {
        case 'get_question':
            $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
            $stmt = $pdo->prepare("SELECT question_text, point_value FROM questions WHERE question_id = ?");
            $stmt->execute([$id]);
            $question = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($question) {
                $response = $question;
            } else {
                $response = ['error' => 'Question not found.'];
            }
            break;

        case 'check_answer':
            $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
            $user_answer = trim(strtolower($_POST['answer']));

            // CRITICAL FIX: The answer column is now confirmed to be `answer_text`
            $stmt = $pdo->prepare("SELECT `answer_text`, `point_value` FROM questions WHERE question_id = ?"); 
            $stmt->execute([$id]);
            $q_data = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$q_data) {
                $response = ['error' => 'Question data not found.'];
                break;
            }
            
            // 1. Compare against the `answer_text` column
            $correct_answer = trim(strtolower($q_data['answer_text']));
            $is_correct = ($user_answer === $correct_answer);

            $response = [
                'correct' => $is_correct,
                'value' => $q_data['point_value'], 
                // 2. Send back the official answer from the `answer_text` column
                'correct_answer' => $q_data['answer_text'] 
            ];
    break;

        case 'save_game':
            $score = filter_var($_POST['score'], FILTER_SANITIZE_NUMBER_INT);
            // This is complex, as you need to UPDATE existing stats, not just insert
            
            $stmt = $pdo->prepare("SELECT total_score, total_games, highest_score FROM user_stats WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($stats) {
                // Update existing stats
                $new_total_score = $stats['total_score'] + $score;
                $new_games = $stats['total_games'] + 1;
                $new_highest = max($stats['highest_score'], $score);
                
                $stmt = $pdo->prepare("
                    UPDATE user_stats SET 
                        total_score = ?, 
                        total_games = ?, 
                        highest_score = ? 
                    WHERE user_id = ?
                ");
                $stmt->execute([$new_total_score, $new_games, $new_highest, $user_id]);
            } else {
                // Insert new record if stats don't exist
                $stmt = $pdo->prepare("
                    INSERT INTO user_stats (user_id, total_score, total_games, highest_score) 
                    VALUES (?, ?, 1, ?)
                ");
                $stmt->execute([$user_id, $score, $score]);
            }
            $response = ['success' => true];
            break;

        default:
            http_response_code(400);
            $response = ['error' => 'Unknown action.'];
            break;
    }
} catch (PDOException $e) {
    http_response_code(500);
    $response = ['error' => 'Database error: ' . $e->getMessage()];
}

echo json_encode($response);
exit;