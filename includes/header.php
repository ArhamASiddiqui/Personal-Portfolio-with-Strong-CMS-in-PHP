<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Professional Portfolio</title>
    <meta name="description" content="<?php echo htmlspecialchars($settings['meta_description'] ?? 'A professional portfolio website.'); ?>">
    <meta name="keywords" content="portfolio, web development, projects, skills">
    <?php clearstatcache(); ?>
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo filemtime('assets/css/style.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<nav class="main-nav">
    <div class="logo">
        <a href="/my_portfolio_cms/index.php">Arham A. Siddiqui</a>
    </div>
    <ul class="nav-links">
        <li><a href="/my_portfolio_cms/index.php">Home</a></li>
        <li><a href="/my_portfolio_cms/index.php#about">About</a></li>
        <li><a href="/my_portfolio_cms/index.php#services">Services</a></li>
        <li><a href="/my_portfolio_cms/index.php#companies">Companies</a></li>
        <li><a href="/my_portfolio_cms/index.php#projects">Projects</a></li>
        <li><a href="/my_portfolio_cms/index.php#testimonials">Testimonials</a></li>
        <li><a href="/my_portfolio_cms/blog.php">Blog</a></li>
        <li><a href="/my_portfolio_cms/contact.php">Contact</a></li>
    </ul>
</nav>

