<?php
// process_signup.php
session_start();
header('Content-Type: application/json');
require 'db_connect.php'; 

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'No data received']);
    exit;
}

$email = $input['email'];
$password = $input['password'];
$firstName = $input['firstName'];
$lastName = $input['lastName'];
$fullName = $firstName . ' ' . $lastName;

try {
    // Check if email exists
    $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $check->execute([$email]);
    if ($check->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email already exists']);
        exit;
    }

    // Insert user
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (full_name, email, password_hash, auth_provider) VALUES (?, ?, ?, 'email')";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$fullName, $email, $passwordHash])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }

} catch (PDOException $e) {
    // Important: Send the error back so we can see it
    echo json_encode(['success' => false, 'message' => 'DB Error: ' . $e->getMessage()]);
}
?>