<?php
// Page-specific variables
$page_title = "Manage Settings";
$active_page = "settings";

// Include header (handles session, db, etc.)
require_once 'partials/header.php';

// --- SECURITY: Role-Based Access Control ---
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    die("ACCESS DENIED: You do not have permission to access this page.");
}

// Handle form submission to update settings
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Update text-based settings from the 'settings' array
    if(isset($_POST['settings']) && is_array($_POST['settings'])) {
        foreach ($_POST['settings'] as $key => $value) {
            $sql = "UPDATE settings SET setting_value = ? WHERE setting_key = ?";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "ss", $value, $key);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
    }

    // Handle file upload for hero image
    if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] === 0) {
        $target_dir = "../assets/images/"; // Relative to the main site's assets
        $file_name = uniqid() . '-' . basename($_FILES["hero_image"]["name"]);
        $target_file = $target_dir . $file_name;
        
        if (move_uploaded_file($_FILES["hero_image"]["tmp_name"], $target_file)) {
            $sql_update_image = "UPDATE settings SET setting_value = ? WHERE setting_key = 'hero_image'";
            if ($stmt = mysqli_prepare($conn, $sql_update_image)) {
                mysqli_stmt_bind_param($stmt, "s", $file_name);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
    }
    
    // Redirect back with a success message
    header("location: manage_settings.php?status=updated");
    exit;
}

// Fetch all settings from the database to populate the form
$settings = [];
$sql = "SELECT setting_key, setting_value FROM settings";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}

// Include the sidebar
require_once 'partials/sidebar.php';
?>

<header class="main-header">
    <div class="header-left">
        <h2>Manage Settings</h2>
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
        <h3 style="margin-top: 0; color: #7b68ee; font-size: 1.2em; font-weight: 600; margin-bottom: 20px;">Website Settings</h3>
        
        <?php if (isset($_GET['status']) && $_GET['status'] == 'updated'): ?>
            <div class="success-message">Settings have been saved successfully.</div>
        <?php endif; ?>

        <form action="manage_settings.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="hero_title">Hero Section Title</label>
                <input type="text" id="hero_title" name="settings[hero_title]" value="<?php echo htmlspecialchars($settings['hero_title'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="hero_tagline">Hero Section Tagline</label>
                <input type="text" id="hero_tagline" name="settings[hero_tagline]" value="<?php echo htmlspecialchars($settings['hero_tagline'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="social_linkedin">LinkedIn URL</label>
                <input type="url" id="social_linkedin" name="settings[social_linkedin]" value="<?php echo htmlspecialchars($settings['social_linkedin'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="social_github">GitHub URL</label>
                <input type="url" id="social_github" name="settings[social_github]" value="<?php echo htmlspecialchars($settings['social_github'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="social_twitter">Twitter URL</label>
                <input type="url" id="social_twitter" name="settings[social_twitter]" value="<?php echo htmlspecialchars($settings['social_twitter'] ?? ''); ?>">
            </div>
            <hr style="margin: 20px 0;">
            <h4 style="color: #7b68ee;">SEO Settings</h4>
            <div class="form-group">
                <label for="meta_title">Meta Title</label>
                <input type="text" id="meta_title" name="settings[meta_title]" value="<?php echo htmlspecialchars($settings['meta_title'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="meta_description">Meta Description</label>
                <textarea id="meta_description" name="settings[meta_description]" rows="3"><?php echo htmlspecialchars($settings['meta_description'] ?? ''); ?></textarea>
            </div>
            <hr style="margin: 20px 0;">
            <div class="form-group">
                <label for="hero_image">Hero Section Image</label>
                <input type="file" id="hero_image" name="hero_image" accept="image/*">
                <?php if (!empty($settings['hero_image'])): ?>
                    <small>Current image: <a href="../assets/images/<?php echo htmlspecialchars($settings['hero_image']); ?>" target="_blank"><?php echo htmlspecialchars($settings['hero_image']); ?></a></small>
                <?php endif; ?>
            </div>
            <button type="submit">Save Settings</button>
        </form>
    </div>
</div>

<?php
// Include the footer
require_once 'partials/footer.php';
?>