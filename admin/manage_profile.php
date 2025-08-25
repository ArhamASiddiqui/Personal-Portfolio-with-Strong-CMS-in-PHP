<?php
// Page-specific variables
$page_title = "Manage Profile";
$active_page = "profile";

// Include header (handles session, db, etc.)
require_once 'partials/header.php';

$update_err = '';
$update_success = '';
 
// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION["id"];
    
    // --- NEW SECURITY CHECK ---
    // First, verify the current password
    $current_password = $_POST['current_password'];
    $sql_check = "SELECT password FROM users WHERE id = ?";
    if ($stmt_check = mysqli_prepare($conn, $sql_check)) {
        mysqli_stmt_bind_param($stmt_check, "i", $user_id);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);
        $user_check = mysqli_fetch_assoc($result_check);
        
        if (!password_verify($current_password, $user_check['password'])) {
            $update_err = "The current password you entered is incorrect.";
        }
        mysqli_stmt_close($stmt_check);
    }
    // --- END SECURITY CHECK ---

    // If the current password is correct, proceed with the update
    if (empty($update_err)) {
        $new_username = trim($_POST['username']);
        $new_email = trim($_POST['email']);
        $new_password = trim($_POST['new_password']);
        
        $sql_parts = [];
        $params = [];
        $param_types = '';
        
        // Always update username and email
        $sql_parts[] = "username = ?";
        $params[] = $new_username;
        $param_types .= 's';
        
        $sql_parts[] = "email = ?";
        $params[] = $new_email;
        $param_types .= 's';
    
        // Only update password if a new one is provided
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $sql_parts[] = "password = ?";
            $params[] = $hashed_password;
            $param_types .= 's';
        }
    
        // Only update profile photo if a new one is uploaded
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === 0) {
            $target_dir = "assets/images/"; // Relative to the admin folder
            $file_name = uniqid() . '-' . basename($_FILES["profile_photo"]["name"]);
            $target_file = $target_dir . $file_name;
            
            if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $target_file)) {
                $sql_parts[] = "profile_photo = ?";
                $params[] = $file_name;
                $param_types .= 's';
                $_SESSION['profile_photo'] = $file_name; // Update session immediately
            }
        }
    
        $sql_update = "UPDATE users SET " . implode(', ', $sql_parts) . " WHERE id = ?";
        $params[] = $user_id;
        $param_types .= 'i';
    
        if ($stmt_update = mysqli_prepare($conn, $sql_update)) {
            mysqli_stmt_bind_param($stmt_update, $param_types, ...$params);
            if (mysqli_stmt_execute($stmt_update)) {
                $_SESSION["username"] = $new_username; // Update session immediately
                $update_success = "Your profile has been updated successfully.";
            } else {
                $update_err = "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt_update);
        }
    }
}
 
// Fetch current user data to populate the form
$user_id = $_SESSION["id"];
$user_data = [];
$sql = "SELECT username, email, profile_photo FROM users WHERE id = ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user_data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

// Include the sidebar
require_once 'partials/sidebar.php';
?>
 
<header class="main-header">
    <div class="header-left">
        <h2>Manage Profile</h2>
    </div>
    <div class="header-right">
        <i class="fas fa-search"></i>
        <i class="fas fa-bell"></i>
        <div class="user-profile">
            <img src="assets/images/<?php echo htmlspecialchars($_SESSION['profile_photo'] ?? 'user.jpg'); ?>?v=<?php echo time(); ?>" alt="User" class="profile-pic">
        </div>
    </div>
</header>
 
<div class="content-body">
    <div class="card" style="text-align: left; padding: 25px;">
        <h3 style="margin-top: 0; color: #7b68ee; font-size: 1.2em; font-weight: 600; margin-bottom: 20px;">Update Your Profile</h3>
        
        <?php if (!empty($update_err)): ?>
            <div class="alert" style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px;"><?php echo $update_err; ?></div>
        <?php endif; ?>
        <?php if (!empty($update_success)): ?>
            <div class="success-message"><?php echo $update_success; ?></div>
        <?php endif; ?>

        <form action="manage_profile.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required value="<?php echo htmlspecialchars($user_data['username'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($user_data['email'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password">
                <small>Leave blank to keep your current password.</small>
            </div>
            <div class="form-group">
                <label for="profile_photo">Profile Picture</label>
                <input type="file" id="profile_photo" name="profile_photo" accept="image/*">
                 <?php if (!empty($user_data['profile_photo'])): ?>
                    <small>Current photo: <?php echo htmlspecialchars($user_data['profile_photo']); ?></small>
                <?php endif; ?>
            </div>
            <hr style="margin: 20px 0;">
            <div class="form-group">
                <label for="current_password"><strong>Current Password (Required to Save Changes)</strong></label>
                <input type="password" id="current_password" name="current_password" required>
            </div>
            <button type="submit">Update Profile</button>
        </form>
    </div>
</div>
 
<?php
// Include the footer
require_once 'partials/footer.php';
?>