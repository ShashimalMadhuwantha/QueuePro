<?php
session_start();
require '../connections/connection.php';
if (!isset($_SESSION['UserDID'])) {
    // Redirect to login page if not logged in
    header("Location: doctorlogout.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Panel</title>
    <link rel="title icon" href="../pictures/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/navstyle.css">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <style>
        * {
        font-family: 'Roboto', sans-serif;
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        background-color: #f4f9fc; /* Light blue background for the body */
        color: #333;
        font-size: 16px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
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



    .profile {
        margin-top: 80px;
        padding: 40px;
        width: 100%;
        max-width: 900px;
        background: linear-gradient(to right, #ffffff, #e0f7fa); /* Gradient background */
        border-radius: 15px; /* Rounded corners */
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); /* Enhanced shadow */
        color: #333;
    }

    .card {
        display: flex;
        flex-direction: row;
        align-items: center;
        background: #ffffff; /* White background for the card */
        border-radius: 15px; /* Rounded corners */
        padding: 30px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: box-shadow 0.3s;
        min-height: 300px; /* Adjusted height */
        border: 2px solid #003366; /* Dark blue border */
    }

    .card:hover {
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
    }

    .card-content {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 20px;
        width: 100%;
    }

    .card-left {
        flex: 1;
        display: flex;
        justify-content: center;
    }

    .card-left .doctor-photo {
        border-radius: 50%;
        border: 4px solid #003366; /* Dark blue border for the photo */
        width: 150px;
        height: 150px;
        object-fit: cover;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Shadow for the photo */
    }

    .card-right {
        flex: 2;
    }

    .card-right h3 {
        margin-bottom: 15px;
        color: #003366; /* Dark blue for the card heading */
        font-size: 26px;
        font-weight: 700;
    }


.card-right p {
        margin: 10px 0;
        font-size: 18px;
        color: #003366; /* Dark blue for the text */
    }

    .card-right strong {
        color: #003366; /* Dark blue for the strong text */
    }

    @media (max-width: 768px) {
        .nav-toggle {
            display: block;
        }

        .nav-items ul {
            display: none;
            flex-direction: column;
            width: 100%;
        }

        .nav-items ul.show {
            display: flex;
        }

        .nav-item {
            margin: 0;
            margin-bottom: 10px;
        }

        .nav-link {
            text-align: center;
            padding: 10px;
        }

        .card {
            flex-direction: column;
            min-height: auto; /* Allow card height to adjust in column layout */
        }

        .card-content {
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }

        .card-right {
            text-align: center;
        }
    }
    </style>
    <script>
        function toggleNav() {
            const navItems = document.querySelector('.nav-items ul');
            navItems.classList.toggle('show');
        }
    </script>
</head>
<body>
    <div class="nav">
        <div class="head">
            <h2>Doctor Panel</h2>
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
    <?php
    // Fetch doctor's details
    $doctorID = $_SESSION['UserDID'];
    $sql = "SELECT * FROM doctorlogin WHERE DID = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $doctorID);
    $stmt->execute();
    $result = $stmt->get_result();
    $doctor = $result->fetch_assoc();
    ?>
    <br><br><br>
    <div class="profile">
        <div class="card">
            <div class="card-content">
                <div class="card-left">
                    <img src="<?php echo htmlspecialchars($doctor['Photo']); ?>" alt="Doctor Photo" class="doctor-photo">
                </div>
                <div class="card-right">
                    <h3>DOCTOR PROFILE</h3>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($doctor['First_Name'] . " " . htmlspecialchars($doctor['Last_Name'])) ?></p>
                    <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($doctor['Contact_No']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($doctor['Email']); ?></p>
                    <p><strong>Specialization:</strong> <?php echo htmlspecialchars($doctor['Specialization']); ?></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
