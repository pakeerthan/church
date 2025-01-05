<?php
session_start();
require 'db.php';

// Check if the user is logged in and has the correct role (Super Admin or Admin)
if ($_SESSION['role'] != 'super_admin' && $_SESSION['role'] != 'admin') {
    echo "Access Denied.";
    exit();
}

// Ensure title, start, and end are set
if (isset($_POST['title'], $_POST['start'], $_POST['end'])) {
    $title = $_POST['title'];
    $start = $_POST['start'];
    $end = $_POST['end'];

    // Insert the new schedule into the database
    $stmt = $conn->prepare("INSERT INTO schedules (title, start, end) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $start, $end);

    if ($stmt->execute()) {
        echo "Event saved successfully!";
    } else {
        echo "Error saving event.";
    }
} else {
    echo "Invalid request.";
}
