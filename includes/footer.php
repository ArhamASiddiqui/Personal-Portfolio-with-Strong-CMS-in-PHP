<footer class="main-footer">
    <div class="footer-content">
        <p>
    Designed &amp; Developed by Arham A. Siddiqui &nbsp;&bull;&nbsp; Powered by Diamondz Developers
    <br>
    &copy; <?php echo date("Y"); ?> Diamondz Group. All Rights Reserved.
</p>
        <div class="social-links">
            <a href="<?php echo htmlspecialchars($settings['social_twitter'] ?? '#'); ?>" target="_blank"><i class="fab fa-twitter"></i></a>
            <a href="<?php echo htmlspecialchars($settings['social_linkedin'] ?? '#'); ?>" target="_blank"><i class="fab fa-linkedin"></i></a>
            <a href="<?php echo htmlspecialchars($settings['social_github'] ?? '#'); ?>" target="_blank"><i class="fab fa-github"></i></a>
        </div>
        <a href="/my_portfolio_cms/admin/dashboard.php" class="admin-link">Admin Panel</a>
    </div>
</footer>

</body>
</html>