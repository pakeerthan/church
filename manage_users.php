<?php
session_start();
require 'db.php';
include 'nav.php'; // Include the navigation component

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'super_admin' && $_SESSION['role'] != 'admin')) {
    echo "Access denied.";
    exit();
}

// Fetch users based on current user’s role
$query = "SELECT users.id, users.username, roles.role_name FROM users JOIN roles ON users.role_id = roles.id";
if ($_SESSION['role'] == 'admin') {
    // Admins can only view other Admins and Staff; exclude Super Admins
    $query .= " WHERE roles.role_name IN ('admin', 'staff')";
}
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Manage Users</title>
</head>

<body>
    <h1>User Management</h1>
    <a href="create_user.php">Create New User</a>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
        <?php while ($user = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td><?php echo $user['username']; ?></td>
                <td><?php echo ucfirst($user['role_name']); ?></td>
                <td>
                    <a href="edit_user.php?id=<?php echo $user['id']; ?>">Edit</a>
                    <?php if ($_SESSION['role'] == 'super_admin' || $user['role_name'] != 'super_admin'): ?>
                        <!-- Only allow deletion of non-Super Admin users -->
                        <a href="delete_user.php?id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>

</html>