<?php
session_start();
 
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../admin/login.php");
    exit;
}
require_once '../includes/db.php';
 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
 
        // Logic for ADDING a new blog post
        if ($_POST['action'] === 'add') {
            $title = trim($_POST['title']);
            $content = trim($_POST['content']);
            $featured_image = '';
            $is_published = isset($_POST['is_published']) ? 1 : 0;
 
            // Generate a unique slug from the title
            $slug = strtolower(str_replace(' ', '-', $title));
 
            // Check for duplicate slug and append a unique ID if needed
            $sql_check = "SELECT id FROM blog_posts WHERE slug = ?";
            if ($stmt_check = mysqli_prepare($conn, $sql_check)) {
                mysqli_stmt_bind_param($stmt_check, "s", $slug);
                mysqli_stmt_execute($stmt_check);
                mysqli_stmt_store_result($stmt_check);
                if (mysqli_stmt_num_rows($stmt_check) > 0) {
                    $slug = $slug . '-' . uniqid();
                }
                mysqli_stmt_close($stmt_check);
            }
 
            if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === 0) {
                $target_dir = "../assets/images/";
                $file_name = uniqid() . '-' . basename($_FILES["featured_image"]["name"]);
                $target_file = $target_dir . $file_name;
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
 
                $check = getimagesize($_FILES["featured_image"]["tmp_name"]);
                if ($check !== false) {
                    if (move_uploaded_file($_FILES["featured_image"]["tmp_name"], $target_file)) {
                        $featured_image = $file_name;
                    }
                }
            }
 
            $sql = "INSERT INTO blog_posts (title, slug, content, featured_image, is_published) VALUES (?, ?, ?, ?, ?)";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "ssssi", $title, $slug, $content, $featured_image, $is_published);
                if (mysqli_stmt_execute($stmt)) {
                    header("location: /my_portfolio_cms/admin/manage_blogs.php");
                    exit;
                } else {
                    echo "Something went wrong. Please try again later.";
                }
                mysqli_stmt_close($stmt);
            }
 
        } 
        elseif ($_POST['action'] === 'edit' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $is_published = isset($_POST['is_published']) ? 1 : 0;
    $featured_image_update = '';
    $file_name = '';

    // Generate slug for update, ensuring it's unique
    $slug = strtolower(str_replace(' ', '-', $title));
    $sql_check = "SELECT id FROM blog_posts WHERE slug = ? AND id != ?";
    if ($stmt_check = mysqli_prepare($conn, $sql_check)) {
        mysqli_stmt_bind_param($stmt_check, "si", $slug, $id);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);
        if (mysqli_stmt_num_rows($stmt_check) > 0) {
            $slug = $slug . '-' . uniqid();
        }
        mysqli_stmt_close($stmt_check);
    }

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
    
    // Build the SQL query based on whether we have a new image
    if (!empty($featured_image_update)) {
        $sql = "UPDATE blog_posts SET title = ?, slug = ?, content = ?, is_published = ? $featured_image_update WHERE id = ?";
    } else {
        $sql = "UPDATE blog_posts SET title = ?, slug = ?, content = ?, is_published = ? WHERE id = ?";
    }
    
    if ($stmt = mysqli_prepare($conn, $sql)) {
        if (!empty($featured_image_update)) {
            // Bind parameters for UPDATE with image
            mysqli_stmt_bind_param($stmt, "sssisi", $title, $slug, $content, $is_published, $file_name, $id);
        } else {
            // Bind parameters for UPDATE without image
            mysqli_stmt_bind_param($stmt, "sssii", $title, $slug, $content, $is_published, $id);
        }
        
        if (mysqli_stmt_execute($stmt)) {
            header("location: /my_portfolio_cms/admin/manage_blogs.php?status=updated");
            exit;
        } else {
            echo "Error updating blog post: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}
    }
}
elseif (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
 
    $sql = "DELETE FROM blog_posts WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            header("location: /my_portfolio_cms/admin/manage_blogs.php");
            exit;
        }
        mysqli_stmt_close($stmt);
    }
}
 
mysqli_close($conn);
?>