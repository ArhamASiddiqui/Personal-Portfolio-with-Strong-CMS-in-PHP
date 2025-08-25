<?php
// Page-specific variables
$page_title = "Manage Services";
$active_page = "services";

// Include header (handles session, db, etc.)
require_once 'partials/header.php';
 
// Handle secure POST delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = $_POST['id'];
    $sql = "DELETE FROM services WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            header("location: manage_services.php?status=deleted");
            exit;
        }
        mysqli_stmt_close($stmt);
    }
}
 
// Handle edit request to fetch data for the form
$edit_service = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM services WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            $edit_service = mysqli_fetch_assoc($result);
        }
        mysqli_stmt_close($stmt);
    }
}
 
// Fetch all services to display in the table
$services = [];
$sql = "SELECT * FROM services ORDER BY id ASC";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $services[] = $row;
    }
}

// Include the sidebar
require_once 'partials/sidebar.php';
?>
 
<header class="main-header">
    <div class="header-left">
        <h2>Manage Services</h2>
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
            <h3 style="margin: 0; color: #7b68ee; font-size: 1.2em;">Service List</h3>
            <button id="toggle-form" class="action-btn edit-btn" style="background-color: #7b68ee;">
                <i class="fas fa-plus"></i> <?php echo ($edit_service) ? 'Cancel Edit' : 'Add New Service'; ?>
            </button>
        </div>
        
        <?php if (count($services) > 0): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $service): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($service['service_title']); ?></td>
                            <td><?php echo htmlspecialchars($service['description']); ?></td>
                            <td>
                                <div class="actions-container">
                                    <a href="manage_services.php?id=<?php echo $service['id']; ?>&action=edit" class="action-btn edit-btn">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="manage_services.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this service?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $service['id']; ?>">
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
            <p class="no-data">No services found. Add your first service.</p>
        <?php endif; ?>
    </div>

    <div id="form-container" class="card" style="padding: 25px; display: <?php echo ($edit_service) ? 'block' : 'none'; ?>; margin-top: 25px;">
        <h3><?php echo ($edit_service) ? 'Edit Service' : 'Add New Service'; ?></h3>
        <form action="../api/services.php" method="post">
            <input type="hidden" name="action" value="<?php echo ($edit_service) ? 'edit' : 'add'; ?>">
            <?php if ($edit_service): ?>
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($edit_service['id']); ?>">
            <?php endif; ?>
            <div class="form-group">
                <label for="service-title">Title</label>
                <input type="text" id="service-title" name="service_title" required value="<?php echo htmlspecialchars($edit_service['service_title'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="service-description">Description</label>
                <textarea id="service-description" name="description" rows="5" required><?php echo htmlspecialchars($edit_service['description'] ?? ''); ?></textarea>
            </div>
            <button type="submit"><?php echo ($edit_service) ? 'Update Service' : 'Add Service'; ?></button>
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
            
            if (<?php echo json_encode($edit_service !== null); ?> && isVisible) {
                window.location.href = 'manage_services.php';
            }
        });
    });
</script>

<?php
// Include the footer
require_once 'partials/footer.php';
?>