<?php
session_start();
require '../connections/connection.php';
// Check if the user is logged in
if (!isset($_SESSION['UserID'])) {
    header("Location: Adminlogout.php");
    exit();
}

$schedule_id = isset($_GET['SID']) ? $_GET['SID'] : '';
$start_time = isset($_GET['Start_Time']) ? $_GET['Start_Time'] : '';

if (isset($_POST["btnSCsubmit"]) || isset($_POST["btnSCupdate"]) || isset($_POST["btnSCdelete"])) {
    header("Refresh:0");
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Clinics</title>
    <link rel="title icon" href="../pictures/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/navstyle.css">
    <link rel="stylesheet" type="text/css" href="../css/tablestyle.css">
    <link rel="stylesheet" type="text/css" href="../bootstraplibraries/css/bootstrap.min.css">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>

    <script>
        emailjs.init('me2rjZpK5KnqfbBdm');

        function toggleNav() {
            var nav = document.querySelector('.nav-items ul');
            nav.classList.toggle('show');
        }

        function fetchAvailableRooms(formType) {
            const dateInput = document.getElementById(formType + '_txtday');
            const roomSelect = document.getElementById(formType + '_txtrm');
            const date = dateInput.value;

            if (date) {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'fetchrooms.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function () {
                    if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                        roomSelect.innerHTML = this.responseText;
                    }
                };
                xhr.send('date=' + encodeURIComponent(date));
            }
        }
    </script>
</head>
<body>
<div class="nav">
    <div class="head">
        <h2>Clinics</h2>
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

$details = "SELECT * FROM clinic";
$result = mysqli_query($con, $details);

if ($result) {
    echo "<table id='tb1'>";
    echo "<tr>";
    echo "<th colspan='7'>Clinic Data</th>";
    echo "</tr>";
    echo "<tr>";
    echo "<th>Clinic Id</th>";
    echo "<th>Schedule Id</th>";
    echo "<th>Start Time</th>";
    echo "<th>Patient Limit</th>";
    echo "<th>Date</th>";
    echo "<th>Room Number</th>";
    echo "<th>Current State</th>";
    echo "</tr>";

    while ($row = mysqli_fetch_array($result)) {
        echo "<tr>";
        echo "<td>{$row[0]}</td>";
        echo "<td>{$row[1]}</td>";
        echo "<td>{$row[2]}</td>";
        echo "<td>{$row[3]}</td>";
        echo "<td>{$row[4]}</td>";
        echo "<td>{$row[5]}</td>";
        echo "<td>{$row[6]}</td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "Error: " . mysqli_error($con);
}


?>
<br><br><br>

<!-- Form to add clinic -->
<div class="form-container">
    <form name="frmschedule" method="post" action="#">
        <table id='tb1'>
            <tr>
                <td class="names">Patient Limit</td>
                <td><input type="text" name="txtpl"></td>
            </tr>
            <tr>
                <td class="names">Date</td>
                <td><input type="date" name="txtday" id="add_txtday" onchange="fetchAvailableRooms('add')"></td>
            </tr>
            <tr>
                <td class="names">Room Number</td>
                <td>
                    <select name="txtrm" id="add_txtrm">
                        <option value="">Select Room Number</option>
                        <!-- Available room numbers will be loaded here by JavaScript -->
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2"><input type="submit" name="btnSCsubmit" value="Submit" class="btn btn-success"></td>
            </tr>
        </table>
    </form>
</div>
<br><br>
<!-- Form to update clinic -->
<div class="form-container">
    <form name="frmupdate" method="post" action="#">
        <table id='tb1'>
            <tr>
                <td class="names">Clinic ID</td>
                <td>
                    <select name="cmbCID">
                        <option value="">Select Clinic ID</option>
                        <?php
                        
                        if (!$con) {
                            die("Connection failed: " . mysqli_connect_error());
                        }
                        $query = "SELECT CID FROM clinic";
                        $result = mysqli_query($con, $query);
                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='{$row['CID']}'>{$row['CID']}</option>";
                            }
                        }
                        
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="names">Schedule ID</td>
                <td>
                    <select name="cmbSID">
                        <option value="">Select Schedule ID</option>
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
                <td class="names">Patient Limit</td>
                <td><input type="text" name="txtpl"></td>
            </tr>
            <tr>
                <td class="names">Date</td>
                <td><input type="date" name="txtday" id="update_txtday" onchange="fetchAvailableRooms('update')"></td>
            </tr>
            <tr>
                <td class="names">Room Number</td>
                <td>
                    <select name="txtrm" id="update_txtrm">
                        <option value="">Select Room Number</option>
                        <!-- Available room numbers will be loaded here by JavaScript -->
                    </select>
                </td>
            </tr>
      
            <tr>
                <td colspan="2"><input type="submit" name="btnSCupdate" value="Update" class="btn btn-success"></td>
            </tr>
        </table>
    </form>
