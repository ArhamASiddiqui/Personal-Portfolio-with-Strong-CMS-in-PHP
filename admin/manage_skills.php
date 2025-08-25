<?php
// Page-specific variables
$page_title = "Manage Skills";
$active_page = "skills";

// Include header (handles session, db, etc.)
require_once 'partials/header.php';
 
// Handle secure POST delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = $_POST['id'];
    $sql = "DELETE FROM skills WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            header("location: manage_skills.php?status=deleted");
            exit;
        }
        mysqli_stmt_close($stmt);
    }
}

// Handle edit request to fetch data for the form
$edit_skill = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM skills WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            $edit_skill = mysqli_fetch_assoc($result);
        }
        mysqli_stmt_close($stmt);
    }
}
 
// Fetch all skills to display in the table
$skills = [];
$sql = "SELECT * FROM skills ORDER BY percentage DESC";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $skills[] = $row;
    }
}

// Include the sidebar
require_once 'partials/sidebar.php';
?>
 
<header class="main-header">
    <div class="header-left">
        <h2>Manage Skills</h2>
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
            <h3 style="margin: 0; color: #7b68ee; font-size: 1.2em;">Skills List</h3>
            <button id="toggle-form" class="action-btn edit-btn" style="background-color: #7b68ee;">
                <i class="fas fa-plus"></i> <?php echo ($edit_skill) ? 'Cancel Edit' : 'Add New Skill'; ?>
            </button>
        </div>
        
        <?php if (count($skills) > 0): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Skill Name</th>
                    <th>Percentage</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($skills as $skill): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($skill['skill_name']); ?></td>
                        <td><?php echo htmlspecialchars($skill['percentage']); ?>%</td>
                        <td>
                            <div class="actions-container">
                                <a href="manage_skills.php?id=<?php echo $skill['id']; ?>&action=edit" class="action-btn edit-btn">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="manage_skills.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this skill?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $skill['id']; ?>">
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
        <p class="no-data" style="text-align: center;">No skills found. Add your first skill.</p>
        <?php endif; ?>
    </div>

    <div id="form-container" class="card" style="padding: 25px; display: <?php echo ($edit_skill) ? 'block' : 'none'; ?>; margin-top: 25px;">
        <h3><?php echo ($edit_skill) ? 'Edit Skill' : 'Add New Skill'; ?></h3>
        <form action="../api/skills.php" method="post">
            <input type="hidden" name="action" value="<?php echo ($edit_skill) ? 'edit' : 'add'; ?>">
            <?php if ($edit_skill): ?>
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($edit_skill['id']); ?>">
            <?php endif; ?>
            <div class="form-group">
                <label for="skill-name">Skill Name</label>
                <input type="text" id="skill-name" name="skill_name" required value="<?php echo htmlspecialchars($edit_skill['skill_name'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="skill-percentage">Percentage (0-100)</label>
                <input type="number" id="skill-percentage" name="percentage" min="0" max="100" required value="<?php echo htmlspecialchars($edit_skill['percentage'] ?? ''); ?>">
            </div>
            <button type="submit"><?php echo ($edit_skill) ? 'Update Skill' : 'Add Skill'; ?></button>
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
            
            if (<?php echo json_encode($edit_skill !== null); ?> && isVisible) {
                window.location.href = 'manage_skills.php';
            }
        });
    });
</script>

<?php
// Include the footer
require_once 'partials/footer.php';
?>