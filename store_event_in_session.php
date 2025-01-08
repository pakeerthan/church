<?php
// Start the session to store data
session_start();

// Ensure the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the event ID is provided in the POST request
    if (isset($_POST['event_id']) && is_numeric($_POST['event_id'])) {
        // Sanitize and store the event ID in the session
        $_SESSION['event_id'] = intval($_POST['event_id']); // Ensure it's an integer
    }
}
