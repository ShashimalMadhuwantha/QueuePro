<?php
session_start();
require '../connections/connection.php';

// Check if the user is logged in and is user ID 'A001'
if (!isset($_SESSION['UserID'])) {
    // Redirect to login page if not logged in
    header("Location: Adminlogout.php");
    exit();
} elseif ($_SESSION['UserID'] !== 'A001' && $_SESSION['UserID'] !== 'A002' && $_SESSION['UserID'] !== 'A003') {
    $_SESSION['error_message'] = "Access Denied";
    header("Location: error.php");
    exit();
}
// Refresh page on certain button clicks
if (isset($_POST["btnupdate"]) || isset($_POST["btndelete"]) || isset($_POST["btndeleted"]) || isset($_POST["btnupdated"]) || isset($_POST["btnblock"]) || isset($_POST["btnunblock"]) || isset($_POST["btnatupdate"]) || isset($_POST["btnlga"])) 
{
    header("Refresh:0");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Logins</title>
    <link rel="title icon" href="../pictures/logo.png">
        <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="../css/navstyle.css">
        <link rel="stylesheet" type="text/css" href="../css/tablestyle.css">
    <link rel="stylesheet" type="text/css" href="../bootstraplibraries/css/bootstrap.min.css">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

</head>
<body>
    <!-- nav bar response -->
    <script>
        function toggleNav() 
        {
            var nav = document.querySelector('.nav-items ul');
            nav.classList.toggle('show');
        }
    </script>

    <div class="nav">
    <div class="head">
        <h2>Manage Logins</h2>
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
    <?php


         

        if (!$con) {
            die("Connection failed: " . mysqli_connect_error());
        }

        $details = "SELECT * FROM adminlogin where AID != 'A001'";
        $result = mysqli_query($con, $details);
        


        if ($result) {
            echo "<table id='tb1'>";
            echo "<tr>";
            echo "<th colspan='5'>Admin Data</th>";
            echo "</tr>";
            echo "<tr>";
            echo "<th>Admin Id</th>";
            echo "<th>User Name</th>";
            echo "<th>Password</th>";
            echo "<th>Login Attempts</th>";
            echo "<th>Block Status</th>";
            echo "</tr>";

            while ($row = mysqli_fetch_array($result)) {
                echo "<tr>";
                echo "<td>{$row[0]}</td>";
                echo "<td>{$row[1]}</td>";
                echo "<td>{$row[2]}</td>";
                echo "<td>{$row[3]}</td>";
                echo "<td>{$row[4]}</td>";
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "Error: " . mysqli_error($con);
        }

        
    ?>

    <!-- update -->
    <?php
        // Initialize variables to avoid undefined variable warnings
        $id = $uname = $pass = '';

        if (isset($_POST["btnupdate"])) {
            $id = $_POST["cmbAID"];
            $uname = $_POST["txtuname"];
            $pass = $_POST["txtpass"];
        
             

            if (!$con) {
                die("Connection failed: " . mysqli_connect_error());
            }

            // Use prepared statement to prevent SQL injection
            $query = "UPDATE adminlogin SET Username = ?, Password = ? WHERE AID = ?";
            
            // Prepare the statement
            $stmt = mysqli_prepare($con, $query);
            
            // Bind parameters
            mysqli_stmt_bind_param($stmt, "sss", $uname, $pass, $id);
            
            // Execute the statement
            $result = mysqli_stmt_execute($stmt);
            
            if ($result) {
                echo "Number of records updated: " . mysqli_stmt_affected_rows($stmt);
            } else {
                echo "Error updating record: " . mysqli_error($con);
            }

            // Clean up statement
            mysqli_stmt_close($stmt);
            
        }
    


        // delete
        if (isset($_POST["btndelete"])) {
            $id = $_POST["cmbAID"];

             

            if (!$con) {
                die("Connection failed: " . mysqli_connect_error());
            }

            // Use prepared statements to prevent SQL injection
            $query = "DELETE FROM adminlogin WHERE AID = '$id'";
            $result = mysqli_query($con, $query);

            echo "No of Data Deleted: " . $result;

            
        }
    ?>

    <!-- blockbtn -->
    <?php
    if (isset($_POST["btnblock"])) {
        $id = $_POST["cmbBAID"];

        // Check if a valid admin ID is selected
        if (!empty($id)) {
             

            if (!$con) {
                die("Connection failed: " . mysqli_connect_error());
            }

            // Block user by setting 'blocked' status to true (assuming 'blocked' column is a boolean)
            $blstatus = 1;

            $query = "UPDATE adminlogin SET blocked = $blstatus WHERE AID = ?";
            $stmtblock = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmtblock, "s", $id);
            mysqli_stmt_execute($stmtblock);
            $result = mysqli_stmt_affected_rows($stmtblock);

            if ($result) {
                echo "User Blocked: Admin ID - $id";
            } else {
                echo "Error blocking user: " . mysqli_error($con);
            }

            
        } else {
            echo "Please select an Admin ID to block.";
        }
    }

    if (isset($_POST["btnunblock"])) {
        $id = $_POST["cmbBAID"];

       
        if (!empty($id)) {
             

            if (!$con) {
                die("Connection failed: " . mysqli_connect_error());
            }

            // Unblock user by setting 'blocked' status to false (assuming 'blocked' column is a boolean)
            $blstatus = 0;
            $attempt=0;

            $query = "UPDATE adminlogin SET blocked = $blstatus ,login_attempts=$attempt WHERE AID = ?";
            $stmtunblovk = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmtunblovk, "s", $id);
            mysqli_stmt_execute($stmtunblovk);
            $result = mysqli_stmt_affected_rows($stmtunblovk);

            if ($result) {
                echo "User Unblocked: Admin ID - $id";
            } else {
                echo "Error unblocking user: " . mysqli_error($con);
            }

            
        } else {
            echo "Please select an Admin ID to unblock.";
        }
    }
    ?>


    <br><br>
    <div class="form-container">
        <form name="frmadmin" method="post" action="#">
            <table id='tb1'>
                <tr>
                <tr>
                    <td class="names">Admin ID </td>
                    <td>
                    <!-- add admin id to the combobox -->
                        <select name="cmbAID">
                            <option value="">Select Admin ID</option> 
                            <?php
                            
                             

                            if (!$con) {
                                die("Connection failed: " . mysqli_connect_error());
                            }

                            
                            $query = "SELECT AID FROM adminlogin WHERE AID != 'A001'";
                            $result = mysqli_query($con, $query);

                           
                            if ($result && mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<option value='{$row['AID']}'>{$row['AID']}</option>";
                                }
                            }

                            
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="names">User Name </td>
                    <td><input type="text" name="txtuname"> </td>
                </tr>
                <tr>
                    <td class="names">Password </td>
                    <td><input type="text" name="txtpass" ></td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" name="btnupdate" value="Update" class="btn btn-success"> <input type="submit" name="btndelete" value="Delete" class="btn btn-danger"></td>
                </tr>
           </table>
        </form>
    </div>
