<?php
session_start();
require 'db.php';
include 'nav.php'; // Include the navigation component

// Check if the user is logged in and has the correct role (Staff)
if ($_SESSION['role'] != 'staff') {
    echo "Access Denied.";
    exit();
}

// Get the current user ID
$user_id = $_SESSION['user_id'];

// Fetch events assigned to the current staff member from the database
$query = "SELECT id, title, start, end FROM schedules WHERE user_id = '$user_id'"; // Filter by user ID
$result = $conn->query($query);

$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = [
        'id' => $row['id'],
        'title' => $row['title'],
        'start' => $row['start'],
        'end' => $row['end'],
    ];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Schedule - Staff</title>

    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@3.2.0/dist/fullcalendar.min.css" rel="stylesheet" />

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@3.2.0/dist/fullcalendar.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        #calendar {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }
    </style>
</head>

<body>

    <h1>View Schedule (Staff View)</h1>

    <!-- FullCalendar -->
    <div id="calendar"></div>

    <script>
        $(document).ready(function() {
            // Initialize FullCalendar for Staff View (View Only, No Edit)
            $('#calendar').fullCalendar({
                events: <?php echo json_encode($events); ?>, // Display only the events assigned to the logged-in staff
                editable: false, // Disable editing
                droppable: false, // Disable dragging
                dayClick: function(date, jsEvent, view) {
                    // Display event details if necessary
                    alert('No action available for Staff!');
                },
                eventClick: function(calEvent, jsEvent, view) {
                    // Display event details in an alert box
                    alert("Event: " + calEvent.title + "\nStart: " + calEvent.start.format('YYYY-MM-DD HH:mm') + "\nEnd: " + calEvent.end.format('YYYY-MM-DD HH:mm'));
                }
            });
        });
    </script>

</body>

</html>