<?php
// Page-specific variables
$page_title = "Manage Blog Posts";
$active_page = "blogs";

// Include header (handles session, db, etc.)
require_once 'partials/header.php';
 
// Handle secure POST delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = $_POST['id'];
    $sql = "DELETE FROM blog_posts WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            header("location: manage_blogs.php?status=deleted");
            exit;
        }
        mysqli_stmt_close($stmt);
    }
}

// Handle edit request to fetch data for the form
$edit_blog = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM blog_posts WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            $edit_blog = mysqli_fetch_assoc($result);
        }
        mysqli_stmt_close($stmt);
    }
}
 
// Fetch all blog posts to display in the table
$blogs = [];
$sql = "SELECT id, title, is_published, created_at FROM blog_posts ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $blogs[] = $row;
    }
}

// Include the sidebar
require_once 'partials/sidebar.php';
?>
 
<header class="main-header">
    <div class="header-left">
        <h2>Manage Blog Posts</h2>
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
            <h3 style="margin: 0; color: #7b68ee; font-size: 1.2em;">Blog Posts List</h3>
            <button id="toggle-form" class="action-btn edit-btn" style="background-color: #7b68ee;">
                <i class="fas fa-plus"></i> <?php echo ($edit_blog) ? 'Cancel Edit' : 'Add New Post'; ?>
            </button>
        </div>
        
        <?php if (count($blogs) > 0): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Published</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($blogs as $blog): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($blog['title']); ?></td>
                        <td><?php echo $blog['is_published'] ? 'Yes' : 'No'; ?></td>
                        <td>
                            <div class="actions-container">
                                <a href="manage_blogs.php?id=<?php echo $blog['id']; ?>&action=edit" class="action-btn edit-btn">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="manage_blogs.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this blog post?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $blog['id']; ?>">
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
        <p class="no-data" style="text-align: center;">No blog posts found. Add your first post.</p>
        <?php endif; ?>
    </div>

    <div id="form-container" class="card" style="padding: 25px; display: <?php echo ($edit_blog) ? 'block' : 'none'; ?>; margin-top: 25px;">
        <h3><?php echo ($edit_blog) ? 'Edit Post' : 'Add New Post'; ?></h3>
        <form action="../api/blogs.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="<?php echo ($edit_blog) ? 'edit' : 'add'; ?>">
            <?php if ($edit_blog): ?>
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($edit_blog['id']); ?>">
            <?php endif; ?>
            <div class="form-group">
                <label for="post-title">Title</label>
                <input type="text" id="post-title" name="title" required value="<?php echo htmlspecialchars($edit_blog['title'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="post-content">Content</label>
                <textarea id="post-content" name="content" rows="10" required><?php echo htmlspecialchars($edit_blog['content'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label for="post-image">Featured Image</label>
                <input type="file" id="post-image" name="featured_image" accept="image/*">
                <?php if ($edit_blog && !empty($edit_blog['featured_image'])): ?>
                    <small>Current image: <?php echo htmlspecialchars($edit_blog['featured_image']); ?></small>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <input type="checkbox" id="is-published" name="is_published" value="1" <?php echo ($edit_blog && $edit_blog['is_published'] == 1) ? 'checked' : ''; ?>>
                <label for="is-published" style="display: inline-block; font-weight: normal;">Publish Post</label>
            </div>
            <button type="submit"><?php echo ($edit_blog) ? 'Update Post' : 'Add Post'; ?></button>
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
            
            if (<?php echo json_encode($edit_blog !== null); ?> && isVisible) {
                window.location.href = 'manage_blogs.php';
            }
        });
    });
</script>

<?php
// Include the footer
require_once 'partials/footer.php';
?>