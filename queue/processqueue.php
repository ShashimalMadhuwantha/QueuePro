<?php
require '../connections/connectionpdo.php';

$scheduleID = $_GET['scheduleID'] ?? '';
$clinicID = $_GET['clinicID'] ?? '';
$startTime = $_GET['startTime'] ?? '';
$duration = $_GET['duration'] ?? '';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}

// Fetch ongoing queue numbers
$query = "
    SELECT Ongoing_Queue_Number
    FROM viewqueue
    WHERE CID = :clinicID AND SID = :scheduleID
    ORDER BY Ongoing_Queue_Number
";
$stmt = $pdo->prepare($query);
$stmt->execute(['clinicID' => $clinicID, 'scheduleID' => $scheduleID]);
$data = $stmt->fetchAll();

// Fetch patient count
$appointmentQuery = "
    SELECT COUNT(*) AS patient_count
    FROM appointment
    WHERE Clinic_ID = :clinicID AND Schedule_ID = :scheduleID
";
$appointmentStmt = $pdo->prepare($appointmentQuery);
$appointmentStmt->execute(['clinicID' => $clinicID, 'scheduleID' => $scheduleID]);
$appointmentData = $appointmentStmt->fetch();
$patient_count = $appointmentData['patient_count'];

// Fetch current state
$clinicQuery = "
    SELECT Current_State
    FROM clinic
    WHERE CID = :clinicID
";
$clinicStmt = $pdo->prepare($clinicQuery);
$clinicStmt->execute(['clinicID' => $clinicID]);
$clinicData = $clinicStmt->fetch();
$current_state = $clinicData['Current_State'];

// Generate queue numbers based on patient count
$queueNumbers = range(1, $patient_count);

// Prepare a set of ongoing queue numbers for easy lookup
$ongoingQueueNumbers = array_column($data, 'Ongoing_Queue_Number');

// Parse the duration and convert it to minutes
preg_match('/(\d+)\s*Hours/', $duration, $matches);
$durationInMinutes = isset($matches[1]) ? (int)$matches[1] * 60 : 0;

// Calculate the average time per appointment
$averageTimePerAppointment = $patient_count > 0 ? floor($durationInMinutes / $patient_count) : 0;

// Calculate estimated times
$startDateTime = new DateTime($startTime);
$estimatedTimes = [];
foreach ($queueNumbers as $number) {
    $estimatedTime = clone $startDateTime;
    $estimatedTime->modify('+' . ($number - 1) * $averageTimePerAppointment . ' minutes');
    $estimatedTimes[$number] = $estimatedTime->format('H:i');
}

// Calculate the last queue number from the patient count
$lastQueueNumber = $patient_count;


