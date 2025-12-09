<?php
// create_quiz.php
session_start();
require 'db_connect.php';

header('Content-Type: application/json');

// 1. Security Check
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// 2. Get Input from Folders page
$data = json_decode(file_get_contents('php://input'), true);
$title = trim($data['title'] ?? 'Untitled Quiz');
$course = trim($data['course'] ?? 'General');
$user_id = $_SESSION['user_id'];

// 3. Generate Unique Code
function generateCode($pdo) {
    do {
        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $stmt = $pdo->prepare("SELECT id FROM quizzes WHERE quiz_code = ?");
        $stmt->execute([$code]);
    } while ($stmt->fetch());
    return $code;
}

try {
    $quiz_code = generateCode($pdo);

    // 4. INSERT the new quiz (Status = draft)
    $stmt = $pdo->prepare("INSERT INTO quizzes (user_id, title, course_name, quiz_code, status, created_at) VALUES (?, ?, ?, ?, 'draft', NOW())");
    $stmt->execute([$user_id, $title, $course, $quiz_code]);
    
    $quiz_id = $pdo->lastInsertId();

    // 5. Create 1 Default Empty Question (So the editor isn't blank)
    $qStmt = $pdo->prepare("INSERT INTO questions (quiz_id, question_text, type, order_index) VALUES (?, '', 'multiple_choice', 0)");
    $qStmt->execute([$quiz_id]);
    
    $qId = $pdo->lastInsertId();
    $optStmt = $pdo->prepare("INSERT INTO question_options (question_id, option_text, is_correct) VALUES (?, ?, ?)");
    $optStmt->execute([$qId, '', 0]); 
    $optStmt->execute([$qId, '', 0]);

    echo json_encode(['success' => true, 'quiz_id' => $quiz_id]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
}
?>