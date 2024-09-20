<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Generate OTP
    $otp = rand(000000, 999999);

    // Store OTP in session for later verification
    $_SESSION['otp'] = $otp;
    $_SESSION['email'] = $email;

    // Prepare data for JavaScript
    $email_js_data = json_encode([
        'email' => $email,
        'otp' => $otp
    ]);

    // Indicate that the OTP has been generated
    $otp_generated = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email with OTP</title>
    <link rel="title icon" href="../pictures/logo.png">
    <!-- <link rel="stylesheet" href="style.css" /> -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.4.0/fonts/remixicon.css" rel="stylesheet" />
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>
    <!-- <link rel="stylesheet" href="main.css" /> -->
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #e9ecef;
            position: relative;
        }

        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .welcome-message {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            font-size: 1em;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        .form-group {
            margin-top: 20px;
        }

        label {
            margin-bottom: 5px;
            font-weight: 600;
            color: #333333;
        }

        input {
            margin-top: 5px;
            padding: 12px;
            font-size: 1em;
            border: 1px solid #ced4da;
            border-radius: 5px;
            transition: border-color 0.3s ease-in-out;
        }

        input:focus {
            border-color: #80bdff;
            outline: none;
        }

        button {
            margin-top: 20px;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease-in-out;
        }

        button:hover {
            background-color: #0056b3;
        }

        button:active {
            background-color: #004494;
        }
    </style>
</head>
<body>
<?php if(isset($_COOKIE["first_name"]) && isset($_COOKIE["last_name"])): ?>
        <div class="welcome-message">
            Hello, <?php echo htmlspecialchars($_COOKIE["first_name"]); ?> <?php echo htmlspecialchars($_COOKIE["last_name"]); ?>! Enter your Email for Verification
        </div>
    <?php else: ?>
        <div class="welcome-message">
            Hello Enter your Email for Verification!
        </div>
    <?php endif; ?>
    <section id="verify-email" class="container">
        <form method="post" action="verify_email.php">
            <div class="form-group">
                <label for="email">Enter Your Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <button type="submit">Send OTP</button>
        </form>
    </section>

    <?php if (isset($otp_generated) && $otp_generated): ?>
        <script type="text/javascript">
            (function(){
                emailjs.init('me2rjZpK5KnqfbBdm');
            })();

            let emailData = <?php echo $email_js_data; ?>;
            let params = {
                to_email: emailData.email,
                subject: 'OTP Verification',
                message:  emailData.otp
            };

            emailjs.send('service_6l79cl3', 'template_r15480d', params)
                .then(function(response) {
                    alert('OTP sent to your email!');
                    window.location.href = 'verify_otp.php';
                }, function(error) {
                    alert('Failed to send OTP. Please try again later.');
                });
        </script>
    <?php endif; ?>
</body>
</html>
