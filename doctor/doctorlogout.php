<?php
session_start();

if (!isset($_SESSION['UserDID'])) {
    // Redirect to login page if not logged in
    header("Location: ../login.php");
    exit();
}


//Delete session
session_destroy();

//Redirect to login.php
header("Location: ../login.php");


?>