</div>
<br><br>


<div class="form-container">
    <form name="frmstatusupdate" method="post" action="#">
        <table id='tb1'>
            <tr>
                <td class="names">Clinic ID</td>
                <td>
                    <select name="cmbCID">
                        <option value="">Select Clinic ID</option>
                        <?php
                        
                        if (!$con) {
                            die("Connection failed: " . mysqli_connect_error());
                        }
                        $query = "SELECT CID FROM clinic";
                        $result = mysqli_query($con, $query);
                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='{$row['CID']}'>{$row['CID']}</option>";
                            }
                        }
                        
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="names">Current State</td>
                <td>
                    <select name="cmbCS">
                        <option value="">Select Clinic State</option>
                        <option value="OnGoing">OnGoing</option>
                        <option value="No">No</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2"><input type="submit" name="btnSCUPdate" value="Update" class="btn btn-success"></td>
            </tr>
        </table>
    </form>
</div>
<br><br>


<!-- Form to delete clinic -->
<div class="form-container">
    <form name="frmdelete" method="post" action="#"  onsubmit="return confirmDelete();">
        <table id='tb1'>
            <tr>
                <td class="names">Clinic ID</td>
                <td>
                    <select name="cmbCID">
                        <option value="">Select Clinic ID</option>
                        <?php
                        
                        if (!$con) {
                            die("Connection failed: " . mysqli_connect_error());
                        }
                        $query = "SELECT CID FROM clinic";
                        $result = mysqli_query($con, $query);
                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='{$row['CID']}'>{$row['CID']}</option>";
                            }
                        }
                        
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="submit" name="btnSCdelete" value="Delete" class="btn btn-danger">

                </td>
            </tr>
        </table>
    </form>