// Update clinic status
if (count($ongoingQueueNumbers) > 0 && max($ongoingQueueNumbers) >= $lastQueueNumber) {
    // All patients have been served
    $queryupdatestatus = "UPDATE clinic SET Current_State = 'Completed' WHERE CID = :clinicID AND SID = :scheduleID";
    $stmt5 = $pdo->prepare($queryupdatestatus);
    $stmt5->execute(['clinicID' => $clinicID, 'scheduleID' => $scheduleID]);
} else if (count($ongoingQueueNumbers) > 0 && max($ongoingQueueNumbers) < $lastQueueNumber) {
    // Some patients are still being served
    $queryupdatestatus1 = "UPDATE clinic SET Current_State = 'OnGoing' WHERE CID = :clinicID AND SID = :scheduleID";
    $stmt6 = $pdo->prepare($queryupdatestatus1);
    $stmt6->execute(['clinicID' => $clinicID, 'scheduleID' => $scheduleID]);
} 

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="120">
    <title>Queue</title>
    <link rel="title icon" href="../pictures/logo.png">
    <!-- <link rel="stylesheet" href="style.css" /> -->
    <link rel="stylesheet" href="queue.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap");

        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            font-family: "Roboto", sans-serif;
        }

        body {
            background-color: #f4f5f7;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            padding: 2rem;
        }

        .queue-item {
            display: flex;
            align-items: center;
            background-color: #fff;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.5s ease forwards;
            position: relative;
        }

        .queue-item:not(:last-child) {
            margin-bottom: 1rem;
        }

        .queue-item.current {
            background-color: #0056b3;
            border-left: 5px solid #4b70f5;
            text-align: center;
        }

        .queue-item.ongoing {
            background-color: #e7f0ff;
            border-left: 5px solid #4b70f5;
            align-items: center;
            justify-content: center;
            height: 100px;
        }

        .queue-number {
            font-size: 2rem;
            font-weight: 700;
            color: #4b70f5;
            margin-right: 1rem;
        }

        .queue-time {
            font-size: 1rem;
            font-weight: 500;
            color: #fff;
            background-color:  #4b70f5;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            margin-left: auto;
        }

        .ongoing-info {
            font-size: 2rem;
            font-family: "Roboto", sans-serif;
            color: #4b70f5;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .clinic-over {
            font-size: 2rem;
            font-family: "Roboto", sans-serif;
            color: #d9534f;
            text-align: center;
            margin-top: 2rem;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

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

        @media (max-width: 800px) {
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

        
        
        @media (max-width: 800px) {
            .container
            {
                width: 100%;

            }
            .queue-item {
            display: flex;
            align-items: center;
            background-color: #fff;
            padding: 0.5rem;
            margin-bottom: 0.5rem;
            border-radius: 6px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.5s ease forwards;
            
        }

        .queue-item:not(:last-child) {
            margin-bottom: 0.3rem;
        }

        .queue-item.current {
            background-color: #0056b3;
            border-left: 2px solid #4b70f5;
            text-align: center;
        }

        .queue-item.ongoing {
            background-color: #e7f0ff;
            border-left: 2px solid #4b70f5;
            align-items: center;
            justify-content: center;
            height: 60px;
        }

        .queue-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: #4b70f5;
            margin-right: 0.5rem;
        }

        .queue-time {
            font-size: 0.9rem;
            font-weight: 500;
            color: #fff;
            background-color:  #4b70f5;
            padding: 0.3rem 0.5rem;
            border-radius: 30px;
            margin-left: auto;
        }

        .ongoing-info {
            font-size: 1.5rem;
            font-family: "Roboto", sans-serif;
            color: #4b70f5;
            align-items: center;
            justify-content: center;
            text-align: center;
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
        <li class="link"><a href="../appointment/makeappointment.php">Make Appointment</a></li>
        <li class="link"><a href="../appointment/verify_email.php">View Appointment</a></li>
        <li class="link"><a href="viewqueue.php">View Queue</a></li>
        <li class="link"><a href="../appointment/viewdoctors.php">View Doctor Details</a></li>
    </ul>
    <i class="fa-solid fa-bars" style="color: #74C0FC;"></i>
</nav>
<div class="container">
    <?php if($patient_count == 0): ?>
        <!-- Display this message only when there are no patients -->
        <p class="clinic-over">No Patients Have been Appointed</p>
    <?php else: ?>
        <!-- Render the queue items if there are patients -->
        <?php foreach ($queueNumbers as $number): ?>
            <div class="queue-item <?php echo ($number == $current_state) ? 'current' : (in_array($number, $ongoingQueueNumbers) ? 'ongoing' : ''); ?>">
                <p class="queue-number"><?php echo htmlspecialchars($number); ?></p>
                <?php if (in_array($number, $ongoingQueueNumbers)): ?>
                    <p class="ongoing-info">Currently Being Served</p>
                <?php endif; ?>
                <p class="queue-time">Estimated: <?php echo htmlspecialchars($estimatedTimes[$number]); ?></p>
            </div>
        <?php endforeach; ?>
        <!-- Display the clinic over message if applicable -->
        <?php if (count($ongoingQueueNumbers) > 0 && max($ongoingQueueNumbers) >= $lastQueueNumber): ?>
            <p class="clinic-over">Clinic is over</p>
        <?php endif; ?>
    <?php endif; ?>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const queueItems = document.querySelectorAll(".queue-item");
        queueItems.forEach((item, index) => {
            item.style.animationDelay = `${index * 0.1}s`;
        });
    });
</script>
</body>
</html>
