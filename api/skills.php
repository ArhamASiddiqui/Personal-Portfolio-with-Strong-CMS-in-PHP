<?php
session_start();
 
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../admin/login.php");
    exit;
}
require_once '../includes/db.php';
 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
 
        // Handles adding a new skill
        if ($_POST['action'] === 'add') {
            $skill_name = trim($_POST['skill_name']);
            $percentage = trim($_POST['percentage']); // FIXED: Was 'proficiency'

            // FIXED: SQL now uses the correct 'percentage' column
            $sql = "INSERT INTO skills (skill_name, percentage) VALUES (?, ?)";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                // "si" means we are binding one String and one Integer
                mysqli_stmt_bind_param($stmt, "si", $skill_name, $percentage);
                if (mysqli_stmt_execute($stmt)) {
                    header("location: /my_portfolio_cms/admin/manage_skills.php?status=added");
                    exit;
                }
                mysqli_stmt_close($stmt);
            }
 
        // Handles editing an existing skill
        } elseif ($_POST['action'] === 'edit' && isset($_POST['id'])) {
            $id = $_POST['id'];
            $skill_name = trim($_POST['skill_name']);
            $percentage = trim($_POST['percentage']); // FIXED: Was 'proficiency'
 
            // FIXED: SQL now uses the correct 'percentage' column
            $sql = "UPDATE skills SET skill_name = ?, percentage = ? WHERE id = ?";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                // "sii" is for String, Integer, Integer ($skill_name, $percentage, $id)
                mysqli_stmt_bind_param($stmt, "sii", $skill_name, $percentage, $id);
                if (mysqli_stmt_execute($stmt)) {
                    header("location: /my_portfolio_cms/admin/manage_skills.php?status=updated");
                    exit;
                }
                mysqli_stmt_close($stmt);
            }
 
        }
    }
}
 
mysqli_close($conn);
?>