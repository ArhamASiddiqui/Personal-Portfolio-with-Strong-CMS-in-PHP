<?php
// Includes the database connection file
require_once 'includes/db.php';

// Fetch all services from the database
$services = [];
$sql = "SELECT * FROM services ORDER BY id ASC";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $services[] = $row;
    }
}
?>
<?php include 'includes/header.php'; ?>

<header class="hero-section">
    <div class="hero-content">
        <div class="hero-text">
            <h1>My Services</h1>
            <p>A list of services I offer to help you build your digital presence.</p>
        </div>
    </div>
</header>

<section class="services-section">
    <div class="services-intro">
        <h2>What I Do</h2>
        <p>We are a dedicated team of professionals ready to help you.</p>
    </div>
    <div class="services-grid">
        <?php if (count($services) > 0): ?>
            <?php foreach ($services as $service): ?>
                <div class="service-card">
                    <h3><?php echo htmlspecialchars($service['service_title']); ?></h3>
                    <p><?php echo htmlspecialchars($service['description']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-data">No services found. Please add some from the Admin Panel.</p>
        <?php endif; ?>
    </div>
</section>
<?php include 'includes/footer.php'; ?>