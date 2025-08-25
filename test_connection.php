<?php
// Includes the database connection file
require_once 'includes/db.php';

// Check if the connection variable is set
if (isset($conn)) {
    echo "<h1>Database Connection Successful!</h1>";
    echo "<p>You are now connected to the 'portfolio_cms' database.</p>";
} else {
    echo "<h1>Database Connection Failed!</h1>";
    echo "<p>Please check your 'includes/db.php' file and XAMPP status.</p>";
}

// Close the connection (good practice)
mysqli_close($conn);

?>