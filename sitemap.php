<?php
header('Content-Type: application/xml');
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

// Add static pages
$pages = [
    '/',
    '/about.php',
    '/services.php',
    '/blog.php',
    '/contact.php'
];

foreach ($pages as $page) {
    echo '<url>';
    echo '<loc>http://localhost/my_portfolio_cms' . $page . '</loc>';
    echo '</url>';
}

// Fetch dynamic blog posts
require_once 'includes/db.php';
$sql = "SELECT slug FROM blog_posts WHERE is_published = 1";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<url>';
        echo '<loc>http://localhost/my_portfolio_cms/blog_post.php?slug=' . htmlspecialchars($row['slug']) . '</loc>';
        echo '</url>';
    }
}
mysqli_close($conn);

echo '</urlset>';
?>