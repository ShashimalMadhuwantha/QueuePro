<?php


session_start();

// Check if the user is logged in and is user ID 'A001'
if (!isset($_SESSION['UserID'])) {
    // Redirect to login page if not logged in
    header("Location: ../login.php");
    exit();
}


//Delete session
session_destroy();

//Redirect to login.php
header("Location: ../login.php");


?>