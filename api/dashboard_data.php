<?php
// Includes the database connection file. Path is relative from the api folder.
require_once '../includes/db.php';

// Set the content type to JSON
header('Content-Type: application/json');

// Function to get the count of rows from a table
function getCount($conn, $table) {
    $sql = "SELECT COUNT(*) FROM {$table}";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        return mysqli_fetch_row($result)[0];
    }
    return 0;
}

// Function to get data for a chart
function getChartData($conn, $table, $column) {
    $data = [];
    $sql = "SELECT {$column}, COUNT(*) as count FROM {$table} GROUP BY {$column}";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }
    return $data;
}

// Get all the necessary data
$data = [
    'totalProjects' => getCount($conn, 'projects'),
    'totalMessages' => getCount($conn, 'contact_messages'),
    'totalSkills' => getCount($conn, 'skills'),
    'projectTypes' => getChartData($conn, 'projects', 'type'),
];

// Return data as a JSON object
echo json_encode($data);

// Close the connection
mysqli_close($conn);

?>