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
$query = "SELECT id, title,description, start, end FROM schedules WHERE user_id = '$user_id'"; // Filter by user ID
$result = $conn->query($query);

$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = [
        'id' => $row['id'],
        'title' => $row['title'],
        'description' => $row['description'],
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

    <!-- Fontawsome css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- FullCalendar CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css">

    <!-- FullCalendar JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

    <!-- jQuery (required for FullCalendar and AJAX) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>

    <style>
        #calendar {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }

        .fc-event.custom-event .event-username {
            font-size: 12px;
            color: #fff;
            margin-top: 5px;
            /* Add spacing between title and username */
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
                eventRender: function(event, element) {
                    // Create custom elements for username and description
                    var titleElement = $('<div class="event-title" style="font-size: 16px; color: #fff;">' + (event.title || 'No title') + '</div>');
                    var descriptionElement = $('<div class="event-description" style="font-size: 12px; color: #fff;"><i class="fa-solid fa-envelope" style="color: #fff;margin-right:5px;"></i>' + (event.description || 'No description') + '</div>');
                    var timeElement = $('<div class="event-date" style="font-size: 12px; color: #fff;"><i class="fa fa-clock" style="color: #fff;margin-right:5px;"></i>' + (event.start.format('HH:mm') || 'No description') + ' - ' + (event.end.format('HH:mm') || 'No description') + '</div>');

                    // Remove the title element from the event
                    element.find('.fc-title').remove(); // Removes the title from the event
                    element.find('.fc-time').remove(); // Removes any time-related element

                    // Add the custom elements (in this case, only the username)
                    element.append(titleElement);
                    element.append(descriptionElement);
                    element.append(timeElement);

                    // Style the event's background and border
                    element.css({
                        'background-color': '#2196F3', // Blue background
                        'border-radius': '5px',
                        'border': '2px solid #1E88E5', // Darker blue border
                        'padding': '8px', // Padding for spacing
                        'position': 'relative', // For positioning icon
                    });
                },
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