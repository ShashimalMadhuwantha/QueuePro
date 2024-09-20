<?php

session_start();

// Check if the user is logged in and is user ID 'A001'
if (!isset($_SESSION['UserID'])) {
    // Redirect to login page if not logged in
    header("Location: Adminlogout.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404</title>
    <link rel="title icon" href="../pictures/logo.png">
    <link rel="stylesheet" type="text/css" href="../css/errorstyle.css">
    <link rel="stylesheet" type="text/css" href="../bootstraplibraries/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Access Denied</h1>
        <?php
        if (isset($_SESSION['error_message'])) {
            echo "<p style='color: red;'>" . $_SESSION['error_message'] . "</p>";
            unset($_SESSION['error_message']);
        } else {
            echo "<p style='color: red;'>You Do not have access to This!.</p>";
        }
        ?>
        <a href="AdminPanel.php">Go to Admin Panel</a>
    </div>
</body>
</html>
