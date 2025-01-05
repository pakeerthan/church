<?php
session_start();
?>

<nav>
    <ul class="nav-list">
        <!-- Link to Home / Dashboard (Can be for any main page) -->
        <li><a href="dashboard.php">Home</a></li>

        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($_SESSION['role'] == 'super_admin'): ?>
                <!-- Super Admin Navigation -->
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="view_schedules.php">View Schedules</a></li>
                <li><a href="balance.php">Balance</a></li>
            <?php elseif ($_SESSION['role'] == 'admin'): ?>
                <!-- Admin Navigation -->
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="view_schedules.php">View Schedules</a></li>
                <li><a href="balance.php">Balance</a></li>
            <?php elseif ($_SESSION['role'] == 'staff'): ?>
                <!-- Staff Navigation -->
                <li><a href="staff_view_schedules.php">View My Schedule</a></li>
            <?php endif; ?>

            <!-- Common Navigation for all logged-in users -->
            <li><a href="logout.php">Logout</a></li>
        <?php else: ?>
            <!-- Navigation for non-logged-in users -->
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
        <?php endif; ?>
    </ul>
</nav>

<!-- Add basic styles for navigation -->
<style>
    nav {
        background-color: #333;
        padding: 10px;
    }

    .nav-list {
        list-style: none;
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        /* Allow items to wrap on smaller screens */
        justify-content: center;
        /* Center the navigation items */
    }

    .nav-list li {
        margin: 5px;
        /* Add some spacing between items */
    }

    nav a {
        color: white;
        text-decoration: none;
        font-size: 16px;
        padding: 5px 10px;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    nav a:hover {
        text-decoration: underline;
        background-color: #555;
        /* Add a background on hover */
    }

    /* Responsive design for mobile */
    @media (max-width: 768px) {
        .nav-list {
            flex-direction: column;
            align-items: center;
            /* Center align the navigation items */
        }

        .nav-list li {
            margin: 10px 0;
            /* Increase spacing for mobile */
        }
    }
</style>