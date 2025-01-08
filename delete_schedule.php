<?php
require 'db.php';
header('Content-Type: application/json');


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];

    $query = "DELETE FROM schedules WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Event deleted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete the event.']);
    }

    $stmt->close();
    $conn->close();
}
