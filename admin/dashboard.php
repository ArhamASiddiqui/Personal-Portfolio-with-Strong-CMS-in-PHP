<?php
// Page-specific variables
$page_title = "Admin Dashboard";
$active_page = "dashboard"; // This is for the sidebar active state

// Include the header
require_once 'partials/header.php';

// Include the sidebar
require_once 'partials/sidebar.php';
?>

<?php require_once 'partials/main_header.php'; ?>

<div class="content-body">
    <div class="card-grid">
        <div class="card">
            <h3>Total Projects</h3>
            <p class="card-value" id="totalProjects">-</p>
        </div>
        <div class="card">
            <h3>Total Messages</h3>
            <p class="card-value" id="totalMessages">-</p>
        </div>
        <div class="card">
            <h3>Total Skills</h3>
            <p class="card-value" id="totalSkills">-</p>
        </div>
    </div>

    <div class="chart-section-grid">
        <div class="chart-card">
            <h3>Project Types</h3>
            <canvas id="projectTypesChart"></canvas>
        </div>
        <div class="chart-card">
            <h3>Messages Over Time</h3>
            <canvas id="messagesChart"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="assets/js/dashboard.js"></script>

<?php
// Include the footer
require_once 'partials/footer.php';
?>