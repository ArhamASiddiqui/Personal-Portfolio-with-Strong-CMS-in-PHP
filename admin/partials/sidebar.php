<aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo-circle">L</div>
        <h3>Diamondz Dev</h3>
    </div>
    <ul class="sidebar-nav">
        <li><a href="dashboard.php" class="<?php echo ($active_page === 'dashboard') ? 'active' : ''; ?>"><i class="fas fa-home"></i>Dashboard</a></li>
        <li><a href="manage_projects.php" class="<?php echo ($active_page === 'projects') ? 'active' : ''; ?>"><i class="fas fa-folder"></i>Projects</a></li>
        <li><a href="manage_companies.php" class="<?php echo ($active_page === 'companies') ? 'active' : ''; ?>"><i class="fas fa-globe"></i>Companies</a></li>
        <li><a href="manage_blogs.php" class="<?php echo ($active_page === 'blogs') ? 'active' : ''; ?>"><i class="fas fa-blog"></i>Blog Posts</a></li>
        <li><a href="manage_skills.php" class="<?php echo ($active_page === 'skills') ? 'active' : ''; ?>"><i class="fas fa-code"></i>Skills</a></li>
        <li><a href="manage_services.php" class="<?php echo ($active_page === 'services') ? 'active' : ''; ?>"><i class="fas fa-tools"></i>Services</a></li>
        <li><a href="manage_testimonials.php" class="<?php echo ($active_page === 'testimonials') ? 'active' : ''; ?>"><i class="fas fa-star"></i>Testimonials</a></li>
        <li><a href="manage_messages.php" class="<?php echo ($active_page === 'messages') ? 'active' : ''; ?>"><i class="fas fa-envelope"></i>Messages</a></li>
        <li><a href="manage_users.php" class="<?php echo ($active_page === 'users') ? 'active' : ''; ?>"><i class="fas fa-users-cog"></i>Users</a></li>
        <li><a href="manage_settings.php" class="<?php echo ($active_page === 'settings') ? 'active' : ''; ?>"><i class="fas fa-cog"></i>Settings</a></li>
        <li><a href="manage_profile.php" class="<?php echo ($active_page === 'profile') ? 'active' : ''; ?>"><i class="fas fa-user-circle"></i>Profile</a></li>
    </ul>
    <div class="sidebar-footer">
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
    </div>
</aside>

<main class="main-content">
    <?php // Iske baad page ka header aur content aayega ?>