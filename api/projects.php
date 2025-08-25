<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../admin/login.php");
    exit;
}
require_once '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $live_link = trim($_POST['live_link']);
        $source_link = trim($_POST['source_link']);
        $technologies = trim($_POST['technologies']);
        $type = trim($_POST['type']); // Get the type of the project
        $featured_image = '';

        // Check which page to redirect to
        $redirect_page = ($type == 'website') ? 'manage_companies.php' : 'manage_projects.php';

        // Logic for ADDING a new project
        if ($_POST['action'] === 'add') {
            // Handle image upload
            if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === 0) {
                $target_dir = "../assets/images/";
                $file_name = uniqid() . '-' . basename($_FILES["featured_image"]["name"]);
                $target_file = $target_dir . $file_name;
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                $check = getimagesize($_FILES["featured_image"]["tmp_name"]);
                if ($check !== false) {
                    if (!file_exists($target_file) && $_FILES["featured_image"]["size"] <= 5000000 && ($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif")) {
                        if (move_uploaded_file($_FILES["featured_image"]["tmp_name"], $target_file)) {
                            $featured_image = $file_name;
                        }
                    }
                }
            }

            $sql = "INSERT INTO projects (title, description, live_link, source_link, technologies, type, featured_image) VALUES (?, ?, ?, ?, ?, ?, ?)";

            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "sssssss", $title, $description, $live_link, $source_link, $technologies, $type, $featured_image);

                if (mysqli_stmt_execute($stmt)) {
                    header("location: /my_portfolio_cms/admin/{$redirect_page}?status=success");
                    exit;
                } else {
                    echo "Error adding project: " . mysqli_error($conn);
                }
                mysqli_stmt_close($stmt);
            }
        }
        // Logic for UPDATING an existing project
        elseif ($_POST['action'] === 'edit' && isset($_POST['id'])) {
            $id = $_POST['id'];
            $featured_image_update = '';

            // Handle image upload (if a new image is provided)
            if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === 0) {
                $target_dir = "../assets/images/";
                $file_name = uniqid() . '-' . basename($_FILES["featured_image"]["name"]);
                $target_file = $target_dir . $file_name;
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                if (move_uploaded_file($_FILES["featured_image"]["tmp_name"], $target_file)) {
                    $featured_image_update = ", featured_image = ?";
                }
            }

            $sql = "UPDATE projects SET title = ?, description = ?, live_link = ?, source_link = ?, technologies = ?, type = ? {$featured_image_update} WHERE id = ?";

            if ($stmt = mysqli_prepare($conn, $sql)) {
                if (!empty($featured_image_update)) {
                    mysqli_stmt_bind_param($stmt, "sssssssi", $title, $description, $live_link, $source_link, $technologies, $type, $file_name, $id);
                } else {
                    mysqli_stmt_bind_param($stmt, "ssssssi", $title, $description, $live_link, $source_link, $technologies, $type, $id);
                }

                if (mysqli_stmt_execute($stmt)) {
                    header("location: /my_portfolio_cms/admin/{$redirect_page}?status=updated");
                    exit;
                } else {
                    echo "Error updating project: " . mysqli_error($conn);
                }
                mysqli_stmt_close($stmt);
            }
        }
    }
} elseif (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM projects WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            // Corrected redirect for delete action
            $redirect_page_after_delete = (isset($_GET['type']) && $_GET['type'] == 'website') ? 'manage_companies.php' : 'manage_projects.php';
            header("location: /my_portfolio_cms/admin/{$redirect_page_after_delete}?status=deleted");
            exit;
        }
        mysqli_stmt_close($stmt);
    }
}

mysqli_close($conn);
?>