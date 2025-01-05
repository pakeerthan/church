<?php
session_start();
require 'db.php';
include 'nav.php'; // Include the navigation component

// Check if the user is logged in and has the correct role (Super Admin or Admin)
if ($_SESSION['role'] != 'super_admin' && $_SESSION['role'] != 'admin') {
    echo "Access Denied.";
    exit();
}

// Get the selected date from the POST request
if (isset($_POST['date'])) {
    $selectedDate = $_POST['date'];

    // Convert to SQL-compatible date format (YYYY-MM-DD)
    $startDate = date('Y-m-d 00:00:00', strtotime($selectedDate));
    $endDate = date('Y-m-d 23:59:59', strtotime($selectedDate));

    // Fetch events for the selected day
    $query = "SELECT id, title, start, end FROM schedules WHERE start BETWEEN ? AND ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch events
    $events = [];
    while ($row = $result->fetch_assoc()) {
        $events[] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'start' => $row['start'],
            'end' => $row['end'],
        ];
    }

    // Return events as JSON
    echo json_encode($events);
}
