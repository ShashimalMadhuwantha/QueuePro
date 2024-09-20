<?php
session_start();
require '../connections/connection.php';
// Check if the user is logged in
if (!isset($_SESSION['UserDID'])) {
    header("Location: doctorlogout.php");
    exit();
}



if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
$current_date = date('Y-m-d');
// Escape the session variable to prevent SQL injection
$userDID = mysqli_real_escape_string($con, $_SESSION['UserDID']);

$details = "SELECT schedule.DID, clinic.*
            FROM schedule
            INNER JOIN clinic ON schedule.SID = clinic.SID
           WHERE schedule.DID = '$userDID' AND clinic.Date >= '$current_date'";
$result = mysqli_query($con, $details);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Clinics</title>
    <link rel="title icon" href="../pictures/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/navstyle.css">

    <link rel="stylesheet" type="text/css" href="../bootstraplibraries/css/bootstrap.min.css">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <style>
        /* General Styles */
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        /* Card Styles */
        .card-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 20px;
        }

        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin: 15px;
            width: 300px;
            transition: transform 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-content {
            padding: 20px;
        }

        .card-content p {
            margin: 10px 0;
            font-size: 0.9em;
        }

        /* Button Styles */
        .action-button {
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
            font-size: 1em;
            padding: 10px;
            text-align: center;
            width: 100%;
            transition: background-color 0.3s;
            margin-top: 10px;
        }

        .action-button:hover {
            background-color: #0056b3;
        }

        /* Media Queries */
        @media only screen and (max-width: 600px) {
            .card {
                width: 50%;
                max-width: 300px;
            }
        }
    </style>
</head>
<body>
<script>
    function toggleNav() {
        var nav = document.querySelector('.nav-items ul');
        nav.classList.toggle('show');
    }
</script>

<div class="nav">
    <div class="head">
        <h2>View Clinic</h2>
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
<br>
<br><br>
<!-- Display Clinics as Cards -->
<div class="card-container">
    <?php
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<div class='card'>";
            echo "<div class='card-content'>";
            echo "<h3>Clinic ID: {$row['CID']}</h3>";
            echo "<p><strong>Schedule ID:</strong> {$row['SID']}</p>";
            echo "<p><strong>Start Time:</strong> {$row['Start_Time']}</p>";
            echo "<p><strong>Patient Limit:</strong> {$row['P_Limit']}</p>";
            echo "<p><strong>Date:</strong> {$row['Date']}</p>";
            echo "<p><strong>Room Number:</strong> {$row['Room_Number']}</p>";
            echo "<p><strong>Current State:</strong> {$row['Current_State']}</p>";
            echo "<button class='action-button' onclick=\"location.href='docviewappoinment.php?CID={$row['CID']}&SID={$row['SID']}'\">View Appointments</button>";
            echo "</div>";
            echo "</div>";
        }
    } else {
        echo "No clinics found.";
    }
    ?>
</div>

<br><br>


</body>
</html>

<?php
mysqli_close($con);
?>
