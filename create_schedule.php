<?php
session_start();
require 'db.php';

// Ensure the user is logged in and has a valid session
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'super_admin' && $_SESSION['role'] != 'admin')) {
    echo "Access Denied.";
    exit();
}

// Check if form data is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $start = $_POST['start'];
    $end = $_POST['end'];
    $user_id = $_POST['user_id'];

    // Insert the schedule into the 'schedules' table
    $query = "INSERT INTO schedules (title, description, start, end, user_id) 
              VALUES (?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("ssssi", $title, $description, $start, $end, $user_id);
        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "Error: Could not insert schedule.";
        }
        $stmt->close();
    } else {
        echo "Error: Could not prepare the statement.";
    }
} else {
    echo "Invalid request method.";
}
