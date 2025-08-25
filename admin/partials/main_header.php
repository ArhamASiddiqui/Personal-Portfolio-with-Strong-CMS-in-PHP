<header class="main-header">
    <div class="header-left">
        <h2><?php echo htmlspecialchars($page_title ?? 'Dashboard'); ?></h2>
    </div>
    <div class="header-right">
        <div class="search-container">
            <span id="search-icon"><i class="fas fa-search"></i></span>
            <form action="search.php" method="GET" class="search-bar" id="search-bar">
                <input type="text" name="query" placeholder="Search...">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>

        <div class="notification-container">
            <i class="fas fa-bell" id="notification-bell"></i>
            <span class="notification-badge" id="notification-count" style="display: none;">0</span>
            <div class="notifications-dropdown" id="notifications-dropdown">
                <div class="dropdown-header">
                    <span>Notifications</span>
                    <a href="#" id="mark-all-read">Mark all as read</a>
                </div>
                <div class="dropdown-body" id="notification-list">
                    </div>
            </div>
        </div>
        <div class="user-profile">
            <img src="assets/images/<?php echo htmlspecialchars($_SESSION['profile_photo'] ?? 'user.jpg'); ?>?v=<?php echo time(); ?>" alt="User" class="profile-pic">
        </div>
    </div>
</header>