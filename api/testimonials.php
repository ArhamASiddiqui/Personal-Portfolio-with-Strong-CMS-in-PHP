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
            // This 'add' block was already working correctly
            $client_name = trim($_POST['client_name']);
            $client_company = trim($_POST['client_company']);
            $review_text = trim($_POST['review_text']);
            $rating = (int)($_POST['rating'] ?? 5);
            $client_photo = '';
 
            if (isset($_FILES['client_photo']) && $_FILES['client_photo']['error'] === 0) {
                $target_dir = "../assets/images/";
                $file_name = uniqid() . '-' . basename($_FILES["client_photo"]["name"]);
                $target_file = $target_dir . $file_name;
                if (move_uploaded_file($_FILES["client_photo"]["tmp_name"], $target_file)) {
                    $client_photo = $file_name;
                }
            }
 
            $sql = "INSERT INTO testimonials (client_name, client_company, review_text, rating, client_photo) VALUES (?, ?, ?, ?, ?)";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "sssis", $client_name, $client_company, $review_text, $rating, $client_photo);
                if (mysqli_stmt_execute($stmt)) {
                    header("location: /my_portfolio_cms/admin/manage_testimonials.php?status=added");
                    exit;
                }
                mysqli_stmt_close($stmt);
            }

        } elseif ($_POST['action'] === 'edit' && isset($_POST['id'])) {
            // This 'edit' block is the one we are fixing to be more compatible
            $id = $_POST['id'];
            $client_name = trim($_POST['client_name']);
            $client_company = trim($_POST['client_company']);
            $review_text = trim($_POST['review_text']);
            $rating = (int)($_POST['rating'] ?? 5);
            
            $sql = "UPDATE testimonials SET client_name = ?, client_company = ?, review_text = ?, rating = ? WHERE id = ?";
            $types = "sssii";
            $params = [$client_name, $client_company, $review_text, $rating, $id];

            if (isset($_FILES['client_photo']) && $_FILES['client_photo']['error'] === 0) {
                $target_dir = "../assets/images/";
                $file_name = uniqid() . '-' . basename($_FILES["client_photo"]["name"]);
                $target_file = $target_dir . $file_name;
                if (move_uploaded_file($_FILES["client_photo"]["tmp_name"], $target_file)) {
                    $sql = "UPDATE testimonials SET client_name = ?, client_company = ?, review_text = ?, rating = ?, client_photo = ? WHERE id = ?";
                    $types = "sssisi";
                    $params = [$client_name, $client_company, $review_text, $rating, $file_name, $id];
                }
            }
 
            if ($stmt = mysqli_prepare($conn, $sql)) {
                // This is the compatible way to bind a dynamic number of parameters
                $bind_params = [ $types ];
                foreach ($params as $key => $value) {
                    $bind_params[] = &$params[$key];
                }
                call_user_func_array([$stmt, 'bind_param'], $bind_params);

                if (mysqli_stmt_execute($stmt)) {
                    header("location: /my_portfolio_cms/admin/manage_testimonials.php?status=updated");
                    exit;
                }
                mysqli_stmt_close($stmt);
            }
        }
    }
}
mysqli_close($conn);
?>