<?php
session_start();
require 'db.php';

// Check if the user is logged in and has the correct role
if (!isset($_SESSION['user_id']) && ($_SESSION['role'] != 'super_admin' || $_SESSION['role'] != 'admin')) {
    echo "Access Denied.";
    exit();
}

$user_id = isset($_GET['id']) ? $_GET['id'] : null;

if ($user_id) {
    // Delete user from the database
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    // Redirect back to the manage users page after deletion
    header("Location: manage_users.php");
    exit();
} else {
    echo "Invalid user ID.";
}
