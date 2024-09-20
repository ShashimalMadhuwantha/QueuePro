<?php
session_start();
require '../connections/connection.php';
// Check if the user is logged in and is user ID 'A001'
if (!isset($_SESSION['UserID'])) 
{
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
    <title>Add Doctors</title>
    <link rel="title icon" href="../pictures/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/navstyle.css">
    <link rel="stylesheet" type="text/css" href="../bootstraplibraries/css/bootstrap.min.css">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</head>
<body>
    <!-- nav bar response -->
    <script>
        function toggleNav() {
            var nav = document.querySelector('.nav-items ul');
            nav.classList.toggle('show');
        }
    </script>
    
<div class="nav">
    <div class="head">
        <h2>Add Doctors</h2>
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
    <br><br><br>
    <div class="form-container">
        <form name="frmadddoctors" method="post" action="#" enctype="multipart/form-data">
            <table>
                <tr style="display: none;">
                    <td class="names">DID</td>
                    <td><input type="text" name="txtdid" value="<?php echo isset($next_id) ? $next_id : ''; ?>" readonly></td>
                </tr>
                <tr>
                    <td class="names">First Name</td>
                    <td><input type="text" name="txtfname" required></td>
                </tr>
                <tr>
                    <td class="names">Last Name</td>
                    <td><input type="text" name="txtlname" required></td>
                </tr>
                <tr>
                    <td class="names">Password</td>
                    <td><input type="text" name="txtpass" required></td>
                </tr>
                <tr>
                    <td class="names">Contact No</td>
                    <td><input type="text" name="txtcno" required></td>
                </tr>
                <tr>
                    <td class="names">Specialization</td>
                    <td><input type="text" name="txtspe" required></td>
                </tr>
                <tr>
                    <td class="names">Email</td>
                    <td><input type="email" name="txtemail" required></td>
                </tr>
                <tr>
                    <td class="names">Image</td>
                    <td><input type="file" name="filephoto" required></td>
                </tr>
                
                <tr>
                    <td></td>
                    <td ><input type="submit" name="btnsubmit" class="btn btn-success"></td>
                </tr>
            </table>
        </form>
    </div>
</body>
</html>


<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uname = isset($_POST["txtuname"]) ? $_POST["txtuname"] : '';
    $pass = isset($_POST["txtpass"]) ? $_POST["txtpass"] : '';
    $fname = isset($_POST["txtfname"]) ? $_POST["txtfname"] : '';
    $lname = isset($_POST["txtlname"]) ? $_POST["txtlname"] : '';
    $cno = isset($_POST["txtcno"]) ? $_POST["txtcno"] : '';
    $spe = isset($_POST["txtspe"]) ? $_POST["txtspe"] : '';
    $email = isset($_POST["txtemail"]) ? $_POST["txtemail"] : '';

    $photo = '';
    if(isset($_FILES["filephoto"]) && $_FILES["filephoto"]["error"] == 0) {
        $target_dir = "../profile/";
        $photo = $target_dir . basename($_FILES["filephoto"]["name"]);
        move_uploaded_file($_FILES["filephoto"]["tmp_name"], $photo);
    }

    if (!empty($fname) && !empty($pass)) 
    {
        

        if (!$con) 
        {
            die("Connection failed: " . mysqli_connect_error());
        }

        // Query to get the maximum Doctor ID
        $query_max_id = "SELECT MAX(SUBSTRING(DID, 2)) AS max_id FROM doctorlogin WHERE DID LIKE 'D%'";
        $result = mysqli_query($con, $query_max_id);
        $row = mysqli_fetch_assoc($result);
        $max_id = $row['max_id'];

        // Incrementing doctor ID
        if ($max_id === null) 
        {
            // If no records exist, start with D001
            $next_id = 'D001';
        } 
        else 
        {
            // Increment the last ID found
            $next_id = 'D' . str_pad((int)$max_id + 1, 3, '0', STR_PAD_LEFT);
        }

        // Insert query with auto-assigned Doctor ID
        $query = "INSERT INTO doctorlogin (DID, Password, AID, First_Name, Last_Name, Contact_No, Specialization, Email, Photo) VALUES ('$next_id', '$pass', '$_SESSION[UserID]', '$fname', '$lname', '$cno', '$spe', '$email', '$photo')";

        $return = mysqli_query($con, $query);

        if ($return) 
        {
            echo "Record inserted successfully";
        } 
        else 
        {
            echo "Error: " . mysqli_error($con);
        }

        mysqli_close($con);
    } 
    else 
    {
        echo "Username and Password are required fields.";
    }
}
?>