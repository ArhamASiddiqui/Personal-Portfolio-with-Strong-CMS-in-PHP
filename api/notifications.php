<?php
session_start();
require_once '../includes/db.php';

// Ensure the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$response = [];

// Handle marking notifications as read
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['action']) && $data['action'] === 'mark_read') {
        if(isset($data['id']) && is_numeric($data['id'])) {
            $id_to_mark = $data['id'];
            $sql = "UPDATE notifications SET is_read = 1 WHERE id = ?";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "i", $id_to_mark);
                mysqli_stmt_execute($stmt);
                $response['status'] = 'success';
            }
        } elseif (isset($data['id']) && $data['id'] === 'all') {
             $sql = "UPDATE notifications SET is_read = 1 WHERE is_read = 0";
            if (mysqli_query($conn, $sql)) {
                $response['status'] = 'success';
            }
        }
    }

// Handle fetching unread notifications
} else {
    $sql = "SELECT * FROM notifications WHERE is_read = 0 ORDER BY created_at DESC";
    $result = mysqli_query($conn, $sql);
    $notifications = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $notifications[] = $row;
        }
    }
    $response['notifications'] = $notifications;
    $response['unread_count'] = count($notifications);
}


header('Content-Type: application/json');
echo json_encode($response);
mysqli_close($conn);

?>