<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_otp = $_POST['enteredOTP'];

    if ($entered_otp == $_SESSION['otp']) {
        // OTP verified
        header("Location: view_appointments.php");
        exit();
    } else {
        $error = "Invalid OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link rel="title icon" href="../pictures/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.4.0/fonts/remixicon.css" rel="stylesheet" />
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f0f0;
            font-family: 'Roboto', sans-serif;
        }

        .otp-Form {
            width: 220px;
            height: 300px;
            background-color: rgb(255, 255, 255);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px 30px;
            gap: 20px;
            position: relative;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.082);
            border-radius: 15px;
        }

        .mainHeading {
            font-size: 1.5em;
            color: rgb(15, 15, 15);
            font-weight: 700;
        }

        .otpSubheading {
            font-size: 1rem;
            color: black;
            line-height: 17px;
            text-align: center;
        }

        .inputContainer {
            width: 100%;
            display: flex;
            flex-direction: row;
            gap: 10px;
            align-items: center;
            justify-content: center;
        }

        .otp-input {
            background-color: rgb(228, 228, 228);
            width: 180px;
            height: 30px;
            text-align: center;
            border: none;
            border-radius: 7px;
            caret-color: rgb(127, 129, 255);
            color: rgb(44, 44, 44);
            outline: none;
            font-weight: 600;
        }

        .otp-input:focus,
        .otp-input:valid {
            background-color: rgba(127, 129, 255, 0.199);
            transition-duration: .3s;
        }

        .verifyButton {
            width: 100%;
            height: 30px;
            border: none;
            background-color: #007bff;
            color: white;
            font-weight: 600;
            cursor: pointer;
            border-radius: 10px;
            transition-duration: .2s;
        }

        .verifyButton:hover {
            background-color: blue;
            transition-duration: .2s;
        }


        .error-message {
            color: red;
            font-size: 0.8em;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <form class="otp-Form" method="POST" action="">
        <span class="mainHeading">Enter OTP</span>
        <p class="otpSubheading">We have sent a verification code to your Email Address</p>
        <div class="inputContainer">
            <input required="required" maxlength="6" type="text" class="otp-input" name="enteredOTP" id="otp-input1">        </div>
        <button class="verifyButton" type="submit">Verify</button>
    </form>

    <?php if (isset($error)) echo "<p class='error-message'>$error</p>"; ?>
</div>
</body>
</html>
