<?php
require_once 'includes/db.php';

$post = null;
if (isset($_GET['slug'])) {
    $slug = $_GET['slug'];
    $sql = "SELECT * FROM blog_posts WHERE slug = ? AND is_published = 1";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $slug);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $post = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
    }
}
?>
<?php include 'includes/header.php'; ?>

<style>
.blog-post-container {
    max-width: 900px;
    margin: 0 auto;
    background-color: #1a1c3c;
    border-radius: 10px;
    padding: 40px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.blog-post-title {
    font-size: 2.5em;
    color: #61dafb;
    margin-bottom: 20px;
    line-height: 1.3;
}

.blog-post-meta {
    color: #b0b0b0;
    font-size: 0.95em;
    margin-bottom: 30px;
    padding-bottom: 15px;
    border-bottom: 1px solid #2a2c4c;
}

.blog-post-image {
    width: 100%;
    height: auto;
    border-radius: 5px;
    margin-bottom: 30px;
}

.blog-post-content {
    color: #e5e5e5;
    font-size: 1.1em;
    line-height: 1.8;
    overflow-wrap: break-word;
    word-wrap: break-word;
}

.blog-post-content p {
    margin-bottom: 1.5em;
}

.blog-post-content img {
    max-width: 100%;
    height: auto;
    border-radius: 5px;
    margin: 20px 0;
}

.blog-not-found {
    text-align: center;
    color: #b0b0b0;
    font-size: 1.2em;
    padding: 60px 20px;
}
</style>

<section style="padding: 80px 50px; background-color: #0b0c20; color: #fff; text-align: left;">
    <?php if ($post): ?>
        <div class="blog-post-container">
            <h1 class="blog-post-title"><?php echo htmlspecialchars($post['title']); ?></h1>
            <div class="blog-post-meta">
                Published on: <?php echo htmlspecialchars(date('F j, Y', strtotime($post['created_at']))); ?> by Arham A. Siddiqui
            </div>

            <?php if (!empty($post['featured_image'])): ?>
                <img src="assets/images/<?php echo htmlspecialchars($post['featured_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="blog-post-image">
            <?php endif; ?>

            <div class="blog-post-content">
                <?php 
                // Format content with proper paragraph breaks
                $content = htmlspecialchars($post['content']);
                $content = nl2br($content);
                $content = preg_replace('/(https?:\/\/[^\s]+)/', '<a href="$1" target="_blank" style="color: #61dafb;">$1</a>', $content);
                echo $content; 
                ?>
            </div>
        </div>
    <?php else: ?>
        <p class="blog-not-found">Blog post not found.</p>
    <?php endif; ?>
</section>
<?php include 'includes/footer.php'; ?>