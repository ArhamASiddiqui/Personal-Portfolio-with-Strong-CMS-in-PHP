<?php
// Includes the database connection file
require_once 'includes/db.php';

// Fetch all the necessary data for the website
$settings = [];
$sql_settings = "SELECT setting_key, setting_value FROM settings";
$result_settings = mysqli_query($conn, $sql_settings);
if ($result_settings) {
    while ($row = mysqli_fetch_assoc($result_settings)) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}

$projects = [];
$sql_projects = "SELECT * FROM projects WHERE type = 'project' ORDER BY created_at DESC LIMIT 6";
$result_projects = mysqli_query($conn, $sql_projects);
if ($result_projects) {
    while ($row = mysqli_fetch_assoc($result_projects)) {
        $projects[] = $row;
    }
}

$skills = [];
// FIXED: Changed 'proficiency' to 'percentage'
$sql_skills = "SELECT * FROM skills ORDER BY percentage DESC";
$result_skills = mysqli_query($conn, $sql_skills);
if ($result_skills) {
    while ($row = mysqli_fetch_assoc($result_skills)) {
        $skills[] = $row;
    }
}

// ... (rest of your PHP queries for services, testimonials, etc. remain the same)
$services = [];
$sql_services = "SELECT * FROM services ORDER BY id ASC";
$result_services = mysqli_query($conn, $sql_services);
if ($result_services && mysqli_num_rows($result_services) > 0) {
    while ($row = mysqli_fetch_assoc($result_services)) {
        $services[] = $row;
    }
}
$testimonials = [];
$sql_testimonials = "SELECT * FROM testimonials ORDER BY created_at DESC LIMIT 3";
$result_testimonials = mysqli_query($conn, $sql_testimonials);
if ($result_testimonials && mysqli_num_rows($result_testimonials) > 0) {
    while ($row = mysqli_fetch_assoc($result_testimonials)) {
        $testimonials[] = $row;
    }
}
$companies = [];
$sql_companies = "SELECT * FROM projects WHERE type = 'website' ORDER BY created_at DESC";
$result_companies = mysqli_query($conn, $sql_companies);
if ($result_companies && mysqli_num_rows($result_companies) > 0) {
    while ($row = mysqli_fetch_assoc($result_companies)) {
        $companies[] = $row;
    }
}
?>
<?php include 'includes/header.php'; ?>

<header class="hero-section">
    <div class="hero-content">
        <div class="hero-text">
            <h1><?php echo htmlspecialchars($settings['hero_title'] ?? 'Videographer\'s Portfolio'); ?></h1>
            <p><?php echo htmlspecialchars($settings['hero_tagline'] ?? 'A professional videographer creating stunning visuals for your brand.'); ?></p>
            <a href="#projects" class="cta-button">View My Work</a>
        </div>
        <img src="assets/images/<?php echo htmlspecialchars($settings['hero_image'] ?? 'hero-image.jpg'); ?>" alt="Hero Image" class="hero-image">
    </div>
</header>
<section id="about" class="about-section animated-section">
    <div class="container">
        <h2>About Me</h2>
        <div class="about-content">
            <div class="about-text">
                <p>Hello! I'm Arham A. Siddiqui, a passionate web developer dedicated to building exceptional digital experiences. My work is driven by a core belief that the best websites are born from the intersection of elegant design and robust functionality. I focus on creating intuitive, user-friendly interfaces that not only look beautiful but also provide a seamless and engaging journey for the end-user.

With a keen eye for detail and a commitment to writing clean, efficient code, I specialize in turning complex problems into elegant, scalable solutions. Whether it's a dynamic portfolio, a custom web application, or an e-commerce platform, I leverage modern technologies to ensure every project is not just functional but also future-proof and performant.

Ultimately, my mission is to serve as a technology partner for businesses and individuals, helping to transform your unique vision into a tangible online reality. I believe in collaborative creation and am always excited to take on new challenges. Let's build something amazing together.</p>
            </div>
            <div class="skills-list">
                <h3>My Skills</h3>
                <?php if (count($skills) > 0): ?>
                    <?php foreach ($skills as $skill): ?>
                        <div class="skill-item">
                            <h4 style="margin-bottom: 5px;"><?php echo htmlspecialchars($skill['skill_name']); ?> (<?php echo htmlspecialchars($skill['percentage']); ?>%)</h4>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo htmlspecialchars($skill['percentage']); ?>%;"></div>
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

