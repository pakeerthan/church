<?php
session_start();
require 'db.php';
include 'nav.php'; // Include the navigation component

if (!isset($_SESSION['user_id']) && ($_SESSION['role'] != 'super_admin' || $_SESSION['role'] != 'admin')) {
    echo "Access denied.";
    exit();
}

// Check if there are any existing Admins in the database
$admin_check_query = "SELECT COUNT(*) AS admin_count FROM users JOIN roles ON users.role_id = roles.id WHERE roles.role_name = 'admin'";
$admin_check_result = $conn->query($admin_check_query);
$admin_count = $admin_check_result->fetch_assoc()['admin_count'];

// Determine allowed roles based on current user role and admin count
$allowed_roles_query = "";
if ($_SESSION['role'] == 'super_admin' && $admin_count == 0) {
    // Super Admin can create the first Admin
    $allowed_roles_query = "SELECT id, role_name FROM roles WHERE role_name IN ('admin', 'staff')";
} elseif ($_SESSION['role'] == 'admin') {
    // Admin can create other Admins and Staff
    $allowed_roles_query = "SELECT id, role_name FROM roles WHERE role_name IN ('admin', 'staff')";
} else {
    echo "Access denied.";
    exit();
}

// Fetch allowed roles
$allowed_roles_result = $conn->query($allowed_roles_query);

// Handle form submission for creating users
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role_id = $_POST['role_id'];

    // Insert the new user
    $insert_query = "INSERT INTO users (username, password, role_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("ssi", $username, $password, $role_id);
    $stmt->execute();

    echo "User created successfully!";
    header("Location: manage_users.php");
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Create User</title>
</head>

<body>
    <h1>Create New User</h1>
    <form method="POST">
        <label>Username:</label>
        <input type="text" name="username" required>
        <label>Password:</label>
        <input type="password" name="password" required>
        <label>Role:</label>
        <select name="role_id">
            <?php while ($role = $allowed_roles_result->fetch_assoc()): ?>
                <option value="<?php echo $role['id']; ?>"><?php echo ucfirst($role['role_name']); ?></option>
            <?php endwhile; ?>
        </select>
        <button type="submit">Create User</button>
    </form>
</body>

</html>