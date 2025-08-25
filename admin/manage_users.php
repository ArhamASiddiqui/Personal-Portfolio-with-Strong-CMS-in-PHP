<?php
// Page-specific variables
$page_title = "Manage Users";
$active_page = "users";

// Include header (handles session, db, etc.)
require_once 'partials/header.php';

// --- SECURITY: Role-Based Access Control ---
// Only allow admins to access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    // You can redirect to a 'permission-denied' page or just show an error
    die("ACCESS DENIED: You do not have permission to access this page.");
}
 
// Handle secure POST delete request
$delete_err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id_to_delete = $_POST['id'];

    // --- SECURITY: Prevent self-deletion ---
    if ($id_to_delete == $_SESSION['id']) {
        $delete_err = "Error: You cannot delete your own account.";
    } else {
        $sql = "DELETE FROM users WHERE id = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $id_to_delete);
            if (mysqli_stmt_execute($stmt)) {
                header("location: manage_users.php?status=deleted");
                exit;
            }
            mysqli_stmt_close($stmt);
        }
    }
}
 
// Handle edit request to fetch data for the form
$edit_user = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id_to_edit = $_GET['id'];
    $sql = "SELECT id, username, email, role FROM users WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id_to_edit);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            $edit_user = mysqli_fetch_assoc($result);
        }
        mysqli_stmt_close($stmt);
    }
}
 
// Fetch all users except the currently logged-in admin
$users = [];
$current_user_id = $_SESSION['id'];
$sql = "SELECT id, username, email, role FROM users WHERE id != ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $current_user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
    }
    mysqli_stmt_close($stmt);
}


// Include the sidebar
require_once 'partials/sidebar.php';
?>
 
<header class="main-header">
    <div class="header-left">
        <h2>Manage Users</h2>
    </div>
    <div class="header-right">
        <i class="fas fa-search"></i>
        <i class="fas fa-bell"></i>
        <div class="user-profile">
            <img src="assets/images/<?php echo htmlspecialchars($_SESSION['profile_photo'] ?? 'user.jpg'); ?>" alt="User" class="profile-pic">
        </div>
    </div>
</header>
 
<div class="content-body">
    <?php if(!empty($delete_err)): ?>
        <div class="alert" style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px;"><?php echo $delete_err; ?></div>
    <?php endif; ?>

    <div class="card" style="text-align: left; padding: 25px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0; color: #7b68ee; font-size: 1.2em;">Users List</h3>
            <button id="toggle-form" class="action-btn edit-btn" style="background-color: #7b68ee;">
                <i class="fas fa-plus"></i> <?php echo ($edit_user) ? 'Cancel Edit' : 'Add New User'; ?>
            </button>
        </div>
        
        <table class="data-table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars(ucfirst($user['role'])); ?></td>
                        <td>
                            <div class="actions-container">
                                <a href="manage_users.php?id=<?php echo $user['id']; ?>&action=edit" class="action-btn edit-btn">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="manage_users.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" class="action-btn delete-btn">
                                        <i class="fas fa-user-times"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div id="form-container" class="card" style="padding: 25px; display: <?php echo ($edit_user) ? 'block' : 'none'; ?>; margin-top: 25px;">
        <h3><?php echo ($edit_user) ? 'Edit User' : 'Add New User'; ?></h3>
        <form action="../api/users.php" method="post">
            <input type="hidden" name="action" value="<?php echo ($edit_user) ? 'edit' : 'add'; ?>">
            <?php if ($edit_user): ?>
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($edit_user['id']); ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required value="<?php echo htmlspecialchars($edit_user['username'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($edit_user['email'] ?? ''); ?>">
            </div>
             <div class="form-group">
                <label for="role">Role</label>
                <select name="role" id="role" required>
                    <option value="editor" <?php echo (isset($edit_user['role']) && $edit_user['role'] == 'editor') ? 'selected' : ''; ?>>Editor</option>
                    <option value="admin" <?php echo (isset($edit_user['role']) && $edit_user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" <?php echo ($edit_user) ? '' : 'required'; ?>>
                <?php if ($edit_user): ?>
                    <small>Leave blank to keep the current password.</small>
                <?php endif; ?>
            </div>
            <button type="submit"><?php echo ($edit_user) ? 'Update User' : 'Add User'; ?></button>
        </form>
    </div>
</div>
 
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleButton = document.getElementById('toggle-form');
        const formContainer = document.getElementById('form-container');
 
        toggleButton.addEventListener('click', function() {
            const isVisible = formContainer.style.display === 'block';
            formContainer.style.display = isVisible ? 'none' : 'block';
            
            if (<?php echo json_encode($edit_user !== null); ?> && isVisible) {
                window.location.href = 'manage_users.php';
            }
        });
    });
</script>

<?php
// Include the footer
require_once 'partials/footer.php';
?>