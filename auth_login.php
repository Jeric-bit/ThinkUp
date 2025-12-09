<?php
// auth_login.php
session_start();
header('Content-Type: application/json');

// 1. Include the database connection
// Make sure db_connect.php exists in the same folder!
if (!file_exists('db_connect.php')) {
    echo json_encode(['success' => false, 'message' => 'Error: db_connect.php not found']);
    exit;
}
require 'db_connect.php';

// 2. Get the data sent from the form
$input = json_decode(file_get_contents('php://input'), true);
$email = $input['username'] ?? ''; 
$password = $input['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all fields']);
    exit;
}

try {
    // 3. Look for the user in the database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // 4. Verify the password
        // Note: This only works if the password was hashed during sign-up
        if ($user['password_hash'] && password_verify($password, $user['password_hash'])) {
            
            // Login Success: Save to Session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];

            echo json_encode(['success' => true]);
            exit;
        } elseif (!$user['password_hash']) {
            // User signed up with Google/Facebook, so they have no password
            echo json_encode(['success' => false, 'message' => 'Please log in using Google or Facebook']);
            exit;
        }
    }

    echo json_encode(['success' => false, 'message' => 'Incorrect email or password']);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>