<?php
require 'db.php';

// Only allow registration if there are no users (initial setup)
$query = "SELECT COUNT(*) as count FROM users";
$result = $conn->query($query);
$count = $result->fetch_assoc()['count'];

if ($count > 0) {
    die("Registration is disabled. Super Admin can add other users.");
}

// Registration form processing
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Default role for first user is 'super_admin'
    $query = "INSERT INTO users (username, password, role_id) VALUES (?, ?, (SELECT id FROM roles WHERE role_name = 'super_admin'))";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();

    echo "Super Admin created successfully!";
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Register Super Admin</title>
</head>

<body>
    <form method="POST">
        <label>Username: </label><input type="text" name="username" required>
        <label>Password: </label><input type="password" name="password" required>
        <button type="submit">Register Super Admin</button>
    </form>
</body>

</html>