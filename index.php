<?php
require 'connections/connection.php';



if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$query = "SELECT COUNT(DISTINCT Patient_ID) as patient_count FROM appointment WHERE DATE(Date_and_Time) = CURDATE()";
$result = mysqli_query($con, $query);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $patient_count = $row['patient_count'];
} else {
    $patient_count = 0;
}

mysqli_close($con);
?>




<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>QueuePro</title>
  <link rel="title icon" href="pictures/logo.png">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.4.0/fonts/remixicon.css" rel="stylesheet">
  
  <style>
    @import url("https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap");

* {
  padding: 0;
  margin: 0;
  box-sizing: border-box;
  font-family: "Roboto", sans-serif;
}

.container {
  max-width: 1200px;
  margin: auto;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.header .content .btn-primary {
  padding: 0.75em 1.5em;
  background: #007bff;
  color: #fff;
  text-decoration: none;
  border-radius: 5px;
  font-size: 1em;
  transition: background 0.3s ease;
}

.header .content .btn-primary:hover {
  background: #0056b3;
}

nav {
  padding: 2rem 1rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 2rem;
  position: relative;
}

.nav__logo {
  font-size: 1.7rem;
  font-weight: 600;
  color: #4b70f5;
  cursor: default;
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

.header {
  padding: 0 1rem;
  flex: 1;
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 2rem;
  align-items: center;
}

.content h1 {
  margin-bottom: 1rem;
  font-size: 3.2rem;
  font-weight: 700;
  color: #111827;
}

.content h1 b {
  color: #4b70f5;
}

.image {
  position: relative;
  text-align: center;
  isolation: isolate;
}

.image__bg {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  height: 450px;
  width: 450px;
  background-color: #4b70f5;
  border-radius: 100%;
  z-index: -100;
}

.image img {
  width: 100%;
  max-width: 470px;
}

.image__content {
  position: absolute;
  top: 50%;
  left: 50%;
  padding: 1rem 2rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  text-align: left;
  background-color: #ffffff;
  border-radius: 5px;
  box-shadow: 5px 5px 20px rgba(0, 0, 0, 0.2);
}

.image__content__1 {
  transform: translate(calc(-50% - 12rem), calc(-50% - 8rem));
}

.image__content__1 span {
  padding: 10px 12px;
  font-size: 1.5rem;
  color: #4b70f5;
  background-color: #defcf4;
  border-radius: 100%;
}

.image__content__1 h4 {
  font-size: 1.5rem;
  font-weight: 600;
  color: #111827;
}

.image__content__1 p {
  color: #6b7280;
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

  .header {
    grid-template-columns: 1fr;
    text-align: center;
  }

  .header {
    position:relative;
    padding: 1rem;
    grid-template-columns: repeat(1, 1fr);
  }

  .content {
    text-align: center;
  }

  .image {
    grid-area: 1/1/2/2;
  }


  .image {
  position: relative;
  text-align: center;
  isolation: isolate;
}

.image__bg {
  position: absolute;
  top: 55%;
  left: 50%;
  transform: translate(-50%, -50%);
  height: 100%;
  width: 100%;
  background-color: #4b70f5;
  border-radius: 100%;
  z-index: -1;
}

.image img {
  width: 95%;
}

.image__content {
  position: absolute;
  top: 50%;
  left: 50%;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  text-align: left;
  background-color: #ffffff;
  border-radius: 5px;
  box-shadow: 5px 5px 20px rgba(0, 0, 0, 0.2);
  padding: 20;
}

.image__content__1 {
  transform: translate(calc(-50% - 12rem), calc(-50% - 9rem));
  left: 70%;
  width: 40%;
  top: 40%;
}

.image__content__1 span {
  padding: 5px;
  font-size: 1.5rem;
  color: #4b70f5;
  background-color: #defcf4;
  border-radius: 100%;
  z-index: 1;
}

.image__content__1 h4 {
  font-size: 1rem;
  font-weight: 900;
  color: #111827;
}

.image__content__1 p {
  color: #6b7280;
  font-size: 0.9rem;
}
}

footer {
  background: #57584f;
  color: #fff;
  text-align: center;
  padding: 1.5rem 0;
  width: 100%;
  margin-top: 2rem;
}

.footer-content {
  margin-bottom: 1rem;
}

.footer-content h3 {
  font-size: 1.8rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.footer-content p {
  font-size: 1rem;
  margin-bottom: 1rem;
}

.socials {
  list-style: none;
  padding: 0;
  display: flex;
  justify-content: center;
  gap: 1rem;
}

.socials li {
  display: inline;
}

.socials a {
  color: #fff;
  font-size: 1.2rem;
  text-decoration: none;
  transition: color 0.3s;
}

.socials a:hover {
  color: #4b70f5;
}

.footer-bottom {
  margin-top: 1rem;
  font-size: 0.9rem;
  color: #bbb;
}

.footer-bottom p {
  margin: 0;
}

@media (max-width: 600px) {
  .footer-content h3 {
    font-size: 1.5rem;
  }

  .footer-content p {
    font-size: 0.9rem;
  }

  .socials {
    display: flex;
    flex-direction: row;
    align-items: center;
  }

  .socials li {
    margin-bottom: 0.5rem;
  }

  .footer-bottom {
    font-size: 0.8rem;
  }
}
/* Chatbox Icon Styles */
.chatbox-icon {
  position: fixed;
  bottom: 1rem;
  right: 1rem;
  background: #007bff;
  color: #fff;
  border-radius: 50%;
  padding: 1rem;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
  cursor: pointer;
  font-size: 2rem;
  z-index: 1000;
}
.chatbox-icon {
      position: fixed;
      bottom: 1rem;
      right: 1rem;
      background: #007bff;
      color: #fff;
      border-radius: 50%;
      padding: 1rem;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
      cursor: pointer;
      font-size: 2rem;
      z-index: 1000;
    }

    /* Chatbox Card Styles */
    .chatbox-card {
      position: fixed;
      bottom: 0;
      right: 0;
      width: 100%;
      max-width: 600px;
      height: 400px;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
      transform: translateY(100%);
      transition: transform 0.3s ease;
      z-index: 1;
    }

    .chatbox-card iframe {
      width: 100%;
      height: 100%;
      border: none;
    }

    .chatbox-card.show {
      transform: translateY(0);
    }

    .chatbox-close {
      position: absolute;
      top: 10px;
      right: 10px;
      background: #007bff;
      color: #fff;
      border: none;
      border-radius: 50%;
      padding: 0.5rem;
      cursor: pointer;
      font-size: 1.5rem;
    }

    @media (max-width: 800px) {
      .chatbox-icon {
        font-size: 1.2rem;
        padding: 0.5rem;
      }
      .chatbox-card iframe {
      width: 100%;
      height: 100%;
      border: none;
    }

      .chatbox-card {
        height: 60%;
        width: 100%;

      }

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

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const chatboxIcon = document.getElementById('chatbox-icon');
    const chatboxCard = document.getElementById('chatbox-card');
    const chatboxClose = document.querySelector('.chatbox-close');

    chatboxIcon.addEventListener('click', function() {
      chatboxCard.classList.toggle('show');
    });

    chatboxClose.addEventListener('click', function() {
      chatboxCard.classList.remove('show');
    });
  });
</script>

</head>
<body>
  <div class="container">
    <nav>
      <div class="nav__logo">QueuePro</div>
      
      <ul class="nav__links">
        <li class="link"><a href="index.php">Home</a></li>
        <li class="link"><a href="appointment/makeappointment.php">Make Appointment</a></li>
        <li class="link"><a href="appointment/verify_email.php">View Appointment</a></li>
        <li class="link"><a href="queue/viewqueue.php">View Queue</a></li>
        <li class="link"><a href="appointment/viewdoctors.php">View Doctor Details</a></li>
      </ul>
      <i class="fa-solid fa-bars" style="color: #74C0FC;"></i>
    </nav>
    <header class="header">
      <div class="content">
        <h1>Find & Search Your <b>Favourite</b> Doctor</h1>
        <p>Your health is our priority. Connect with the best doctors in the industry.</p><br>
        <a href="appointment/makeappointment.php" class="btn-primary">Make an Appointment</a>
      </div>
      <div class="image">
        <div class="image__bg"></div>
        <img src="pictures/heroimg-removebg-preview.png" alt="hero image">
        <div class="image__content image__content__1">
          <span><i class="ri-user-3-line"></i></span>
          <div class="details">
            <h4><?php echo $patient_count?></h4>
            <p>Active Patients</p>
          </div>
        </div>
        
      </div>
    </header>
  </div>

  <footer>
    <div class="footer-content">
      <h3>QueuePro</h3>
      <p>Connecting you with the best healthcare providers. Your health is our priority.</p>
      <ul class="socials">
        <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
        <li><a href="#"><i class="fab fa-twitter"></i></a></li>
        <li><a href="#"><i class="fab fa-instagram"></i></a></li>
        <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
        <li><a href="#"><i class="fab fa-youtube"></i></a></li>
      </ul>
    </div>
    <div class="footer-bottom">
      <p>&copy; 2024 QueuePro. All rights reserved.</p>
    </div>
  </footer>
  <!-- Start of LiveChat (www.livechat.com) code -->
<script>
    window.__lc = window.__lc || {};
    window.__lc.license = 18559068;
    window.__lc.integration_name = "manual_onboarding";
    window.__lc.product_name = "livechat";
    ;(function(n,t,c){function i(n){return e._h?e._h.apply(null,n):e._q.push(n)}var e={_q:[],_h:null,_v:"2.0",on:function(){i(["on",c.call(arguments)])},once:function(){i(["once",c.call(arguments)])},off:function(){i(["off",c.call(arguments)])},get:function(){if(!e._h)throw new Error("[LiveChatWidget] You can't use getters before load.");return i(["get",c.call(arguments)])},call:function(){i(["call",c.call(arguments)])},init:function(){var n=t.createElement("script");n.async=!0,n.type="text/javascript",n.src="https://cdn.livechatinc.com/tracking.js",t.head.appendChild(n)}};!n.__lc.asyncInit&&e.init(),n.LiveChatWidget=n.LiveChatWidget||e}(window,document,[].slice))
</script>
<noscript><a href="https://www.livechat.com/chat-with/18559068/" rel="nofollow">Chat with us</a>, powered by <a href="https://www.livechat.com/?welcome" rel="noopener nofollow" target="_blank">LiveChat</a></noscript>
<!-- End of LiveChat code -->

</body>
</html>
