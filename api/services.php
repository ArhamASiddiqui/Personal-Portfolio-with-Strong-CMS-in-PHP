<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../admin/login.php");
    exit;
}
require_once '../includes/db.php';
 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $service_title = trim($_POST['service_title']);
            $description = trim($_POST['description']);
 
            $sql = "INSERT INTO services (service_title, description) VALUES (?, ?)";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "ss", $service_title, $description);
                if (mysqli_stmt_execute($stmt)) {
                    header("location: /my_portfolio_cms/admin/manage_services.php");
                    exit;
                }
                mysqli_stmt_close($stmt);
            }
        } elseif ($_POST['action'] === 'edit' && isset($_POST['id'])) {
            $id = $_POST['id'];
            $service_title = trim($_POST['service_title']);
            $description = trim($_POST['description']);
 
            $sql = "UPDATE services SET service_title = ?, description = ? WHERE id = ?";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "ssi", $service_title, $description, $id);
                if (mysqli_stmt_execute($stmt)) {
                    header("location: /my_portfolio_cms/admin/manage_services.php");
                    exit;
                }
                mysqli_stmt_close($stmt);
            }
        }
    }
} elseif (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM services WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            header("location: /my_portfolio_cms/admin/manage_services.php");
            exit;
        }
        mysqli_stmt_close($stmt);
    }
}
mysqli_close($conn);
?>