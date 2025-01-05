<?php
$password = 'fr!Church1ad1'; // Replace with the Super Admin's password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
echo "Hashed Password: " . $hashedPassword;
