<?php
// Includes the database connection file
require_once 'includes/db.php';

// Fetch all blog posts from the database
$blogs = [];
$sql = "SELECT * FROM blog_posts WHERE is_published = 1 ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $blogs[] = $row;
    }
}
?>
<?php include 'includes/header.php'; ?>

<style>
.blog-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 30px;
    max-width: 1200px;
    margin: 0 auto;
}

.blog-card {
    background-color: #1a1c3c;
    border-radius: 10px;
    padding: 30px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    text-align: left;
    transition: transform 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.blog-card:hover {
    transform: translateY(-5px);
}

.blog-image {
    width: 100%;
    height: 250px;
    object-fit: cover;
    border-radius: 5px;
    margin-bottom: 15px;
}

.blog-title {
    font-size: 1.5em;
    color: #61dafb;
    margin: 15px 0 10px;
    line-height: 1.3;
}

.blog-excerpt {
    color: #b0b0b0;
    font-size: 0.95em;
    line-height: 1.6;
    margin-bottom: 20px;
    flex-grow: 1;
    overflow: hidden;
}

.blog-excerpt.truncated {
    display: -webkit-box;
    -webkit-line-clamp: 4;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.read-more {
    display: inline-block;
    margin-top: 15px;
    color: #61dafb;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s ease;
}

.read-more:hover {
    color: #ffffff;
    text-decoration: underline;
}

.no-blogs {
    color: #b0b0b0;
    font-size: 1.2em;
    text-align: center;
    grid-column: 1 / -1;
}
</style>

<section style="padding: 80px 50px; background-color: #0b0c20; color: #fff; text-align: center;">
    <h2 style="font-size: 2.5em; font-weight: 700; margin-bottom: 50px; color: #61dafb;">My Blog</h2>
    <div class="blog-grid">
        <?php if (count($blogs) > 0): ?>
            <?php foreach ($blogs as $blog): ?>
                <div class="blog-card">
                    <?php if (!empty($blog['featured_image'])): ?>
                        <img src="assets/images/<?php echo htmlspecialchars($blog['featured_image']); ?>" alt="<?php echo htmlspecialchars($blog['title']); ?>" class="blog-image">
                    <?php endif; ?>
                    <h3 class="blog-title"><?php echo htmlspecialchars($blog['title']); ?></h3>
                    <div class="blog-excerpt truncated">
                        <?php 
                        // Clean content by removing HTML tags and extra whitespace
                        $clean_content = strip_tags($blog['content']);
                        $clean_content = preg_replace('/\s+/', ' ', $clean_content);
                        echo htmlspecialchars($clean_content); 
                        ?>
                    </div>
                    <a href="blog_post.php?slug=<?php echo htmlspecialchars($blog['slug']); ?>" class="read-more">Read More</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-blogs">No blog posts found. Please add some from the Admin Panel.</p>
        <?php endif; ?>
    </div>
</section>
<?php include 'includes/footer.php'; ?>