<br>
    <div class="form-container">
        <form name="frmadminstatus" method="post" action="#">
            <table id='tb1'>
                <tr>
                    <td class="names">Admin ID </td>
                    <td>
                    <!-- add admin id to the combobox -->
                        <select name="cmbBAID">
                            <option value="">Select Admin ID</option> 
                            <?php
                            
                             

                            if (!$con) {
                                die("Connection failed: " . mysqli_connect_error());
                            }

                            
                            $query = "SELECT AID FROM adminlogin WHERE AID != 'A001'";
                            $result = mysqli_query($con, $query);

                           
                            if ($result && mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<option value='{$row['AID']}'>{$row['AID']}</option>";
                                }
                            }

                            
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="submit" name="btnblock" value="Block User" class="btn btn-danger"> <input type="submit" name="btnunblock" value="Unblock User" class="btn btn-success">
                    </td>
                </tr>
        </table>
        </form>
    </div>

<br>

    <?php
         

        if (!$con) {
            die("Connection failed: " . mysqli_connect_error());
        }

        $details1 = "SELECT * FROM doctorlogin";
        $result1 = mysqli_query($con, $details1);

        if ($result1) {
            echo "<table id='tb1'>";
            echo "<tr>";
            echo "<th colspan='10'>Doctor Data</th>";
            echo "</tr>";
            echo "<tr>";
            echo "<th>Doctor Id</th>";
            echo "<th>Password</th>";
            echo "<th>Admin Id</th>";
            echo "<th>First Name</th>";
            echo "<th>Last Name</th>";
            echo "<th>Contact Number</th>";
            echo "<th>Specialization</th>";
            echo "<th>Email</th>";
            echo "<th>Arrival time</th>";
            echo "<th>Login Attempts</th>";
            echo "</tr>";

            while ($row1 = mysqli_fetch_array($result1)) {
                echo "<tr>";
                echo "<td>{$row1[0]}</td>";
                echo "<td>{$row1[1]}</td>";
                echo "<td>{$row1[2]}</td>";
                echo "<td>{$row1[3]}</td>";
                echo "<td>{$row1[4]}</td>";
                echo "<td>{$row1[5]}</td>";
                echo "<td>{$row1[6]}</td>";
                echo "<td>{$row1[7]}</td>";
                echo "<td>{$row1[8]}</td>";
                echo "<td>{$row1[9]}</td>";
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "Error: " . mysqli_error($con);
        }

        
    ?>

    <!-- update -->
    <?php
    // Initialize variables to avoid undefined variable warnings
    $id  = $pass = $fname = $lname = $cno = $spe = $email = $arrivaltime = '';

    if (isset($_POST["btnupdated"])) {
        $id = $_POST["cmbDID"] ;
        $pass = $_POST["txtpass"];
        $fname = $_POST["txtfname"];
        $lname = $_POST["txtlname"];
        $cno = $_POST["txtcno"];
        $spe = $_POST["txtspe"];
        $email = $_POST["txtemail"];
   


         

        if (!$con) {
            die("Connection failed: " . mysqli_connect_error());
        }

        // Use prepared statements to prevent SQL injection
        $query = "UPDATE doctorlogin SET  Password = ?, First_Name = ?, Last_Name = ?, Contact_No = ?, Specialization = ?, Email = ? WHERE DID = ?";
        $stmt = mysqli_prepare($con, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sssisss", $pass, $fname, $lname, $cno, $spe, $email,  $id);
            mysqli_stmt_execute($stmt);

            // Check how many rows were affected
            $affected_rows = mysqli_stmt_affected_rows($stmt);
            echo "No of Data Updated: " . $affected_rows;

            mysqli_stmt_close($stmt);
        } else {
            echo "Error preparing statement: " . mysqli_error($con);
        }

        
    }


        // delete
        if (isset($_POST["btndeleted"])) {
            $id = $_POST["cmbDID"];

            if (!$con) {
                die("Connection failed: " . mysqli_connect_error());
            }

            // Use prepared statements to prevent SQL injection
            $query = "DELETE FROM doctorlogin WHERE DID = ?";
            $stmtdel = mysqli_prepare($con, $query);
            if ($stmtdel) {
                mysqli_stmt_bind_param($stmtdel, "s", $id);
                mysqli_stmt_execute($stmtdel);
                // Check how many rows were affected
                $affected_rows = mysqli_stmt_affected_rows($stmtdel);
                echo "No of Data Deleted: " . $affected_rows;

                mysqli_stmt_close($stmtdel);
            } else {
                echo "Error preparing statement: " . mysqli_error($con);
            }

            echo "No of Data Deleted: " . $result;
            
        }


        if (isset($_POST["btnlga"])) {
            $id = $_POST["cmbDID"];
    
            // Check if a valid admin ID is selected
            if (!empty($id)) {
                 
    
                if (!$con) {
                    die("Connection failed: " . mysqli_connect_error());
                }
    
                $loginattempts = 0;
    
                $query = "UPDATE doctorlogin SET login_attempts = $loginattempts WHERE DID = ?";
                $stmtlga = mysqli_prepare($con, $query);

                if($stmtlga)
                {
                    mysqli_stmt_bind_param($stmtlga, "s", $id);
                    mysqli_stmt_execute($stmtlga);

                    // Check how many rows were affected                    
                    $affected_rows = mysqli_stmt_affected_rows($stmtlga);
                    echo "No of Data Updated: " . $affected_rows;

                    mysqli_stmt_close($stmtlga);
                }
    
                else
                {
                    echo "Error preparing statement: " . mysqli_error($con);
                }
    
                
            } else {
                echo "Please select an Doctor ID to block.";
            }
        }
      

    ?>

    <br><br>
    <div class="form-container">
        <form name="frmadmin" method="post" action="#">
            <table id='tb1'>
                <tr>
                    <td class="names">Doctor </td>
                    <td>
                    <!-- add admin id to the combobox -->
                        <select name="cmbDID">
                            <option value="">Select Doctor</option> 
                            <?php
                            
                             

                            if (!$con) {
                                die("Connection failed: " . mysqli_connect_error());
                            }

                            
                            $query = "SELECT * FROM doctorlogin";
                            $result = mysqli_query($con, $query);

                           
                            if ($result && mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<option value='{$row['DID']}'>{$row['First_Name']} {$row['Last_Name']}</option>";
                                }
                            }

                            
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="names">Password </td>
                    <td><input type="text" name="txtpass"></td>
                </tr>
                <tr>
                    <td class="names">First Name</td>
                    <td><input type="text" name="txtfname"></td>
                </tr>
                <tr>
                    <td class="names">Last Name</td>
                    <td><input type="text" name="txtlname"></td>
                </tr>
                <tr>
                    <td class="names">Contact No</td>
                    <td><input type="text" name="txtcno"></td>
                </tr>
                <tr>
                    <td class="names">Specialization</td>
                    <td><input type="text" name="txtspe"></td>
                </tr>
                <tr>
                    <td class="names">Email</td>
                    <td><input type="email" name="txtemail"></td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" name="btnupdated" value="Update" class="btn btn-success"> <input type="submit" name="btndeleted" value="Delete" class="btn btn-danger"></td>
                </tr>
           </table>
        </form>
    </div>
<br>
<div class="form-container">
        <form name="frmdoctorstatus" method="post" action="#">
            <table id='tb1'>
                <tr>
                    <td class="names">Doctor </td>
                    <td>
       
                        <select name="cmbDID">
                            <option value="">Select Doctor </option> 
                            <?php
                            
                             

                            if (!$con) {
                                die("Connection failed: " . mysqli_connect_error());
                            }

                            
                            $query = "SELECT * FROM doctorlogin";
                            $result = mysqli_query($con, $query);

                           
                            if ($result && mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<option value='{$row['DID']}'>{$row['First_Name']} {$row['Last_Name']}</option>";
                                }
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="submit" name="btnlga" value="Clear Login Attempts" class="btn btn-success">
                    </td>
                </tr>
        </table>
        </form>
    </div>
    <br>
</body>
</html>

<?php
mysqli_close($con);
?>
