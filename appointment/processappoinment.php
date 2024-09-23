<?php
require '../connections/connectionpdo.php';
require '../connections/connection.php';

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $doctorID = $_POST['doctorID'] ?? '';
    $scheduleID = $_POST['scheduleID'] ?? '';
    $clinicID = $_POST['clinicID'] ?? '';
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $city = $_POST['city'] ?? '';
    $nic = $_POST['nic'] ?? '';
    $email = $_POST['email'] ?? '';
    $contact_no = $_POST['contact_no'] ?? '';
    $age = $_POST['age'] ?? '';

    setcookie("first_name", $first_name, time() + 86400, "/", "", 0);
    setcookie("last_name", $last_name, time() + 86400, "/", "", 0);

    // Check if patient with the entered email or first name and last name already exists
    $stmt = $pdo->prepare("SELECT Patient_ID FROM patient WHERE NIC=?");
    $stmt->execute([$nic]);
    $existing_patient = $stmt->fetch();

    if ($existing_patient) {
        $patient_id = $existing_patient['Patient_ID'];
    } else {
        // Generate a new Patient_ID
        $stmt = $pdo->query("SELECT MAX(CAST(SUBSTRING(Patient_ID, 2) AS UNSIGNED)) AS max_id FROM patient");
        $row = $stmt->fetch();
        $patient_id = 'P' . str_pad($row['max_id'] + 1, 7, '0', STR_PAD_LEFT);

        // Insert new patient details into the patient table
        $stmt = $pdo->prepare("INSERT INTO patient (Patient_ID, First_Name, Last_Name, City, NIC, Email, Contact_NO, Age) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$patient_id, $first_name, $last_name, $city, $nic, $email, $contact_no, $age]);
    }


    // Generate auto-incremented Appointment_ID
    $stmt = $pdo->query("SELECT MAX(CAST(SUBSTRING(Appointment_ID, 3) AS UNSIGNED)) AS max_id FROM appointment");
    $row = $stmt->fetch();
    $appointment_id = 'AP' . str_pad($row['max_id'] + 1, 7, '0', STR_PAD_LEFT);

    // Get current date and time
    $date_and_time = date('Y-m-d H:i:s');

    // Insert appointment details into the appointment table
    $stmt = $pdo->prepare("INSERT INTO appointment (Appointment_ID, Patient_ID, Schedule_ID, Clinic_ID, Doctor_ID, Date_and_Time) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$appointment_id, $patient_id, $scheduleID, $clinicID, $doctorID, $date_and_time]);

    // Get the current highest queue number for the clinic
    $stmt = $pdo->prepare("SELECT MAX(QueueNo) AS max_queue FROM queue WHERE CID = ? AND SID = ?");
    $stmt->execute([$clinicID, $scheduleID]);
    $row = $stmt->fetch();

    // Increment the max_queue and add two leading zeros
    $queue_number = sprintf('%03d', $row['max_queue'] + 1);

    // Incrementing Queue ID
    $query_max_id = "SELECT MAX(SUBSTRING(QID, 2)) AS max_id FROM queue WHERE QID LIKE 'Q%'";
    $result = $pdo->query($query_max_id);
    $row = $result->fetch();
    $max_id = $row['max_id'];

    if ($max_id === null) {
        $next_id = 'Q001';
    } else {
        $next_id = 'Q' . str_pad((int)$max_id + 1, 3, '0', STR_PAD_LEFT);
    }

    // Insert the queue details into the queue table
    $stmt = $pdo->prepare("INSERT INTO queue (QID, CID, SID, PID, QueueNo) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$next_id, $clinicID, $scheduleID, $patient_id, $queue_number]);

    // Generate QR code containing CID, SID, and QueueNo
    $qr_content = "CID={$clinicID}&SID={$scheduleID}&QueueNo={$queue_number}";
    $qr_code_url = "https://api.qrserver.com/v1/create-qr-code/?size=350x350&data=" . urlencode($qr_content);

    // Fetch additional details for the email
    $stmt = $pdo->prepare("SELECT d.First_Name AS first_name, d.Last_Name AS last_name, c.Date, c.Room_Number, c.Start_Time
    FROM clinic c
    JOIN schedule s ON c.SID = s.SID
    JOIN doctorlogin d ON s.DID = d.DID
    WHERE c.CID = ? AND c.SID = ?");
    $stmt->execute([$clinicID, $scheduleID]);
    $clinic_details = $stmt->fetch();


    $formatted_contact_no = preg_replace('/^0/', '94', $contact_no);

    // Prepare SMS content
    $text = urlencode("Dear $first_name $last_name,\nYour appointment is confirmed with Doctor " . $clinic_details['first_name'] . ' ' . $clinic_details['last_name'] . "\nDate: " . $clinic_details['Date'] . " at " . $clinic_details['Start_Time'] . " \n Room Number: " . $clinic_details['Room_Number'] . ".\n Your Waiting Number is: " . $queue_number . ".\n Qr Code link:$qr_code_url "."\nBest Regards,\nQueuePro");

    // Send SMS
    $user = "94783522092";  
    $password = "6362";     
    $num = $formatted_contact_no;
    $baseurl = "https://www.textit.biz/sendmsg";
    $url = "$baseurl/?id=$user&pw=$password&to=$num&text=$text";
    $ret = file($url);

    // Prepare data for EmailJS
    $email_js_data = json_encode([
        'email' => $email,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'queue_number' => $queue_number,
        'doctor_name' => $clinic_details['first_name'] . ' ' . $clinic_details['last_name'],
        'clinic_date' => $clinic_details['Date'],
        'room_number' => $clinic_details['Room_Number'],
        'start_time' => $clinic_details['Start_Time']
    ]);

    
} else {
    // Retrieve query parameters from URL
    $doctorID = $_GET['doctorID'] ?? '';
    $scheduleID = $_GET['scheduleID'] ?? '';
    $clinicID = $_GET['clinicID'] ?? '';
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make an Appointment</title>
    <link rel="title icon" href="../pictures/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.4.0/fonts/remixicon.css" rel="stylesheet"/>
    <link rel="stylesheet" href="../css/main.css" />
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <script>
    document.addEventListener('DOMContentLoaded', function() {
      const toggleButton = document.querySelector('.fa-bars');
      const navLinks = document.querySelector('.nav__links');

      toggleButton.addEventListener('click', function() {
        navLinks.classList.toggle('active');
      });
    });
  </script>
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
            (function(){
                emailjs.init('me2rjZpK5KnqfbBdm');
            })();

            <?php if (isset($email_js_data)) { ?>
            let emailData = <?php echo $email_js_data; ?>;
            let params = {
                toemail: emailData.email,
                toname: emailData.first_name + ' ' + emailData.last_name,
                queuenumber: emailData.queue_number,
                doctorname: emailData.doctor_name,
                clinicdate: emailData.clinic_date,
                roomnumber: emailData.room_number,
                starttime: emailData.start_time,
                qrcode: "<?php echo $qr_code_url; ?>"
            };

            emailjs.send('service_6l79cl3', 'template_6nalxfc', params)
                .then(function(response) {

                    window.location.href = 'success.php';
                }, function(error) {
                    alert('Failed to send details. Please try again later.');
                });
            <?php } ?>
        });
    </script>
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
    </style>
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
<div class="appointment-form">
    <h2>Make an Appointment</h2>
    <form action="#" method="POST">
        <input type="hidden" name="doctorID" value="<?php echo htmlspecialchars($doctorID); ?>">
        <input type="hidden" name="scheduleID" value="<?php echo htmlspecialchars($scheduleID); ?>">
        <input type="hidden" name="clinicID" value="<?php echo htmlspecialchars($clinicID); ?>">

        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" required>

        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" required>

        <label for="city">City:</label>
        <input type="text" id="city" name="city" required>

        <label for="nic">NIC:</label>
        <input type="text" id="nic" name="nic" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>


        <label for="contact_no">Contact No:</label>
        <input type="text" id="contact_no" name="contact_no" required>

        <label for="age">Age:</label>
        <input type="number" id="age" name="age" required>

        <button type="submit" name="submit">Submit</button>
    </form>
</div>
</body>


<?php
if(isset($_POST["submit"])){

    $updateQuery = "UPDATE clinic SET P_Limit = P_Limit - 1 WHERE CID = '$clinicID'";
    if (mysqli_query($con, $updateQuery)) {
        if ($updateQuery && mysqli_affected_rows($con) > 0) {
            
        } else {
        
        }
    } else {
        echo "Error: " . mysqli_error($con);
    }
    mysqli_close($con);
}
?>