<section id="services" class="services-section animated-section">
    <div class="services-intro">
        <h2>What I Do</h2>
        <p>A dedicated team of professionals to help you create captivating content.</p>
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
<section id="companies" class="companies-section animated-section">
    <h2>My Companies</h2>
    <div class="companies-grid">
        <?php if (count($companies) > 0): ?>
            <?php foreach ($companies as $company): ?>
                <div class="company-card">
                    <img src="assets/images/<?php echo htmlspecialchars($company['featured_image']); ?>" alt="<?php echo htmlspecialchars($company['title']); ?>" class="company-logo">
                    <h3><?php echo htmlspecialchars($company['title']); ?></h3>
                    <p><?php echo htmlspecialchars($company['description']); ?></p>
                    <a href="<?php echo htmlspecialchars($company['live_link']); ?>" target="_blank" class="company-link">Visit Website</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-data">No companies found. Please add some from the Admin Panel.</p>
        <?php endif; ?>
    </div>
</section>
<section id="projects" class="projects-section animated-section">
    <h2>My Latest Work</h2>
    <div class="projects-grid">
        <?php if (count($projects) > 0): ?>
            <?php foreach ($projects as $project): ?>
                <div class="project-card">
                    <img src="assets/images/<?php echo htmlspecialchars($project['featured_image']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>" class="project-image">
                    <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                    <p class="pdesc"><?php echo htmlspecialchars($project['description']); ?></p>
                    <a href="project_details.php?id=<?php echo $project['id']; ?>" class="project-link">View Details</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-data">No projects found. Please add some from the Admin Panel.</p>
        <?php endif; ?>
    </div>
</section>
<section id="testimonials" class="testimonials-section animated-section">
    <h2>What My Clients Say</h2>
    <div class="testimonials-grid">
        <?php if (count($testimonials) > 0): ?>
            <?php foreach ($testimonials as $testimonial): ?>
                <div class="testimonial-card">
                    <?php if (!empty($testimonial['client_photo'])): ?>
                        <img src="assets/images/<?php echo htmlspecialchars($testimonial['client_photo']); ?>" alt="<?php echo htmlspecialchars($testimonial['client_name']); ?>" class="client-photo">
                    <?php endif; ?>
                    <div class="star-rating">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <?php if ($i <= $testimonial['rating']): ?>
                                <i class="fas fa-star"></i>
                            <?php else: ?>
                                <i class="far fa-star"></i>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                    <p class="review-text"><?php echo htmlspecialchars($testimonial['review_text']); ?></p>
                    <h4 class="client-name"><?php echo htmlspecialchars($testimonial['client_name']); ?></h4>
                    <?php if (!empty($testimonial['client_company'])): ?>
                        <p class="client-company"><?php echo htmlspecialchars($testimonial['client_company']); ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-data">No testimonials found. Please add some from the Admin Panel.</p>
        <?php endif; ?>
    </div>
</section>
<section class="quote-section animated-section">
    <p>"I don't just write code. I translate a vision into a living architecture—a system built not on bricks, but on logic, designed to be as robust and scalable as the idea it supports."</p>
    <p class="quote-signature" style="text-decoration: wavy;">- Arham A. Siddiqui</p>
</section>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sections = document.querySelectorAll('.animated-section');
    const observerOptions = { root: null, rootMargin: '0px', threshold: 0.15 };
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                setTimeout(() => { entry.target.classList.add('is-visible'); }, 100);
            }
        });
    }, observerOptions);
    sections.forEach(section => { observer.observe(section); });
});
document.addEventListener('DOMContentLoaded', function() {
  const contents = document.querySelectorAll('.pdesc');
  const readMoreBtns = document.querySelectorAll('.project-link');
  contents.forEach((content, index) => {
    if(readMoreBtns[index]) {
      const fullText = content.textContent;
      const truncated = fullText.substring(0, 100) + '...';
      content.textContent = truncated;
      content.setAttribute('data-full', fullText);
      readMoreBtns[index].addEventListener('click', function(e) {
          e.preventDefault(); // Stop the link from navigating immediately
          // In a real scenario, you'd navigate to project_details.php
          // For a "Read More" toggle on the same page, the logic would be different.
          // This example keeps the link functional as intended.
          window.location.href = this.href;
      });
    }
  });
});
</script>
<?php include 'includes/footer.php'; ?>