<?php
session_start();
require '../connections/connection.php';
// Check if the user is logged in
if (!isset($_SESSION['UserID'])) {
    header("Location: Adminlogout.php");
    exit();
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    

    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Use prepared statements
    if (isset($_POST['btnSsubmit'])) {
        // Add schedule
        $did = $_POST["cmbDID"] ?? '';
        $start_time = $_POST["txtStartTime"] ?? '';
        $duration = $_POST["txtDuration"] ?? '';
        $tpt = $_POST["txttpt"] ?? '';
        $day = $_POST["txtday"] ?? '';

        // Query to get the maximum Schedule ID
        $query_max_id = "SELECT MAX(CAST(SUBSTRING(SID, 2) AS UNSIGNED)) AS max_id FROM schedule WHERE SID LIKE 'S%'";
        $result = mysqli_query($con, $query_max_id);
        $row = mysqli_fetch_assoc($result);
        $max_id = $row['max_id'] ?? 0;

        // Incrementing Schedule ID
        $next_id = 'S' . str_pad($max_id + 1, 3, '0', STR_PAD_LEFT);

        $stmt = $con->prepare("INSERT INTO schedule (SID, DID, AID, Start_Time, Duration, Target_Patient_Type, Day) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $next_id, $did, $_SESSION['UserID'], $start_time, $duration, $tpt, $day);
        $stmt->execute();
        $stmt->close();
    }

    if (isset($_POST['btnSupdate'])) {
        // Update schedule
        $sid = $_POST["cmbSID"] ?? '';
        $did = $_POST["cmbDID"] ?? '';
        $start_time = $_POST["txtStartTime"] ?? '';
        $duration = $_POST["txtDuration"] ?? '';
        $tpt = $_POST["txttpt"] ?? '';
        $day = $_POST["txtday"] ?? '';

        if ($sid) {
            $stmt = $con->prepare("UPDATE schedule SET DID=?, Start_Time=?, Duration=?, Target_Patient_Type=?, Day=? WHERE SID=?");
            $stmt->bind_param("ssssss", $did, $start_time, $duration, $tpt, $day, $sid);
            $stmt->execute();
            $stmt->close();
        }
    }

    if (isset($_POST['btnSdelete'])) {
        // Delete schedule
        $sid = $_POST["cmbSID"] ?? '';
        if ($sid) {
            $stmt = $con->prepare("DELETE FROM schedule WHERE SID=?");
            $stmt->bind_param("s", $sid);
            $stmt->execute();
            $stmt->close();

            // Also delete from clinic
            $stmt = $con->prepare("DELETE FROM clinic WHERE SID=?");
            $stmt->bind_param("s", $sid);
            $stmt->execute();
            $stmt->close();
        }
    }


    header("Refresh:0");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Schedule</title>
    <link rel="icon" href="../pictures/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/navstyle.css">
    <link rel="stylesheet" href="../css/tablestyle.css">
    <link rel="stylesheet" href="../bootstraplibraries/css/bootstrap.min.css">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <style>
        /* Add your additional styles here */
    </style>
</head>
<body>
<script>
    function toggleNav() {
        var nav = document.querySelector('.nav-items ul');
        nav.classList.toggle('show');
    }

    function redirectToAddClinic(sid, starttime, day) {
        window.location.href = "addclinics.php?SID=" + encodeURIComponent(sid) + "&Start_Time=" + encodeURIComponent(starttime) + "&Day=" + encodeURIComponent(day);
    }
    
</script>

<div class="nav">
    <div class="head">
        <h2>Schedule</h2>
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
// Display existing schedules in a table


if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$details = "SELECT * FROM schedule";
$result = mysqli_query($con, $details);

if ($result) {
    echo "<table id='tb1'>";
    echo "<tr><th colspan='8'>Schedule Data</th></tr>";
    echo "<tr>
            <th>Schedule Id</th>
            <th>Doctor Id</th>
            <th>Admin Id</th>
            <th>Start Time</th>
            <th>Duration</th>
            <th>Target Patient Type</th>
            <th>Day</th>
            <th>Action</th>
          </tr>";

    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>{$row['SID']}</td>";
        echo "<td>{$row['DID']}</td>";
        echo "<td>{$row['AID']}</td>";
        echo "<td>{$row['Start_Time']}</td>";
        echo "<td>{$row['Duration']}</td>";
        echo "<td>{$row['Target_Patient_Type']}</td>";
        echo "<td>{$row['Day']}</td>";
        echo "<td><button class='btn btn-success' onclick='redirectToAddClinic(\"{$row['SID']}\", \"{$row['Start_Time']}\", \"{$row['Day']}\")'>Add Clinics</button></td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "Error: " . mysqli_error($con);
}


?>

<br><br><br>

<!-- Form to add schedule -->
<div class="form-container">
    <form name="frmschedule" method="post" action="#">
        <table id='tb1'>
            <tr>
                <td class="names">Doctor</td>
                <td>
                    <select name="cmbDID" required>
                        <option value="">Select Doctor</option>
                        <?php
                        
                        if (!$con) {
                            die("Connection failed: " . mysqli_connect_error());
                        }
                        $query = "SELECT * FROM doctorlogin";
                        $result = mysqli_query($con, $query);
                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='{$row['DID']}'>{$row['First_Name']}</option>";
                            }
                        }
                        
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="names">Start Time</td>
                <td><input type="time" name="txtStartTime" required></td>
            </tr>
            <tr>
                <td class="names">Duration</td>
                <td><input type="text" name="txtDuration" required></td>
            </tr>
            <tr>
                <td class="names">Target Patient Type</td>
                <td><input type="text" name="txttpt" required></td>
            </tr>
            <tr>
                <td class="names">Day</td>
                <td><input type="text" name="txtday" required></td>
            </tr>
            <tr>
                <td colspan="2"><button type="submit" name="btnSsubmit" class="btn btn-success">Add Schedule</button></td>
            </tr>
        </table>
    </form>
