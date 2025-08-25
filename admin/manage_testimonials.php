<?php
// Page-specific variables
$page_title = "Manage Testimonials";
$active_page = "testimonials";

// Include header (handles session, db, etc.)
require_once 'partials/header.php';
 
// Handle secure POST delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = $_POST['id'];
    $sql = "DELETE FROM testimonials WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            header("location: manage_testimonials.php?status=deleted");
            exit;
        }
        mysqli_stmt_close($stmt);
    }
}
 
// Handle edit request to fetch data for the form
$edit_testimonial = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM testimonials WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            $edit_testimonial = mysqli_fetch_assoc($result);
        }
        mysqli_stmt_close($stmt);
    }
}
 
// Fetch all testimonials to display in the table
$testimonials = [];
$sql = "SELECT * FROM testimonials ORDER BY id ASC";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $testimonials[] = $row;
    }
}

// Include the sidebar
require_once 'partials/sidebar.php';
?>
 
<header class="main-header">
    <div class="header-left">
        <h2>Manage Testimonials</h2>
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
            <h3 style="margin: 0; color: #7b68ee; font-size: 1.2em;">Testimonials List</h3>
            <button id="toggle-form" class="action-btn edit-btn" style="background-color: #7b68ee;">
                <i class="fas fa-plus"></i> <?php echo ($edit_testimonial) ? 'Cancel Edit' : 'Add New Testimonial'; ?>
            </button>
        </div>
        
        <?php if (count($testimonials) > 0): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Author</th>
                        <th>Company</th>
                        <th>Testimonial</th>
                        <th>Rating</th> <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($testimonials as $testimonial): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($testimonial['client_name']); ?></td>
                            <td><?php echo htmlspecialchars($testimonial['client_company']); ?></td>
                            <td><?php echo htmlspecialchars(substr($testimonial['review_text'], 0, 50)) . '...'; ?></td>
                            <td>
                                <?php if (isset($testimonial['rating']) && $testimonial['rating'] > 0): ?>
                                    <?php for ($i = 0; $i < $testimonial['rating']; $i++): ?>
                                        <i class="fas fa-star" style="color: #f1c40f;"></i>
                                    <?php endfor; ?>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="actions-container">
                                    <a href="manage_testimonials.php?id=<?php echo $testimonial['id']; ?>&action=edit" class="action-btn edit-btn">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="manage_testimonials.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this testimonial?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $testimonial['id']; ?>">
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
            <p class="no-data">No testimonials found. Add your first one.</p>
        <?php endif; ?>
    </div>

    <div id="form-container" class="card" style="padding: 25px; display: <?php echo ($edit_testimonial) ? 'block' : 'none'; ?>; margin-top: 25px;">
        <h3><?php echo ($edit_testimonial) ? 'Edit Testimonial' : 'Add New Testimonial'; ?></h3>
        <form action="../api/testimonials.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="<?php echo ($edit_testimonial) ? 'edit' : 'add'; ?>">
            <?php if ($edit_testimonial): ?>
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($edit_testimonial['id']); ?>">
            <?php endif; ?>
            <div class="form-group">
                <label for="client-name">Author Name</label>
                <input type="text" id="client-name" name="client_name" required value="<?php echo htmlspecialchars($edit_testimonial['client_name'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="client-company">Company / Position</label>
                <input type="text" id="client-company" name="client_company" value="<?php echo htmlspecialchars($edit_testimonial['client_company'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="review-text">Testimonial Text</label>
                <textarea id="review-text" name="review_text" rows="5" required><?php echo htmlspecialchars($edit_testimonial['review_text'] ?? ''); ?></textarea>
            </div>
             <div class="form-group">
                <label for="client-photo">Author Photo</label>
                <input type="file" id="client-photo" name="client_photo" accept="image/*">
            </div>
             <div class="form-group">
                <label for="rating">Rating (1-5)</label>
                <input type="number" id="rating" name="rating" min="1" max="5" value="<?php echo htmlspecialchars($edit_testimonial['rating'] ?? '5'); ?>">
            </div>
            <button type="submit"><?php echo ($edit_testimonial) ? 'Update Testimonial' : 'Add Testimonial'; ?></button>
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
            
            if (<?php echo json_encode($edit_testimonial !== null); ?> && isVisible) {
                window.location.href = 'manage_testimonials.php';
            }
        });
    });
</script>

<?php
// Include the footer
require_once 'partials/footer.php';
?>