<?php
// Page-specific variables
$page_title = "Manage Messages";
$active_page = "messages";

// Header ko include karna (Isme session check aur db connection ho jayega)
require_once 'partials/header.php';

// === SECURITY FIX: Handle delete request using POST instead of GET ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = $_POST['id'];
    $sql = "DELETE FROM contact_messages WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            // Redirect to avoid form resubmission on refresh
            header("location: manage_messages.php?status=deleted");
            exit;
        } else {
            echo "Error: Could not execute the delete query.";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Error: Could not prepare the delete statement.";
    }
}

// Fetch all messages from the database
$messages = [];
$sql = "SELECT * FROM contact_messages ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $messages[] = $row;
    }
}
?>

<?php
// Sidebar ko include karna
require_once 'partials/sidebar.php';
?>

<header class="main-header">
    <div class="header-left">
        <h2>Manage Messages</h2>
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
        <h3 style="margin-top: 0; color: #7b68ee; font-size: 1.2em; font-weight: 600;">Message List</h3>
        <?php if (count($messages) > 0): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Sender Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Received At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $message): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($message['sender_name']); ?></td>
                            <td><?php echo htmlspecialchars($message['sender_email']); ?></td>
                            <td><?php echo htmlspecialchars($message['subject']); ?></td>
                            <td><?php echo date("d M, Y h:i A", strtotime($message['created_at'])); ?></td>
                            <td>
                                <div class="actions-container">

                                    <a href="mailto:<?php echo htmlspecialchars($message['sender_email']); ?>"
                                        class="action-btn reply-btn">
                                        <i class="fas fa-reply"></i> Reply
                                    </a>

                                    <form action="manage_messages.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this message?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $message['id']; ?>">
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
            <p class="no-data">No messages found.</p>
        <?php endif; ?>
    </div>
</div>

<?php
// Footer ko include karna
require_once 'partials/footer.php';
?>