<?php
session_start();
require 'db.php'; // Make sure the database connection is working

// Check if the user is logged in and has the correct role
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'super_admin' && $_SESSION['role'] != 'admin')) {
    echo json_encode(['status' => 'error', 'message' => 'Access Denied']);
    exit();
}

// Validate POST data
$title = isset($_POST['title']) ? $_POST['title'] : '';
$description = isset($_POST['description']) ? $_POST['description'] : '';
$start = isset($_POST['start']) ? $_POST['start'] : '';
$end = isset($_POST['end']) ? $_POST['end'] : '';
$user_id = isset($_POST['user_id']) ? $_POST['user_id'] : '';

// Validate the data (e.g., ensure fields are not empty, valid date formats, etc.)
if (empty($title) || empty($start) || empty($end) || empty($user_id)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit();
}

// Prepare SQL query to insert the new schedule into the database
$query = "INSERT INTO schedules (title, description, start, end, user_id) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssssi", $title, $description, $start, $end, $user_id);

if ($stmt->execute()) {
    // Successfully created the schedule
    echo json_encode(['status' => 'success', 'message' => 'Schedule created successfully']);
} else {
    // Error occurred while creating the schedule
    echo json_encode(['status' => 'error', 'message' => 'Error occurred while creating the schedule.']);
}

$stmt->close();
$conn->close();
