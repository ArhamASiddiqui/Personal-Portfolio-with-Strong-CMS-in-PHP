<?php
// Page-specific variables
$page_title = "Manage Companies";
$active_page = "companies";

// Include header (handles session, db, etc.)
require_once 'partials/header.php';
 
// Handle secure POST delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = $_POST['id'];
    $sql = "DELETE FROM projects WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            header("location: manage_companies.php?status=deleted");
            exit;
        }
        mysqli_stmt_close($stmt);
    }
}
 
// Handle edit request
$edit_company = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM projects WHERE id = ? AND type = 'website'";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            $edit_company = mysqli_fetch_assoc($result);
        }
        mysqli_stmt_close($stmt);
    }
}
 
// Fetch only companies (type 'website') from the database
$companies = [];
$sql = "SELECT * FROM projects WHERE type = 'website' ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $companies[] = $row;
    }
}

// Include the sidebar
require_once 'partials/sidebar.php';
?>
 
<header class="main-header">
    <div class="header-left">
        <h2>Manage Companies</h2>
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
    <div class="card" style="text-align: left; padding: 25px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0; color: #7b68ee; font-size: 1.2em; font-weight: 600;">Companies List</h3>
            <button id="toggle-form" class="action-btn edit-btn" style="background-color: #7b68ee;">
                <i class="fas fa-plus"></i> <?php echo ($edit_company) ? 'Cancel Edit' : 'Add New Company'; ?>
            </button>
        </div>
        
        <?php if (count($companies) > 0): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Featured Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($companies as $company): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($company['title']); ?></td>
                            <td>
                                <?php if (!empty($company['featured_image'])): ?>
                                    <img src="../assets/images/<?php echo htmlspecialchars($company['featured_image']); ?>" alt="<?php echo htmlspecialchars($company['title']); ?>" class="project-image">
                                <?php else: ?>
                                    No Image
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="actions-container">
                                    <a href="manage_companies.php?id=<?php echo $company['id']; ?>&action=edit" class="action-btn edit-btn">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="manage_companies.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this company?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $company['id']; ?>">
                                        <button type="submit" class="action-btn delete-btn">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">No companies found. Add your first company using the form below.</p>
        <?php endif; ?>
    </div>
 
    <div id="form-container" class="card" style="text-align: left; padding: 25px; display: <?php echo ($edit_company) ? 'block' : 'none'; ?>; margin-top: 25px;">
        <h3 style="margin-top: 0; color: #7b68ee; font-size: 1.2em; font-weight: 600; margin-bottom: 20px;">
            <?php echo ($edit_company) ? 'Edit Company' : 'Add New Company'; ?>
        </h3>
        <form action="../api/projects.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="<?php echo ($edit_company) ? 'edit' : 'add'; ?>">
            <?php if ($edit_company): ?>
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($edit_company['id']); ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="company-title">Title</label>
                <input type="text" id="company-title" name="title" required value="<?php echo htmlspecialchars($edit_company['title'] ?? ''); ?>">
            </div>
            
            <input type="hidden" name="type" value="website">

            <div class="form-group">
                <label for="company-desc">Description</label>
                <textarea id="company-desc" name="description" rows="5"><?php echo htmlspecialchars($edit_company['description'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="company-image">Featured Image</label>
                <input type="file" id="company-image" name="featured_image" accept="image/*">
            </div>
            
            <div class="form-group">
                <label for="live-link">Live Link</label>
                <input type="url" id="live-link" name="live_link" value="<?php echo htmlspecialchars($edit_company['live_link'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="source-link">Source Link</label>
                <input type="url" id="source-link" name="source_link" value="<?php echo htmlspecialchars($edit_company['source_link'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="technologies">Technologies (comma-separated)</label>
                <input type="text" id="technologies" name="technologies" value="<?php echo htmlspecialchars($edit_company['technologies'] ?? ''); ?>">
            </div>
            
            <button type="submit"><?php echo ($edit_company) ? 'Update Company' : 'Add Company'; ?></button>
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
            
            if (<?php echo json_encode($edit_company !== null); ?> && isVisible) {
                window.location.href = 'manage_companies.php';
            }
        });
    });
</script>
 
<?php
// Include the footer
require_once 'partials/footer.php';
?>