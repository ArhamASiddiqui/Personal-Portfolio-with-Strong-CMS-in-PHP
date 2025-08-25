<?php
// Password you want to use
$password_to_hash = 'password123';

// Generate a secure password hash
$hashed_password = password_hash($password_to_hash, PASSWORD_DEFAULT);

echo $hashed_password;
?>