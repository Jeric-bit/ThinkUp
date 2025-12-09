<?php
// auth_social.php
session_start();
require 'db_connect.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

// 1. Validate Input
// We only require provider and oauth_uid. Email might be empty from Facebook.
if (!isset($input['provider']) || !isset($input['oauth_uid'])) {
    echo json_encode(['success' => false, 'message' => 'Missing data from social provider.']);
    exit();
}

$provider = $input['provider'];
$oauth_uid = $input['oauth_uid'];
$full_name = $input['full_name'] ?? 'User';
$email = $input['email'] ?? ''; // Email might be empty

// If Facebook didn't give an email, generate a fake one so the database doesn't complain
if (empty($email)) {
    $email = $oauth_uid . '@' . $provider . '.com';
}

try {
    // 2. STRATEGY A: Check if user exists by OAUTH_UID (Best for Facebook)
    $stmt = $pdo->prepare("SELECT * FROM users WHERE oauth_uid = ? AND auth_provider = ?");
    $stmt->execute([$oauth_uid, $provider]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // --- SCENARIO 1: User Found via Social ID -> Login ---
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        echo json_encode(['success' => true]);
        exit();
    }

    // 3. STRATEGY B: Check if user exists by EMAIL (Account Linking)
    // Only do this if we actually have a real email
    if (!empty($input['email'])) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // --- SCENARIO 2: Email matches existing account -> Link them ---
            $update = $pdo->prepare("UPDATE users SET oauth_uid = ?, auth_provider = ? WHERE id = ?");
            $update->execute([$oauth_uid, $provider, $user['id']]);
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            echo json_encode(['success' => true]);
            exit();
        }
    }

    // --- SCENARIO 3: New User -> Register ---
    $insert = $pdo->prepare("INSERT INTO users (full_name, email, oauth_uid, auth_provider, created_at) VALUES (?, ?, ?, ?, NOW())");
    
    if ($insert->execute([$full_name, $email, $oauth_uid, $provider])) {
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['user_name'] = $full_name;
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error during registration.']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'DB Error: ' . $e->getMessage()]);
}
?>