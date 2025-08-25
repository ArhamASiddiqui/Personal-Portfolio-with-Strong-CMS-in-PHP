<?php
// Page-specific variables
$page_title = "Manage Projects";
$active_page = "projects";

// Include header (handles session, db, etc.)
require_once 'partials/header.php';
 
// Handle secure POST delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = $_POST['id'];
    $sql = "DELETE FROM projects WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            header("location: manage_projects.php?status=deleted");
            exit;
        }
        mysqli_stmt_close($stmt);
    }
}
 
// Handle edit request (this is safe as a GET request)
$edit_project = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM projects WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            $edit_project = mysqli_fetch_assoc($result);
        }
        mysqli_stmt_close($stmt);
    }
}
 
// Fetch only projects from the database
$projects = [];
$sql = "SELECT * FROM projects WHERE type = 'project' ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $projects[] = $row;
    }
}

// Include the sidebar
require_once 'partials/sidebar.php';
?>
 
<header class="main-header">
    <div class="header-left">
        <h2>Manage Projects</h2>
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
            <h3 style="margin: 0; color: #7b68ee; font-size: 1.2em; font-weight: 600;">Projects List</h3>
            <button id="toggle-form" class="action-btn edit-btn" style="background-color: #7b68ee;">
                <i class="fas fa-plus"></i> <?php echo ($edit_project) ? 'Cancel Edit' : 'Add New Project'; ?>
            </button>
        </div>
        
        <?php if (count($projects) > 0): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Featured Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $project): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($project['title']); ?></td>
                            <td>
                                <?php if (!empty($project['featured_image'])): ?>
                                    <img src="../assets/images/<?php echo htmlspecialchars($project['featured_image']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>" class="project-image">
                                <?php else: ?>
                                    No Image
                                <?php endif; ?>
                            </td>
                          <td>
    <div class="actions-container">
        <a href="manage_projects.php?id=<?php echo $project['id']; ?>&action=edit" class="action-btn edit-btn">
            <i class="fas fa-edit"></i> Edit
        </a>

        <form action="manage_projects.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this project?');">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?php echo $project['id']; ?>">
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
            <p class="no-data">No projects found. Add your first project using the form below.</p>
        <?php endif; ?>
    </div>
 
    <div id="form-container" class="card" style="text-align: left; padding: 25px; display: <?php echo ($edit_project) ? 'block' : 'none'; ?>; margin-top: 25px;">
        <h3 style="margin-top: 0; color: #7b68ee; font-size: 1.2em; font-weight: 600; margin-bottom: 20px;">
            <?php echo ($edit_project) ? 'Edit Project' : 'Add New Project'; ?>
        </h3>
        <form action="../api/projects.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="<?php echo ($edit_project) ? 'edit' : 'add'; ?>">
            <?php if ($edit_project): ?>
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($edit_project['id']); ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="project-title">Title</label>
                <input type="text" id="project-title" name="title" required value="<?php echo htmlspecialchars($edit_project['title'] ?? ''); ?>">
            </div>
            
            <input type="hidden" name="type" value="project">

            <div class="form-group">
                <label for="project-desc">Description</label>
                <textarea id="project-desc" name="description" rows="5"><?php echo htmlspecialchars($edit_project['description'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="project-image">Featured Image</label>
                <input type="file" id="project-image" name="featured_image" accept="image/*">
            </div>
            
            <div class="form-group">
                <label for="live-link">Live Link</label>
                <input type="url" id="live-link" name="live_link" value="<?php echo htmlspecialchars($edit_project['live_link'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="source-link">Source Link</label>
                <input type="url" id="source-link" name="source_link" value="<?php echo htmlspecialchars($edit_project['source_link'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="technologies">Technologies (comma-separated)</label>
                <input type="text" id="technologies" name="technologies" value="<?php echo htmlspecialchars($edit_project['technologies'] ?? ''); ?>">
            </div>
            
            <button type="submit"><?php echo ($edit_project) ? 'Update Project' : 'Add Project'; ?></button>
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
            
            // If we are in edit mode, clicking cancel should remove the edit params from URL
            if (<?php echo json_encode($edit_project !== null); ?> && isVisible) {
                window.location.href = 'manage_projects.php';
            }
        });
    });
</script>
 
<?php
// Include the footer
require_once 'partials/footer.php';
?>