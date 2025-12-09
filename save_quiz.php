<?php
// save_quiz.php
session_start();
require 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No data received']);
    exit();
}

$quiz_id = $data['quiz_id'];
$title = trim($data['title']);
$questions = $data['questions'];
$status = $data['status'];
$user_id = $_SESSION['user_id'];

try {
    $pdo->beginTransaction();

    // 1. UPDATE Quiz Details
    $stmt = $pdo->prepare("UPDATE quizzes SET title = ?, status = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$title, $status, $quiz_id, $user_id]);

    if ($stmt->rowCount() === 0) {
        // Double check ownership if no rows updated (could be just no title change)
        $check = $pdo->prepare("SELECT id FROM quizzes WHERE id = ? AND user_id = ?");
        $check->execute([$quiz_id, $user_id]);
        if (!$check->fetch()) {
            throw new Exception("Quiz not found or access denied.");
        }
    }

    // 2. CLEAR Old Questions/Options to prevent duplicates
    $qIdsStmt = $pdo->prepare("SELECT id FROM questions WHERE quiz_id = ?");
    $qIdsStmt->execute([$quiz_id]);
    $qIds = $qIdsStmt->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($qIds)) {
        $placeholders = implode(',', array_fill(0, count($qIds), '?'));
        $delOpt = $pdo->prepare("DELETE FROM question_options WHERE question_id IN ($placeholders)");
        $delOpt->execute($qIds);
        
        // Also delete old student responses for these questions to avoid orphaned data
        // (Optional: remove this line if you want to keep old history despite edits)
        $delResp = $pdo->prepare("DELETE FROM quiz_responses WHERE question_id IN ($placeholders)");
        $delResp->execute($qIds);
    }

    $delQ = $pdo->prepare("DELETE FROM questions WHERE quiz_id = ?");
    $delQ->execute([$quiz_id]);

    // 3. INSERT New Data
    $qInsert = $pdo->prepare("INSERT INTO questions (quiz_id, question_text, type, is_required, is_randomized, show_answers, instructions, order_index) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $optInsert = $pdo->prepare("INSERT INTO question_options (question_id, option_text, is_correct) VALUES (?, ?, ?)");

    foreach ($questions as $index => $q) {
        $qText = trim($q['text'] ?? '');
        $type = $q['type'] ?? 'multiple_choice';
        $required = $q['required'] ? 1 : 0;
        $random = $q['randomize'] ? 1 : 0;
        $show = $q['showAnswers'] ? 1 : 0;
        $instr = trim($q['instructions'] ?? '');
        
        // Get the chosen "correct" answer string
        $correctAnswer = isset($q['correct']) ? trim($q['correct']) : null;

        $qInsert->execute([$quiz_id, $qText, $type, $required, $random, $show, $instr, $index]);
        $newQId = $pdo->lastInsertId();

        // === FIX: Force options for True/False ===
        if ($type === 'true_false') {
            $q['options'] = ['True', 'False'];
        }

        if (isset($q['options']) && is_array($q['options'])) {
            foreach ($q['options'] as $optText) {
                // Skip nulls but allow "0"
                if ($optText === null || trim($optText) === '') continue;
                
                $cleanOpt = trim($optText);
                $isCorrect = 0;

                // === LOGIC START ===
                if ($type === 'identification') {
                    $isCorrect = 1; 
                } else {
                    // For Multiple Choice / True False
                    // Compare trimmed strings. Case-insensitive comparison can be safer:
                    if ($correctAnswer !== null && strcasecmp($cleanOpt, $correctAnswer) === 0) {
                        $isCorrect = 1;
                    }
                }
                // === LOGIC END ===

                $optInsert->execute([$newQId, $cleanOpt, $isCorrect]);
            }
        }
    }

    $pdo->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>