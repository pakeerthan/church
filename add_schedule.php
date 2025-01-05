<?php
session_start();
require 'db.php';

// Check if the user is logged in and has the correct role (Admin or Super Admin)
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'super_admin' && $_SESSION['role'] != 'admin')) {
    echo "Access Denied";
    exit();
}

// Retrieve POST data (schedule form data)
$title = isset($_POST['title']) ? $_POST['title'] : '';
$description = isset($_POST['description']) ? $_POST['description'] : ''; // Added description field
$start = isset($_POST['start']) ? $_POST['start'] : '';
$end = isset($_POST['end']) ? $_POST['end'] : '';
$user_id = isset($_POST['user_id']) ? $_POST['user_id'] : '';

// Validation (you can add more validation checks)
if (empty($title) || empty($start) || empty($end) || empty($user_id)) {
    echo "All fields are required.";
    exit();
}

// Prepare the query to insert the new schedule into the database
$stmt = $conn->prepare("INSERT INTO schedules (title, description, start, end, user_id) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssssi", $title, $description, $start, $end, $user_id);

// Execute the query
if ($stmt->execute()) {
    echo "success";  // Successfully added schedule
} else {
    echo "Error adding schedule.";  // If insertion fails
}

// Close the statement and database connection
$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Schedule</title>

    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@3.2.0/dist/fullcalendar.min.css" rel="stylesheet" />

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@3.2.0/dist/fullcalendar.min.js"></script>

    <!-- jQuery (required for FullCalendar and AJAX) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        /* Simple style for the calendar */
        #calendar {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }

        .event-details {
            display: none;
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 20px;
            position: absolute;
            top: 20%;
            left: 20%;
            width: 60%;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }

        .event-details h2 {
            margin-top: 0;
        }
    </style>
</head>

<body>

    <h1>Manage Schedules</h1>
    <div id="calendar"></div>

    <!-- Event Details Modal -->
    <div class="event-details" id="eventDetails">
        <h2>Event Details</h2>
        <p><strong>Title:</strong> <span id="eventTitle"></span></p>
        <p><strong>Start:</strong> <span id="eventStart"></span></p>
        <p><strong>End:</strong> <span id="eventEnd"></span></p>
        <button id="closeModal">Close</button>
    </div>

    <script>
        $(document).ready(function() {
            // Initialize FullCalendar
            $('#calendar').fullCalendar({
                events: <?php echo json_encode($events); ?>, // Display existing events from the database
                editable: true, // Enable drag-and-drop
                droppable: true, // Enable dropping events
                dayClick: function(date, jsEvent, view) {
                    // Fetch events for the clicked date
                    var selectedDate = date.format();
                    $.ajax({
                        url: 'get_events_for_day.php',
                        type: 'POST',
                        data: {
                            date: selectedDate
                        },
                        success: function(response) {
                            // Display events for that day
                            var events = JSON.parse(response);
                            var eventList = "<ul>";
                            events.forEach(function(event) {
                                eventList += "<li><a href='#' class='event' data-id='" + event.id + "'>" + event.title + " (" + event.start + " - " + event.end + ")</a></li>";
                            });
                            eventList += "</ul>";
                            $('#eventDetails').html(eventList); // Show events in modal
                            $('#eventDetails').show(); // Show the modal
                        }
                    });
                },

                // When clicking on an event, show the event details
                eventClick: function(calEvent, jsEvent, view) {
                    $('#eventTitle').text(calEvent.title);
                    $('#eventStart').text(calEvent.start.format('YYYY-MM-DD HH:mm'));
                    $('#eventEnd').text(calEvent.end.format('YYYY-MM-DD HH:mm'));
                    $('#eventDetails').show(); // Show the event details modal
                }
            });

            // Close the event details modal
            $('#closeModal').click(function() {
                $('#eventDetails').hide();
            });
        });
    </script>

</body>

</html>