<?php
session_start();
require '../connections/connection.php';
if (!isset($_SESSION['UserDID'])) {
    header("Location: doctorlogout.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Arrival Time</title>
    <link rel="icon" href="../pictures/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../css/viewclinic.css">
    <link rel="stylesheet" type="text/css" href="../css/doctorpanelstyle.css">
    <link rel="stylesheet" href="../css/navstyle.css">
    <link rel="stylesheet" type="text/css" href="../bootstraplibraries/css/bootstrap.min.css">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>
    <script type="text/javascript">
        emailjs.init('me2rjZpK5KnqfbBdm');

        function toggleNav() {
            const navItems = document.querySelector('.nav-items ul');
            navItems.classList.toggle('show');
        }
    </script>
</head>
<body>
<div class="nav">
    <div class="head">
        <h2>Arrival Time</h2>
    </div>
    <div class="nav-toggle" onclick="toggleNav()">
        <ion-icon name="menu-outline"></ion-icon>
    </div>
    <div class="nav-items">
        <ul>
            <li class="nav-item"><a class="nav-link" href="doctorpanel.php">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="changearrivaltime.php">Change Arrival Time</a></li>
            <li class="nav-item"><a class="nav-link" href="viewclinic.php">View Clinic</a></li>
            <li class="nav-item"><a class="nav-link" href="doctorlogout.php">Log Out</a></li>
        </ul>
    </div>
</div>
<br><br><br><br><br><br>
<div class="form-container">
    <form name="frmdoctor" method="post" action="changearrivaltime.php">
        <table id='tb1'>
            <tr>
                <td class="names">Clinic</td>
                <td>
                    <select name="cmbCID" required>
                        <option value="">Select Clinic</option>
                        <?php
                        

                        if (!$con) {
                            die("Connection failed: " . mysqli_connect_error());
                        }

                        $userDID = mysqli_real_escape_string($con, $_SESSION['UserDID']);
                        $current_date = date('Y-m-d');

                        $query = "SELECT CID, Date
                                  FROM clinic
                                  INNER JOIN schedule ON clinic.SID = schedule.SID
                                  WHERE schedule.DID = '$userDID' AND clinic.Date >= '$current_date'";
                        $result = mysqli_query($con, $query);

                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='{$row['CID']}'>{$row['Date']}</option>";
                            }
                        }

                        
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="names">Arrival Time</td>
                <td><input type="time" name="txtat" required></td>
            </tr>
            <tr>
                <td class="btn" colspan="2">
                    <input type="submit" name="btnatupdate" value="Update Time">
                </td>
            </tr>
        </table>
    </form>
</div>
<br>
</body>
</html>
<?php

// Handle form submission
if (isset($_POST["btnatupdate"])) {
    $id = $_POST["cmbCID"];
    $arrivaltime = $_POST["txtat"];

    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }

    mysqli_begin_transaction($con);

    try {
        // Update the clinic table
        $query1 = "UPDATE clinic SET Start_Time = ? WHERE CID = ?";
        $stmt1 = mysqli_prepare($con, $query1);
        mysqli_stmt_bind_param($stmt1, "ss", $arrivaltime, $id);
        mysqli_stmt_execute($stmt1);

        // Update the doctorlogin table
        $query2 = "UPDATE doctorlogin SET Arrival_Time = ? WHERE DID = ?";
        $stmt2 = mysqli_prepare($con, $query2);
        mysqli_stmt_bind_param($stmt2, "ss", $arrivaltime, $_SESSION['UserDID']);
        mysqli_stmt_execute($stmt2);

        

        // Retrieve patient emails and telephone numbers
        $query3 = "SELECT patient.Email, patient.First_Name, patient.Last_Name, patient.Contact_NO, clinic.Date 
                   FROM appointment
                   INNER JOIN patient ON appointment.Patient_ID = patient.Patient_ID
                   INNER JOIN clinic ON appointment.Clinic_ID = clinic.CID
                   WHERE appointment.Clinic_ID = ?";
        $stmt3 = mysqli_prepare($con, $query3);
        mysqli_stmt_bind_param($stmt3, "s", $id);
        mysqli_stmt_execute($stmt3);
        $result = mysqli_stmt_get_result($stmt3);

        // Prepare email and SMS data
        $email_data = [];
        $sms_data = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $email_data[] = [
                'Email' => $row['Email'],
                'First_Name' => $row['First_Name'],
                'Last_Name' => $row['Last_Name'],
                'Arrival_Time' => $arrivaltime,
                'Clinic_Date' => $row['Date']
            ];

            $formatted_contact_no = preg_replace('/^0/', '94', $row['Contact_NO']); 
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
            $text = urlencode("Dear {$data['First_Name']} {$data['Last_Name']},\nDoctor's Arrival Time for {$data['Clinic_Date']} has been changed to {$arrivaltime}.\nPlease be on time for your appointment. \nThank you!\nBest Regards,\nQueuePro.");
            $url = "$baseurl/?id=$user&pw=$password&to={$data['Contact_NO']}&text=$text";
            $ret = file($url);
            $res = explode(":", $ret[0]);

        }

       

        // Commit the transaction
        mysqli_commit($con);
        mysqli_close($con);

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
                                    }, 500);  // Delay between batches (0.5 seconds)
                                } else {
                                    alert('Arrival time updated successfully. Emails sent: ' + successCount + ', Failed: ' + failureCount);
                                    window.location.href = 'doctorpanel.php';
                                }
                            });
                    }

                    sendBatch(emailData.splice(0, batchSize));

                    function sendEmail(data) {
                        return new Promise(function(resolve) {
                            let params = {
                                to_email: data.Email,
                                to_name: data.First_Name + ' ' + data.Last_Name,
                                arrival_time: data.Arrival_Time,
                                clinic_date: data.Clinic_Date,
                            };

                            emailjs.send('service_6l79cl3', 'template_bwe7s21', params)
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

    } catch (Exception $e) {
        mysqli_rollback($con);
        echo "<script>alert('An error occurred: " . $e->getMessage() . "');</script>";
        mysqli_close($con);
    }
}
?>
