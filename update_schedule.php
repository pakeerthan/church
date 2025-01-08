<?php
session_start();
require 'db.php';

header('Content-Type: application/json'); // Set the response type to JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['event_id'])) {
        $eventIdFromSession = $_SESSION['event_id'];
        // Check if it's a request for a drag-and-drop update (event date change)
        if (isset($_POST['id']) && isset($_POST['start']) && isset($_POST['end']) && !isset($_POST['title'])) {
            // Drag-and-drop update
            $eventId = $_POST['id'];
            $newStart = $_POST['start'];
            $newEnd = $_POST['end'];

            // Prepare and execute the SQL query to update the event's date
            $stmt = $conn->prepare("UPDATE schedules SET start = ?, end = ? WHERE id = ?");
            $stmt->bind_param('ssi', $newStart, $newEnd, $eventId);

            if ($stmt->execute()) {
                // Successfully updated, now clear the session
                unset($_SESSION['event_id']); // Clear the event ID stored in the session

                echo json_encode(['status' => 'success', 'message' => 'Schedule update success.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Database update failed for drag-and-drop.']);
            }

            $stmt->close();
        }
        // Check if it's a request for editing the event (title, description, user, etc.)
        elseif (isset($_POST['id']) && isset($_POST['title']) && isset($_POST['start'])) {
            // Edit event update
            $eventId = $_POST['id'];
            $title = $_POST['title'];
            $description = isset($_POST['description']) ? $_POST['description'] : null; // Description is optional
            $start = $_POST['start'];
            $end = isset($_POST['end']) ? $_POST['end'] : null; // End time is optional
            $userId = $_POST['user_id'];

            // Prepare and execute the SQL query to update the event details (title, description, user, etc.)
            $stmt = $conn->prepare("UPDATE schedules SET title = ?, description = ?, start = ?, end = ?, user_id = ? WHERE id = ?");
            $stmt->bind_param('ssssii', $title, $description, $start, $end, $userId, $eventId);

            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Schedule update success.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Database update failed for event edit.']);
            }

            $stmt->close();
        } else {
            // Missing parameters
            echo json_encode(['status' => 'error', 'message' => 'Missing parameters in the request.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No event ID found in session.']);
    }

    // Close the database connection
    $conn->close();
}