</div>
<br><br>
</body>
</html>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }

    if (isset($_POST['btnSCsubmit'])) {
        // Add clinic
        $cid = $_POST["cmbCID"] ?? '';
        $sid = $_POST["cmbSID"] ?? '';
        $pl = $_POST["txtpl"] ?? '';
        $date = $_POST["txtday"] ?? '';
        $rm = $_POST["txtrm"] ?? '';
        $cs = "OnGoing" ?? '';

        // Query to get the maximum Clinic ID
        $query_max_id = "SELECT MAX(SUBSTRING(CID, 2)) AS max_id FROM clinic WHERE CID LIKE 'C%'";
        $result = mysqli_query($con, $query_max_id);
        $row = mysqli_fetch_assoc($result);
        $max_id = $row['max_id'];

        // Incrementing Clinic ID
        if ($max_id === null) {
            // If no records exist, start with C001
            $next_id = 'C001';
        } else {
            // Increment the last ID found
            $next_id = 'C' . str_pad((int)$max_id + 1, 3, '0', STR_PAD_LEFT);
        }

        $insert_query = "INSERT INTO clinic (CID, SID, Start_Time, P_Limit, Date, Room_Number, Current_State) 
        VALUES( '$next_id', '$schedule_id', '$start_time' , '$pl', '$date', '$rm', '$cs')";
        mysqli_query($con, $insert_query);
    }

    if (isset($_POST['btnSCupdate'])) {
        // Update clinic
        $cid = $_POST["cmbCID"] ?? '';
        $sid = $_POST["cmbSID"] ?? '';
        $pl = $_POST["txtpl"] ?? '';
        $date = $_POST["txtday"] ?? '';
        $rm = $_POST["txtrm"] ?? '';
     

        if ($sid) {
            $update_query = "UPDATE clinic c
                             JOIN schedule s ON c.SID = s.SID
                             SET c.SID='$sid', c.Start_Time=s.Start_Time, c.P_Limit='$pl', c.Date='$date', c.Room_Number='$rm'
                             WHERE c.CID='$cid'";
            mysqli_query($con, $update_query);
        }
    }

    if (isset($_POST['btnSCUPdate'])) {
        $cid = $_POST["cmbCID"] ?? '';
        $cs = $_POST["cmbCS"] ?? '';
        
        if ($cs && $cid) {
            
        
            if (!$con) {
                die("Connection failed: " . mysqli_connect_error());
            }
        
           
        
            try {
                // Update the clinic state
                $status_Query = "UPDATE clinic SET Current_State='$cs' WHERE CID='$cid'";
                mysqli_query($con, $status_Query);
        
                if ($cs === 'No') {
                    // Retrieve patient emails
                    
                    $query = "SELECT patient.Email,patient.Contact_NO, patient.First_Name, patient.Last_Name, clinic.Date 
                              FROM appointment
                              INNER JOIN patient ON appointment.Patient_ID = patient.Patient_ID
                              INNER JOIN clinic ON appointment.Clinic_ID = clinic.CID
                              WHERE appointment.Clinic_ID = ?";
                    $stmt = mysqli_prepare($con, $query);
                    mysqli_stmt_bind_param($stmt, "s", $cid);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
        
                    $email_data = [];
                    while ($row = mysqli_fetch_assoc($result)) {
                        $email_data[] = [
                            'Email' => $row['Email'],
                            'First_Name' => $row['First_Name'],
                            'Last_Name' => $row['Last_Name'],
                            'Clinic_Date' => $row['Date']
                        ];

                        $formatted_contact_no = preg_replace('/^0/', '94', $row['Contact_NO']); // Format phone number
                        $sms_data[] = [
                            'First_Name' => $row['First_Name'],
                            'Last_Name' => $row['Last_Name'],
                            'Contact_NO' => $formatted_contact_no,
                            'Clinic_Date' => $row['Date']
                        ];
                    }
            
                    // Send SMS
                    $user = "94783522092";  
                    $password = "6362";     
                    $baseurl = "https://www.textit.biz/sendmsg";
                    
                    foreach ($sms_data as $data) {
                        $text = urlencode("Dear {$data['First_Name']} {$data['Last_Name']}, \n\nThe Clinic which was scheduled to be held on {$data['Clinic_Date']}  has been canceled Due to An Unavoidable Reason. Please Make another appointment for the closest day, and we are extremely sorry for the inconvenience occured!\nBest Regards,\nQueuePro.");
                        $url = "$baseurl/?id=$user&pw=$password&to={$data['Contact_NO']}&text=$text";
                        $ret = file($url);
                        $res = explode(":", $ret[0]);
            
                        // if (trim($res[0]) == "OK") {
                        //     echo '<script>alert("SMS Sent - ID: ' . $res[1] . '");</script>';
                        // } else {
                        //     echo '<script>alert("SMS Failed - Error: ' . $res[1] . '");</script>';
                        // }
                    }
        
                    mysqli_stmt_close($stmt);
        
                    // Commit the transaction
                    mysqli_commit($con);
                    
        
                    // Encode email data for JavaScript
                    echo "<script type='text/javascript'>
                            var emailData = " . json_encode($email_data) . ";
                          </script>";
        
                    echo "<script type='text/javascript'>
                            document.addEventListener('DOMContentLoaded', function() {
                                const batchSize = 5;  // Number of emails to send per batch
        
                                let successCount = 0;
                                let failureCount = 0;
        
                                function sendBatch(batch) {
                                    let promises = batch.map(function(data) {
                                        return sendEmail(data);
                                    });
        
                                    Promise.all(promises)
                                        .then(function(results) {
                                            results.forEach(function(result) {
                                                if (result === 'success') {
                                                    successCount++;
                                                } else {
                                                    failureCount++;
                                                }
                                            });
        
                                            if (emailData.length > 0) {
                                                setTimeout(function() {
                                                    sendBatch(emailData.splice(0, batchSize));
                                                }, 1000);  // Delay between batches
                                            } else {
                                                alert('Clinic state updated successfully. Emails sent: ' + successCount + ', Failed: ' + failureCount);
                                                window.location.href = 'addclinics.php';
                                            }
                                        });
                                }
        
                                sendBatch(emailData.splice(0, batchSize));
        
                                function sendEmail(data) {
                                    return new Promise(function(resolve) {
                                        let params = {
                                            to_email: data.Email,
                                            to_name: data.First_Name + ' ' + data.Last_Name,
                                            date: data.Clinic_Date
                                        };
        
                                        emailjs.send('service_6l79cl3', 'template_lu4tnqu', params)
                                            .then(function(response) {
                                                console.log('Email sent to ' + data.Email + ': ' + response.status + ' ' + response.text);
                                                resolve('success');
                                            }, function(error) {
                                                console.error('Failed to send email to ' + data.Email + ': ' + JSON.stringify(error));
                                                resolve('failure');
                                            });
                                    });
                                }
                            });
                          </script>";
        
                } else {
                    // No emails to send if the state is not "No"
                    echo "<script>alert('Clinic state updated successfully.'); window.location.href = 'addclinics.php';</script>";
                }
        
            } catch (Exception $e) {
                mysqli_rollback($con);
                echo "<script>alert('An error occurred: " . $e->getMessage() . "');</script>";
                
            }
        }
    }
    

    if (isset($_POST['btnSCdelete'])) {
        // Delete clinic
        $cid = $_POST["cmbCID"] ?? '';
        if ($cid) {
            $delete_query = "DELETE FROM clinic WHERE CID='$cid'";
            mysqli_query($con, $delete_query);
        }
    }

    mysqli_close($con);
}
?>
<script type="text/javascript">
    function confirmDelete() {
        return confirm("Are you sure you want to delete this clinic?");
    }
    </script>