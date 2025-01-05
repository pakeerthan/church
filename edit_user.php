<?php
session_start();
require 'db.php';
include 'nav.php'; // Include the navigation component

// Check if the user is logged in and has the correct role
if (!isset($_SESSION['user_id']) && ($_SESSION['role'] != 'super_admin' || $_SESSION['role'] != 'admin')) {
    echo "Access Denied.";
    exit();
}

$user_id = isset($_GET['id']) ? $_GET['id'] : null;
$user = [];

// Fetch user to edit
if ($user_id) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
}

// Handle form submission for updating the user
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $role = $_POST['role'];

    // Update user details in the database
    $stmt = $conn->prepare("UPDATE users SET username = ?, role_id = (SELECT id FROM roles WHERE role_name = ?) WHERE id = ?");
    $stmt->bind_param("ssi", $username, $role, $user_id);
    $stmt->execute();

    header("Location: manage_users.php");  // Redirect back to manage users page
    exit();
}

// Fetch available roles
$roles_query = "SELECT role_name FROM roles";
$roles_result = $conn->query($roles_query);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit User</title>
</head>

<body>
    <h1>Edit User</h1>
    <form method="POST">
        <label>Username:</label>
        <input type="text" name="username" value="<?php echo $user['username'] ?? ''; ?>" required>

        <label>Role:</label>
        <select name="role">
            <?php while ($role = $roles_result->fetch_assoc()): ?>
                <option value="<?php echo $role['role_name']; ?>" <?php echo ($user['role_name'] == $role['role_name']) ? 'selected' : ''; ?>>
                    <?php echo ucfirst($role['role_name']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button type="submit">Update User</button>
    </form>
</body>

</html>