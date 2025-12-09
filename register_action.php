<?php
// register_action.php
session_start();
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($full_name) || empty($email) || empty($password)) {
        echo "<script>alert('Please fill all fields'); window.history.back();</script>";
        exit();
    }

    try {
        // Check if email taken
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        
        if ($check->rowCount() > 0) {
            echo "<script>alert('Email already exists. Please Login.'); window.location.href='log-in.php';</script>";
            exit();
        }

        // Hash password
        $pass_hash = password_hash($password, PASSWORD_DEFAULT);

        // Insert
        $sql = "INSERT INTO users (full_name, email, password_hash, auth_provider, created_at) VALUES (?, ?, ?, 'email', NOW())";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$full_name, $email, $pass_hash])) {
            echo "<script>alert('Account created! Please log in.'); window.location.href='log-in.php';</script>";
        } else {
            echo "<script>alert('Error creating account.'); window.history.back();</script>";
        }

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>