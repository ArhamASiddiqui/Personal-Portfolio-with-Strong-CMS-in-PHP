<?php
// Includes the database connection file
require_once 'includes/db.php';

// Fetch all skills from the database
$skills = [];
$sql = "SELECT * FROM skills ORDER BY proficiency DESC";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $skills[] = $row;
    }
}
?>
<?php include 'includes/header.php'; ?>

<header class="hero-section">
    <div class="hero-content">
        <div class="hero-text">
            <h1>About Me</h1>
            <p>A passionate web developer creating modern and dynamic websites.</p>
        </div>
    </div>
</header>

<section class="about-section">
    <div class="container">
        <h2>About Me</h2>
        <div class="about-content">
            <div class="about-text">
                <p>Hello! I'm Arham, a passionate developer with a strong focus on creating dynamic and user-friendly web experiences. With a keen eye for detail and a love for clean code, I build websites that are not only beautiful but also highly functional. My mission is to help businesses and individuals bring their ideas to life online.</p>
            </div>
            <div class="skills-list">
                <h3>My Skills</h3>
                <?php if (count($skills) > 0): ?>
                    <?php foreach ($skills as $skill): ?>
                        <div class="skill-item">
                            <h4 style="margin-bottom: 5px;"><?php echo htmlspecialchars($skill['skill_name']); ?> (<?php echo htmlspecialchars($skill['proficiency']); ?>%)</h4>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo htmlspecialchars($skill['proficiency']); ?>%;"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-data">No skills found. Please add some from the Admin Panel.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>