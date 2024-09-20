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

if (isset($_GET['CID']) && isset($_GET['SID'])) {
    $clinicID = mysqli_real_escape_string($con, $_GET['CID']);
    $scheduleID = mysqli_real_escape_string($con, $_GET['SID']);

    // SQL query with INNER JOIN to fetch appointment details with patient information
    $appointmentDetails = "SELECT a.*, p.First_Name, p.Last_Name 
                           FROM appointment a 
                           INNER JOIN patient p ON a.Patient_ID = p.Patient_ID 
                           WHERE a.Clinic_ID = '$clinicID' AND a.Schedule_ID = '$scheduleID' 
                           ORDER BY p.Patient_ID ASC";
    
    $result = mysqli_query($con, $appointmentDetails);

    // Initialize patient count
    $patientCount = 0;

} else {
    echo "No clinic ID or schedule ID provided.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Appointments</title>
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

        .nav {
        width: 100%;
        background-color: #003366; /* Dark blue for the navigation bar */
        padding: 15px 20px;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1000;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .nav .head h2 {
        color: #fff;
        font-size: 24px;
        font-weight: 700;
    }

    .nav-toggle {
        display: none;
        color: #fff;
        font-size: 24px;
        cursor: pointer;
        transition: color 0.3s;
    }

    .nav-toggle:hover {
        color: #cce5ff;
    }

    .nav-items {
        display: flex;
    }

    .nav-items ul {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        gap: 20px;
    }

    .nav-item {
        margin-right: 20px;
    }

    .nav-link {
        color: #fff;
        text-decoration: none;
        font-size: 16px;
        padding: 10px 15px;
        border-radius: 5px;
        transition: background-color 0.3s, transform 0.2s;
    }

    .nav-link:hover {
        background-color: #0056b3;
        transform: scale(1.05);
    }
        

        @media only screen and (max-width: 768px) {
            .nav-toggle {
                display: block;
            }

            .nav-items {
                display: none;
                position: absolute;
                top: 50px;
                left: 0;
                width: 100%;
                background-color: #007bff;
                flex-direction: column;
                gap: 10px;
                padding: 10px;
            }

            .nav-items.show {
                display: flex;
            }

            .nav-items ul {
                flex-direction: column;
            }

            .nav-items ul li {
                margin-right: 0;
                margin-bottom: 10px;
            }
            .table {
            width: 80%;
            margin-bottom: 1rem;
            background-color: #fff;
            color: #495057;
            border-collapse: collapse;
            
        }
        }

        /* Table Styles */
        .table {
            width: 100%;
            max-width: 100%;
            margin-bottom: 1rem;
            background-color: #fff;
            color: #495057;
            border-collapse: collapse;
            
        }

        .table th,
        .table td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }

        .table th {
            background-color: #007bff;
            color: #fff;
            border-color: #007bff;
        }

        .table th,
        .table td {
            border: 1px solid #dee2e6;
        }

        .table tbody tr:nth-of-type(even) {
            background-color: rgba(0, 123, 255, 0.1);
        }

        .table tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.2);
        }
    </style>
</head>
<body>
<script>
    function toggleNav() {
        var nav = document.querySelector('.nav-items');
        nav.classList.toggle('show');
    }
</script>

<div class="nav">
    <div class="head">
        <h2>View Appointments</h2>
    </div>
    <div class="nav-toggle" onclick="toggleNav()">
        <ion-icon name="menu-outline"></ion-icon>
    </div>
    <div class="nav-items">
        <ul>
            <li><a href="doctorpanel.php">Home</a></li>
            <li><a href="changearrivaltime.php">Change Arrival Time</a></li>
            <li><a href="viewclinic.php">View Clinic</a></li>
            <li><a href="doctorlogout.php">Log Out</a></li>
        </ul>
    </div>
</div>
<br><br><br><br>

<!-- Display Appointments in a Table -->
<table class="table table-bordered table-hover">
    <thead class="thead-dark">
        <tr>
            <th>Patient Name</th>
            <th>Appointment Date</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
            
                echo "<td>{$row['First_Name']} {$row['Last_Name']}</td>";
                echo "<td>{$row['Date_and_Time']}</td>";
                echo "</tr>";
                // Increment patient count
                $patientCount++;
            }
        } else {
            echo "<tr><td colspan='2'>No appointments found for this clinic and schedule.</td></tr>";
        }
        ?>
    </tbody>
    <tfoot>
        <tr>
            <td style="text-align: right;"><strong>Total Patients:</strong></td>
            <td><?php echo $patientCount; ?></td>
        </tr>
    </tfoot>
</table>

<br><br>

</body>
</html>

<?php
mysqli_close($con);
?>
