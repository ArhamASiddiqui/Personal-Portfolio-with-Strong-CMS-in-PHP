<?php
session_start();
require_once '../includes/db.php';

// --- SECURITY: Check if user is logged in and is an admin ---
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    // Send a generic error message or redirect. 403 Forbidden is appropriate.
    http_response_code(403);
    die("Error: You do not have permission to perform this action.");
}
 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {

    // --- ACTION: ADD NEW USER ---
    if ($_POST['action'] === 'add') {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $role = trim($_POST['role']);

        // Basic validation
        if (empty($username) || empty($email) || empty($password) || empty($role)) {
            die("Error: All fields are required.");
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            die("Error: Invalid email format.");
        }

        // Check if username or email already exists
        $sql_check = "SELECT id FROM users WHERE username = ? OR email = ?";
        if($stmt_check = mysqli_prepare($conn, $sql_check)){
            mysqli_stmt_bind_param($stmt_check, "ss", $username, $email);
            mysqli_stmt_execute($stmt_check);
            mysqli_stmt_store_result($stmt_check);
            if(mysqli_stmt_num_rows($stmt_check) > 0){
                die("Error: Username or email already exists.");
            }
            mysqli_stmt_close($stmt_check);
        }

        // --- SECURITY: Hash the password ---
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssss", $username, $email, $hashed_password, $role);
            if (mysqli_stmt_execute($stmt)) {
                header("location: /my_portfolio_cms/admin/manage_users.php?status=added");
                exit;
            }
            mysqli_stmt_close($stmt);
        }
    }

    // --- ACTION: EDIT EXISTING USER ---
    elseif ($_POST['action'] === 'edit' && isset($_POST['id'])) {
        $id = $_POST['id'];
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $role = trim($_POST['role']);

        // Build the query dynamically
        $sql = "UPDATE users SET username = ?, email = ?, role = ?";
        $types = "sssi";
        $params = [$username, $email, $role, $id];

        // If a new password is provided, add it to the query
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET username = ?, email = ?, role = ?, password = ? WHERE id = ?";
            $types = "ssssi";
            $params = [$username, $email, $role, $hashed_password, $id];
        } else {
            $sql .= " WHERE id = ?";
        }

        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind parameters using the compatible method
            $bind_params = [ $types ];
            foreach ($params as $key => $value) {
                $bind_params[] = &$params[$key];
            }
            call_user_func_array([$stmt, 'bind_param'], $bind_params);

            if (mysqli_stmt_execute($stmt)) {
                header("location: /my_portfolio_cms/admin/manage_users.php?status=updated");
                exit;
            }
            mysqli_stmt_close($stmt);
        }
    }
}

mysqli_close($conn);
?>