<?php
session_start();

require '../connections/connection.php';

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
    <title>Add Admins</title>
    <link rel="title icon" href="../pictures/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/navstyle.css">
    <link rel="stylesheet" type="text/css" href="../bootstraplibraries/css/bootstrap.min.css">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

    <!-- JavaScript for toggleNav function -->
    <script>
        function toggleNav() {
            var nav = document.querySelector('.nav-items ul');
            nav.classList.toggle('show');
        }
    </script>
</head>
<body>

<!-- Navigation bar -->
<div class="nav">
    <div class="head">
        <h2>Add Admins</h2>
    </div>
    <div class="nav-toggle" onclick="toggleNav()">
        <ion-icon name="menu-outline"></ion-icon>
    </div>
    <div class="nav-items">
        <ul>
            <li class="nav-item"><a class="nav-link" href="AdminPanel.php">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="addadmins.php">Add Admins</a></li>
            <li class="nav-item"><a class="nav-link" href="adddoctors.php">Add Doctors</a></li>
            <li class="nav-item"><a class="nav-link" href="addclinics.php">Add Clinics</a></li>
            <li class="nav-item"><a class="nav-link" href="assingschedule.php">Assign Schedule</a></li>
            <li class="nav-item"><a class="nav-link" href="managelogins.php">Manage Logins</a></li>
            <li class="nav-item"><a class="nav-link" href="../qr/qrscanner.php">Scanner</a></li>
            <li class="nav-item"><a class="nav-link" href="Adminlogout.php">Logout</a></li>
        </ul>
    </div>
</div>
<br>
<br>
<br>
<!-- Form to add admins -->
<div class="form-container">
    <form name="frmaddadmins" method="post" action="#">
        <table>
            
            <tr>
                <td class="names">Username</td>
                <td><input type="text" name="txtuname" required></td>
            </tr>
            <tr>
                <td class="names">Password</td>
                <td><input type="text" name="txtpass" required></td>
            </tr>
            <tr>
                <td></td>
                <td><input type="submit" name="btnsubmit" class="btn btn-success"></td>
            </tr>
        </table>
    </form>
</div>
</body>
</html>

<?php

// Initialize $next_id variable
$next_id = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uname = isset($_POST["txtuname"]) ? $_POST["txtuname"] : '';
    $pass = isset($_POST["txtpass"]) ? $_POST["txtpass"] : '';

    if (!empty($uname) && !empty($pass)) {
        

        if (!$con) {
            die("Connection failed: " . mysqli_connect_error());
        }

        // Query to get the maximum Admin ID
        $query_max_id = "SELECT MAX(SUBSTRING(AID, 2)) AS max_id FROM adminlogin WHERE AID LIKE 'A%'";
        $result = mysqli_query($con, $query_max_id);
        $row = mysqli_fetch_assoc($result);
        $max_id = $row['max_id'];

        // Incrementing Admin ID
        if ($max_id === null) {
            // If no records exist, start with A001
            $next_id = 'A001';
        } else {
            // Increment the last ID found
            $next_id = 'A' . str_pad((int)$max_id + 1, 3, '0', STR_PAD_LEFT);
        }

        // Insert query with auto-assigned Admin ID
        $query = "INSERT INTO adminlogin (AID, Username, Password) VALUES ('$next_id', '$uname', '$pass')";

        $return = mysqli_query($con, $query);
           
         
         if ($return) 
        {
            echo "Record inserted successfully";
        } 
        else 
        {
            echo "Error: " . mysqli_error($con);
        }
    
    } else {
        echo "<script>alert('Username and Password Required!')</script>";
    }
}

// Database connection and query for Admin ID generation


if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}


mysqli_close($con);

?>
