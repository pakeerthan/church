<?php
session_start();
require 'db.php';

// Check if the user is logged in and has the correct role (Admin or Super Admin)
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'super_admin' && $_SESSION['role'] != 'admin')) {
    echo "Access denied.";
    exit();
}

// Fetch all events from the database to populate the calendar
$query = "SELECT id, title, start, end, user_id FROM schedules"; // Assuming you have a 'schedules' table
$result = $conn->query($query);

$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = [
        'id' => $row['id'],
        'title' => $row['title'],
        'start' => $row['start'],
        'end' => $row['end'],
        'user_id' => $row['user_id'],
    ];
}

// Fetch users for the modal dropdown
$userQuery = "SELECT id, username FROM users";  // Assuming there's a 'users' table
$userResult = $conn->query($userQuery);
$users = [];
while ($user = $userResult->fetch_assoc()) {
    $users[] = $user;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Schedule - Admin</title>

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

        /* Modal Styles */
        .modal-overlay {
            display: none;
            /* Hidden by default */
            position: fixed;
            /* Stay in place */
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0);
            /* Black with opacity */
        }

        /* Modal Content (Right side positioning) */
        #addScheduleModal {
            background-color: #fff;
            padding: 20px;
            padding-top: 15%;
            border: 1px solid #888;
            width: 300px;
            height: 100%;
            /* Set width of the modal */
            position: fixed;
            top: 50%;
            right: 0;
            /* Ensure the modal is on the right side */
            left: auto;
            /* Prevent overriding the right positioning */
            transform: translateY(-50%);
            /* Center it vertically */
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.2);
            /* Add shadow for effect */
            border-radius: 8px;
        }

        /* Close Button */
        .close-btn {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            position: absolute;
            top: 17%;
            right: 10px;
        }

        .close-btn:hover,
        .close-btn:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        #addScheduleModal label,
        #addScheduleModal input,
        #addScheduleModal select {
            display: block;
            width: 100%;
            margin-bottom: 10px;
            font-size: 14px;
        }

        #addScheduleModal button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        #addScheduleModal button:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>

    <h1>Manage Schedules (Admin View)</h1>

    <!-- FullCalendar -->
    <div id="calendar"></div>

    <!-- Modal to Add Schedule -->
    <div id="modal-overlay" class="modal-overlay">
        <div id="addScheduleModal">
            <span class="close-btn" onclick="closeModal()">&times;</span> <!-- Close Button -->
            <h2>Add Schedule</h2>
            <form action="create_schedule.php" method="POST">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required>

                <label for="description">Description:</label>
                <input type="text" id="description" name="description">

                <label for="start">Start Time:</label>
                <input type="datetime-local" id="start" name="start" required>

                <label for="end">End Time:</label>
                <input type="datetime-local" id="end" name="end" required>

                <label for="user_id">Assign User</label>
                <select id="user_id" name="user_id" required>
                    <?php foreach ($users as $user) : ?>
                        <option value="<?php echo $user['id']; ?>"><?php echo $user['username']; ?></option>
                    <?php endforeach; ?>
                </select>

                <button type="submit">Add Schedule</button>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Function to open the modal
            function openModal(date) {
                $('#modal-overlay').show();
                $('#start').val(date.format());
                $('#end').val(date.add(1, 'hour').format());
            }

            // Initialize FullCalendar for Admin View (Editable, Can Add, Edit, Delete Events)
            $('#calendar').fullCalendar({
                events: <?php echo json_encode($events); ?>, // Display all events from the database
                editable: true, // Allow events to be edited by Admin
                droppable: true, // Allow events to be dragged and dropped
                dayRender: function(date, cell) {
                    cell.css('cursor', 'pointer'); // Change the cursor style for all date cells
                },
                dayClick: function(date, jsEvent, view) {
                    openModal(date); // Open the modal when a day is clicked
                },
                eventClick: function(calEvent, jsEvent, view) {
                    $('#eventTitle').text(calEvent.title);
                    $('#eventStart').text(calEvent.start.format('YYYY-MM-DD HH:mm'));
                    $('#eventEnd').text(calEvent.end.format('YYYY-MM-DD HH:mm'));
                    $('#assignedUser').text(calEvent.user_id);
                    $('#eventDetails').show();

                    $('#editEventBtn').click(function() {
                        editEvent(calEvent);
                    });

                    $('#deleteEventBtn').click(function() {
                        deleteEvent(calEvent.id);
                    });
                }
            });
        });

        function editEvent(calEvent) {
            $('#title').val(calEvent.title);
            $('#start').val(calEvent.start.format());
            $('#end').val(calEvent.end.format());
            $('#user_id').val(calEvent.user_id);
            $('#modal-overlay').show();
        }

        function deleteEvent(eventId) {
            if (confirm("Are you sure you want to delete this event?")) {
                $.ajax({
                    url: 'delete_schedule.php',
                    type: 'POST',
                    data: {
                        id: eventId
                    },
                    success: function(response) {
                        if (response == 'success') {
                            alert('Schedule deleted successfully!');
                            window.location.reload();
                        } else {
                            alert('Error deleting schedule.');
                        }
                    }
                });
            }
        }

        // Function to close the modal
        function closeModal() {
            $('#modal-overlay').hide();
        }

        // Close the modal if the user clicks on the overlay (background area)
        $('#modal-overlay').click(function(event) {
            // Close modal only if the click happens on the background, not inside the modal content
            if ($(event.target).is('#modal-overlay')) {
                closeModal();
            }
        });
    </script>

</body>

</html>