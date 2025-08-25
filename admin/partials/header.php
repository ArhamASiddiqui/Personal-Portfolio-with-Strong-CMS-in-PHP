<?php
// Har admin page par session shuru karna aur login status check karna
session_start();
 
// Agar user logged in nahi hai, to login page par bhej dein
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
 
// Database connection file ko include karna
require_once '../includes/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - Portfolio CMS' : 'Admin - Portfolio CMS'; ?></title>
    
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
 
<div class="dashboard-container">
    <?php // Iske baad hum sidebar ko include karenge ?>