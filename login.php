<?php
session_start();
require 'connections/connection.php';

if (isset($_POST["btnlogin"])) {
    $uname = $_POST["txtusername"];
    $pass = $_POST["txtpass"];
   
    

    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Admin login check
    $query = "SELECT * FROM adminlogin WHERE Username='$uname'";
    $result = mysqli_query($con, $query);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        $blocked = $row['blocked'];

        if ($blocked) 
        {
            echo "<script>alert('Your account is blocked. Please contact the administrator.')</script>";
        } 
        else 
        {
            // Check credentials
            if ($row['Password'] === $pass) 
            {
                $_SESSION["Username"] = $uname;
                $_SESSION["UserID"] = $row['AID'];
                $_SESSION["time"] = time();

                header("Location: admin/AdminPanel.php");
                exit();
            } 
            else
            {
                // Increment login attempts
                $query = "UPDATE adminlogin SET login_attempts = login_attempts + 1 WHERE Username='$uname'";
                mysqli_query($con, $query);

                // Check if login attempts exceed limit
                $query = "SELECT login_attempts FROM adminlogin WHERE Username='$uname'";
                $result = mysqli_query($con, $query);
                $row = mysqli_fetch_assoc($result);
                $login_attempts = $row['login_attempts'];

                if ($login_attempts >= 3) 
                {
                    // Block the user
                    $query = "UPDATE adminlogin SET blocked = TRUE WHERE Username='$uname'";
                    mysqli_query($con, $query);
                    echo "<script>alert('Your account has been blocked due to too many failed login attempts. Please contact the administrator.')</script>";
                } 
                else 
                {
                    echo "<script>alert('Invalid Username or Password')</script>";
                }
            }
        }
    } 
    else 
    {
        // Doctor login check
        $query = "SELECT * FROM doctorlogin WHERE First_Name='$uname'";
        $result = mysqli_query($con, $query);

        if ($result && $row = mysqli_fetch_assoc($result)) 
        {
            if ($row['Password'] === $pass) 
            {
                $_SESSION["FName"] = $uname;
                $_SESSION["UserDID"] = $row['DID'];
                $_SESSION["time"] = time();

                header("Location: doctor/doctorpanel.php");
                exit();
            }
            else 
            {
                // Increment login attempts
                $query = "UPDATE doctorlogin SET login_attempts = login_attempts + 1 WHERE First_Name='$uname'";
                mysqli_query($con, $query);

                // Check if login attempts exceed limit
                $query = "SELECT login_attempts FROM doctorlogin WHERE First_Name='$uname'";
                $result = mysqli_query($con, $query);
                $row = mysqli_fetch_assoc($result);
                $login_attempts = $row['login_attempts'];

                if ($login_attempts >= 3) 
                {
                    // Redirect to contact administrator page
                    header("Location: doctor/passwordreset.html");
                    exit();
                } 
                else 
                {
                    echo "<script>alert('Invalid Username or Password')</script>";
                }
            }
        } 
        else 
        {
            echo "<script>alert('Invalid Username or Password')</script>";
        }
    }

    mysqli_close($con);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="title icon" href="pictures/logo.png">
    <link rel="stylesheet" type="text/css" href="css/styleadminlogin.css">
    <link rel="stylesheet" type="text/css" href="bootstraplibraries/css/bootstrap.min.css">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</head>
<body>
    <section>
        <form action="#" method="post" name="frmadminLogin">
            <h1>Login</h1>
            <div class="inputbox">
                <ion-icon name="mail-outline"></ion-icon>
                <input type="text" name="txtusername" id="txtusername" required placeholder=" ">
                <label for="txtusername">Username</label>
            </div>
            <div class="inputbox">
                <ion-icon name="lock-closed-outline"></ion-icon>
                <input type="password" name="txtpass" id="txtpass" required placeholder=" ">
                <label for="txtpass">Password</label>
                <ion-icon name="eye-off-outline" class="eye-icon" id="togglePassword"></ion-icon>
            </div>
            <button name="btnlogin" type="submit" class="btn" id="btnlogin" disabled><span>Login</span></button>
        </form>
    </section>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#txtpass');
        const username = document.querySelector('#txtusername');
        const loginBtn = document.querySelector('#btnlogin');

        togglePassword.addEventListener('click', function (e) 
        {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.setAttribute('name', type === 'password' ? 'eye-off-outline' : 'eye-outline');
            this.classList.toggle('rotate');
        });

        function checkInputs() 
        {
            const usernameValue = username.value.trim();
            const passwordValue = password.value.trim();

            if (usernameValue !== '' && passwordValue !== '')
            {
                loginBtn.classList.add('show');
                loginBtn.removeAttribute('disabled');
            } else {
                loginBtn.classList.remove('show');
                loginBtn.setAttribute('disabled', 'true');
            }
        }

        username.addEventListener('input', checkInputs);
        password.addEventListener('input', checkInputs);
    </script>
</body>
</html>
