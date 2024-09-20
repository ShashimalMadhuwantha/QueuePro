<?php
session_start();
require '../connections/connection.php';

if (!isset($_SESSION['email'])) {
    header('Location: verify_email.php');
    exit();
}

$email = $_SESSION['email'];



if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$sql = "SELECT p.First_Name AS PatientFirstName, p.Last_Name AS PatientLastName, c.Start_Time, d.First_Name AS DoctorFirstName, d.Last_Name AS DoctorLastName, c.Room_Number 
        FROM patient p 
        JOIN appointment a ON p.Patient_ID = a.Patient_ID
        JOIN schedule s ON a.Schedule_ID = s.SID
        JOIN doctorlogin d ON a.Doctor_ID = d.DID
        JOIN clinic c ON a.Clinic_ID = c.CID
        WHERE p.Email = ?";

$stmt = $con->prepare($sql);
if ($stmt === false) {
    die("Prepare failed: " . $con->error);
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result === false) {
    die("Execute failed: " . $stmt->error);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Appointments</title>
    <link rel="title icon" href="../pictures/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.4.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- <link rel="stylesheet" href="style.css" /> -->
    <link rel="stylesheet" href="../css/main.css" />
    <!-- <script defer src="../js/script.js"></script> -->
    <style>
         nav {
                padding: 2rem 1rem;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                position: relative;
                }

                .nav__logo {
                font-size: 1.7rem;
                font-weight: 600;
                color: #4b70f5;
                }

                .nav__links {
                list-style: none;
                display: flex;
                align-items: center;
                gap: 2rem;
                position: inherit;
                }

                .link a {
                text-decoration: none;
                padding: 2rem;
                color: #6b7280;
                cursor: pointer;
                transition: 0.3s;
                }

                .link a:hover {
                color: #4b70f5;
                }

                .fa-bars {
                display: none;
                font-size: 15px;
                cursor: pointer;
                }
                @media (max-width: 800px)
                {
                   .nav-toggle {
                        display: block;
                        cursor: pointer;
                    }
                    .fa-bars {
                    display: block;
                    font-size: 25px;
                    color: #74C0FC;
                    cursor: pointer;
                }
                
                .nav__links {
                    display: none;
                    flex-direction: column;
                    gap: 2rem;
                    background-color: #fff;
                    padding: 1rem;
                    position: fixed;
                    top: 60px;
                    right: 10px;
                    border-radius: 10px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                    z-index: 1;
                }
                
                .nav__links.active {
                    display: flex;
                }
                }    
        .container {
            max-width: 600px;
            margin: 50px auto;
            font-family: Arial, sans-serif;
        }
        .card {
            background-color: white;
            padding: 20px;
            margin: 10px 0;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .card h3 {
            margin: 0 0 10px;
        }
    </style>
     <script>
    document.addEventListener('DOMContentLoaded', function() {
      const toggleButton = document.querySelector('.fa-bars');
      const navLinks = document.querySelector('.nav__links');

      toggleButton.addEventListener('click', function() {
        navLinks.classList.toggle('active');
      });
    });
  </script>
</head>
<body>
<nav>
        <div class="nav__logo">QueuePro</div>
        <ul class="nav__links">
            <li class="link"><a href="../index.php">Home</a></li>
            <li class="link"><a href="makeappointment.php">Make Appointment</a></li>
            <li class="link"><a href="verify_email.php">View Appointment</a></li>
            <li class="link"><a href="../queue/viewqueue.php">View Queue</a></li>
            <li class="link"><a href="viewdoctors.php">View Doctor Details</a></li>
        </ul>
        <i class="fa-solid fa-bars" style="color: #74C0FC;"></i>
      </nav>
    <div class="container">
        <h1>Your Appointments</h1>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='card'>";
                echo "<h3>Patient: " . htmlspecialchars($row['PatientFirstName']) . " " . htmlspecialchars($row['PatientLastName']) . "</h3>";
                echo "<p>Doctor: Dr. " . htmlspecialchars($row['DoctorFirstName']) . " " . htmlspecialchars($row['DoctorLastName']) . "</p>";
                echo "<p>Start Time: " . htmlspecialchars($row['Start_Time']) . "</p>";
                echo "<p>Room Number: " . htmlspecialchars($row['Room_Number']) . "</p>";
                echo "</div>";
            }
        } else {
            echo "<p>No appointments found.</p>";
        }
        ?>
    </div>
</body>
</html>
<?php
$stmt->close();
$con->close();
?>
