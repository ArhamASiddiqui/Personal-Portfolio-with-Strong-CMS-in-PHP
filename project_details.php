<?php
require_once 'includes/db.php';
 
$project = null;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM projects WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $project = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
    }
}
 
if (!$project) {
    include 'includes/header.php';
    echo "<div class='container' style='text-align: center; padding: 50px; color: #e5e5e5; background-color: #1a1c3c; margin-top: 50px; border-radius: 10px;'>";
    echo "<h2>Project Not Found</h2>";
    echo "<p>The project you are looking for does not exist.</p>";
    echo "<a href='index.php#projects' class='cta-button' style='margin-top: 20px;'>Back to Projects</a>";
    echo "</div>";
    include 'includes/footer.php';
    exit;
}
?>
<?php include 'includes/header.php'; ?>
 
<div class="project-details-page">
    <div class="project-header-section">
        <h1><?php echo htmlspecialchars($project['title']); ?></h1>
        <hr class="title-divider">
    </div>
 
    <div class="project-image-full-width">
        <img src="assets/images/<?php echo htmlspecialchars($project['featured_image']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>">
    </div>
 
    <div class="project-description-section">
        <h3>Description</h3>
        <p><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>
    </div>
 
    <div class="project-tech-links-section">
        <div class="project-technologies">
            <?php if (!empty($project['technologies'])): ?>
                <h3>Technologies Used</h3>
                <p><?php echo htmlspecialchars($project['technologies']); ?></p>
            <?php endif; ?>
        </div>
        <div class="project-buttons">
            <?php if (!empty($project['live_link'])): ?>
                <a href="<?php echo htmlspecialchars($project['live_link']); ?>" target="_blank" class="cta-button cust">View Live Project</a>
            <?php endif; ?>
            <?php if (!empty($project['source_link'])): ?>
                <a href="<?php echo htmlspecialchars($project['source_link']); ?>" target="_blank" class="cta-button secondary-cta cust">View Source Code</a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>