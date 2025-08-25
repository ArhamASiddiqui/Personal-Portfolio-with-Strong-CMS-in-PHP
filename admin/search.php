<?php
// Page-specific variables
$page_title = "Search Results";
// We don't set $active_page because no sidebar link corresponds to search results

// Include header (handles session, db, etc.)
require_once 'partials/header.php';

$search_query = '';
$results = [];

if (isset($_GET['query']) && !empty(trim($_GET['query']))) {
    $search_query = trim($_GET['query']);
    
    // Use prepared statements to prevent SQL injection
    $sql = "
        -- Search in projects and companies
        (SELECT 
            id, 
            title, 
            'Project' as type 
        FROM projects 
        WHERE title LIKE ?)
        
        UNION
        
        -- Search in blog posts
        (SELECT 
            id, 
            title, 
            'Blog' as type 
        FROM blog_posts 
        WHERE title LIKE ?)
    ";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Bind the search term to both parts of the UNION query
        $param_query = "%" . $search_query . "%";
        mysqli_stmt_bind_param($stmt, "ss", $param_query, $param_query);
        
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            while($row = mysqli_fetch_assoc($result)) {
                $results[] = $row;
            }
        }
        mysqli_stmt_close($stmt);
    }
}

// Include the sidebar and main header
require_once 'partials/sidebar.php';
require_once 'partials/main_header.php';
?>

<div class="content-body">
    <div class="card" style="text-align: left; padding: 25px;">
        <h3 style="margin-top: 0; color: #7b68ee; font-size: 1.2em; font-weight: 600;">
            Search Results for: "<?php echo htmlspecialchars($search_query); ?>"
        </h3>

        <?php if (!empty($search_query)): ?>
            <?php if (count($results) > 0): ?>
                <p>Found <?php echo count($results); ?> result(s).</p>
                <ul style="list-style: none; padding-left: 0;">
                    <?php foreach ($results as $item): ?>
                        <li style="padding: 10px; border-bottom: 1px solid #f0f0f0;">
                            <strong><?php echo htmlspecialchars($item['type']); ?>:</strong>
                            <a href="<?php
                                // Generate the correct link based on the result type
                                switch ($item['type']) {
                                    case 'Project':
                                        echo 'manage_projects.php';
                                        break;
                                    case 'Blog':
                                        echo 'manage_blogs.php';
                                        break;
                                }
                            ?>?id=<?php echo $item['id']; ?>&action=edit">
                                <?php echo htmlspecialchars($item['title']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No results found for your query.</p>
            <?php endif; ?>
        <?php else: ?>
            <p>Please enter a search term in the header search bar.</p>
        <?php endif; ?>
    </div>
</div>

<?php
// Include the footer
require_once 'partials/footer.php';
?>