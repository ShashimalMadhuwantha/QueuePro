<?php

session_start();

// Check if the user is logged in and is user ID 'A001'
if (!isset($_SESSION['UserID'])) {
    // Redirect to login page if not logged in
    header("Location: ../admin/Adminlogout.php");
    exit(); 
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Scanner</title>
    <script src="../js/ht.js"></script>
    <link rel="title icon" href="../pictures/logo.png">
        <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="../css/navstyle.css">
    
    <link rel="stylesheet" type="text/css" href="../bootstraplibraries/css/bootstrap.min.css">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script>
        function toggleNav() {
            var nav = document.querySelector('.nav-items ul');
            nav.classList.toggle('show');
        }
    </script>
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

        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 0;
        }

        .nav__logo {
            font-size: 1.7rem;
            font-weight: 600;
            color: #4b70f5;
        }

        .nav__links {
            list-style: none;
            display: flex;
            gap: 1.5rem;
        }

        .nav__links .link a {
            text-decoration: none;
            color: #6b7280;
            font-size: 1rem;
            transition: color 0.3s ease;
        }

        .nav__links .link a:hover {
            color: #4b70f5;
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .header h1 {
            font-size: 2.5rem;
            color: #333;
        }

        .qr-scanner {
            text-align: center;
            margin-top: 50px;
        }

        #reader {
            width: 100%;
            max-width: 400px;
            margin: 20px auto;
            border: 2px solid #4b70f5;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 10px;
            background-color: #fff;
        }
    </style>
</head>
<body>
<div class="nav">
    <div class="head">
        <h2>Scanner</h2>
    </div>
    <div class="nav-toggle" onclick="toggleNav()">
        <ion-icon name="menu-outline"></ion-icon>
    </div>
    <div class="nav-items">
        <ul>

            <li class="nav-item"><a class="nav-link" href="../admin/AdminPanel.php">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="../admin/addadmins.php">Add Admins</a></li>
            <li class="nav-item"><a class="nav-link" href="../admin/adddoctors.php">Add Doctors</a></li>
            <li class="nav-item"><a class="nav-link" href="../admin/addclinics.php">Add Clinics</a></li>
            <li class="nav-item"><a class="nav-link" href="../admin/assingschedule.php">Assign Schedule</a></li>
            <li class="nav-item"><a class="nav-link" href="../admin/managelogins.php">Manage Logins</a></li>
            <li class="nav-item"><a class="nav-link" href="../qr/qrscanner.php">Scanner</a></li>
            <li class="nav-item"><a class="nav-link" href="../admin/Adminlogout.php">Logout</a></li>
        </ul>
    </div>
</div>
    <!-- QR Code Scanner Integration -->
    <div class="qr-scanner">
        <h2>Scan QR Code</h2>
        <div id="reader"></div>
    </div>

    <audio id="successAudio">
        <source src="../mp3/success.mp3" type="audio/mpeg">
    </audio>


    <script type="text/javascript">
        // Handle successful QR code scanning
        function onScanSuccess(decodedText, decodedResult) {
            console.log(`Code matched = ${decodedText}`, decodedResult);


            var successAudio = document.getElementById("successAudio");
            successAudio.play();


            // Send the decoded data to the server using AJAX
            fetch('process_qr.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ qr_data: decodedText })
            })
            .then(response => response.text())
            .then(data => {
               // console.log(data);
               // alert(data);
            })
            //.catch(error => console.error('Error:', error));
        }

        // Handle QR code scanning errors
        function onScanFailure(error) {
            console.warn(`Code scan error = ${error}`);
        }6

        // Initialize QR code scanner
        document.addEventListener("DOMContentLoaded", function() {
            // Check if getUserMedia is supported
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
                .then(function(stream) {
                    console.log("Camera access granted");
                    let html5QrcodeScanner = new Html5QrcodeScanner(
                        "reader", { fps: 10, qrbox: 250 }
                    );
                    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
                })
                .catch(function(err) {
                    console.error("Error accessing camera: ", err);
                    alert("Error accessing camera: " + err.message);
                });
            } else {
                console.error('getUserMedia not supported on your browser!');
                alert('Camera access is not supported on your browser.');
            }
        });

        
    </script>
</body>
</html>