</div>
<br><br>
<!-- Form to update schedule -->
<div class="form-container">
    <form name="frmscheduleupdate" method="post" action="#">
        <table id='tb1'>
            <tr>
                <td class="names">Schedule Id</td>
                <td>
                    <select name="cmbSID" required>
                        <option value="">Select Schedule</option>
                        <?php
                        
                        if (!$con) {
                            die("Connection failed: " . mysqli_connect_error());
                        }
                        $query = "SELECT SID FROM schedule";
                        $result = mysqli_query($con, $query);
                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='{$row['SID']}'>{$row['SID']}</option>";
                            }
                        }
                        
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="names">Doctor</td>
                <td>
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
                                echo "<option value='{$row['DID']}'>{$row['First_Name']}</option>";
                            }
                        }
                        
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="names">Start Time</td>
                <td><input type="time" name="txtStartTime"></td>
            </tr>
            <tr>
                <td class="names">Duration</td>
                <td><input type="text" name="txtDuration"></td>
            </tr>
            <tr>
                <td class="names">Target Patient Type</td>
                <td><input type="text" name="txttpt"></td>
            </tr>
            <tr>
                <td class="names">Day</td>
                <td><input type="text" name="txtday"></td>
            </tr>
            <tr>
                
                <td colspan="2"><button type="submit" name="btnSupdate" class="btn btn-success">Update Schedule</button></td>
            </tr>
        </table>
    </form>
</div>
<br><br>
<!-- Form to delete schedule -->
<div class="form-container">
    <form name="frmscheduledelete" method="post" action="#" onsubmit="return confirmDelete()">
        <table id='tb1'>
            <tr>
                <td class="names">Schedule Id</td>
                <td>
                    <select name="cmbSID" required>
                        <option value="">Select Schedule</option>
                        <?php
                        
                        if (!$con) {
                            die("Connection failed: " . mysqli_connect_error());
                        }
                        $query = "SELECT SID FROM schedule";
                        $result = mysqli_query($con, $query);
                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='{$row['SID']}'>{$row['SID']}</option>";
                            }
                        }
                        mysqli_close($con);
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                
                <td colspan="2"><button type="submit" name="btnSdelete" class="btn btn-danger">Delete Schedule</button></td>
            </tr>
        </table>
    </form>
</div>
<br><br>
</body>
</html>

<script type="text/javascript">
    function confirmDelete() {
        return confirm("Are you sure you want to delete this clinic?");
    }
    </script>