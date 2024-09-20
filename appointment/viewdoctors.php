
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctors</title>
    <link rel="title icon" href="../pictures/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- <link rel="stylesheet" href="style.css"> -->
    <script defer src="../js/doctorview.js"></script>
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

                    .card {
                    display: flex;
                    flex-direction: column;
                }

                .card-content {
                    padding: 0.5rem;
                    display: flex;
                    align-items: center;
                }

                .card-left {
                    flex: 0.5;
                }

                .card-left img.doctor-photo {
                    border-radius: 30%;
                    width: 80px;
                    height: 80px;
                    object-fit: cover;
                }

                .card-right {
                    flex: 1;
                    padding-left: 0.5rem;
                }

                .card-right h3 {
                    margin-top: 0;
                }

                .card-right p {
                    margin: 0.5rem 0;
                    font-size: 0.5rem;
                    color: #555;
                }
                }   
                
                body {
                    font-family: 'Roboto', sans-serif;
                    background-color: #f4f4f9;
                    margin: 0;
                    padding: 0;
                }


                .search-container {
                    text-align: center;
                    margin: 2rem;
                }

                .search-container input[type="text"] {
                    width: 50%;
                    padding: 0.5rem;
                    font-size: 1rem;
                    border: 1px solid #ccc;
                    border-radius: 4px;
                }

                .doctors-container {
                    display: flex;
                    flex-wrap: wrap;
                    justify-content: center;
                    padding: 2rem;
                }

                .profile {
                    background: #fff;
                    border-radius: 8px;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                    margin: 1rem;
                    width: 350px; /* Set the card width */
                    overflow: hidden;
                    flex: 1 1 calc(33.333% - 2rem); /* Responsive flex basis */
                    box-sizing: border-box;
                    display: flex; /* Ensure display is set to flex */
                }

                .card {
                    display: flex;
                    flex-direction: column;
                }

                .card-content {
                    padding: 1rem;
                    display: flex;
                    align-items: center;
                }

                .card-left {
                    flex: 1;
                }

                .card-left img.doctor-photo {
                    border-radius: 50%;
                    width: 100px;
                    height: 100px;
                    object-fit: cover;
                }

                .card-right {
                    flex: 2;
                    padding-left: 1rem;
                }

                .card-right h3 {
                    margin-top: 0;
                }

                .card-right p {
                    margin: 0.5rem 0;
                    font-size: 0.9rem;
                    color: #555;
                }

                /* Mobile responsive */
                @media (max-width: 800px) {
                
                    .profile {
                        display:flex;
                        flex-direction: row;
                    }

                    .card {
                    display: flex;
                    flex-direction: column;
                }

                .card-content {
                    padding: 0.5rem;
                    display: flex;
                    align-items: center;
                }

                .card-left {
                    flex: 0.5;
                }

                .card-left img.doctor-photo {
                    border-radius: 30%;
                    width: 80px;
                    height: 80px;
                    object-fit: cover;
                }

                .card-right {
                    flex: 1.5;
                    padding-left: 0.5rem;
                }

                .card-right h3 {
                    margin-top: 0;
                }

                .card-right p {
                    margin: 0.5rem 0;
                    font-size: 0.8rem;
                    color: #555;
                }
                }

                @media (max-width: 480px) {
                    .profile {
                        flex: 1 1 calc(100% - 6rem); 
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
        
        function searchDoctors() {
            var input, filter, cards, cardContainer, h3, title, i;
            input = document.getElementById("doctorSearch");
            filter = input.value.toUpperCase();
            cardContainer = document.getElementById("doctorsContainer");
            cards = cardContainer.getElementsByClassName("profile");
            let noResults = true;
            for (i = 0; i < cards.length; i++) {
                h3 = cards[i].getElementsByTagName("h3")[0];
                if (h3) {
                    title = h3.textContent || h3.innerText;
                    if (title.toUpperCase().indexOf(filter) > -1) {
                        cards[i].style.display = "flex";  // Ensure the display is set to flex
                        noResults = false;
                    } else {
                        cards[i].style.display = "none";
                    }
                }
            }
            document.getElementById("noResults").style.display = noResults ? "block" : "none";
        }
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
<div class="search-container">
    <input type="text" id="doctorSearch" onkeyup="searchDoctors()" placeholder="Search for doctors..">
</div>
<?php
require '../connections/connection.php';


$sql = "SELECT * FROM doctorlogin";
$result = $con->query($sql);
?>

<div class="doctors-container" id="doctorsContainer">
    <?php while ($doctor = $result->fetch_assoc()): ?>
        <div class="profile">
            <div class="card">
                <div class="card-content">
                    <div class="card-left">
                        <img src="<?php echo htmlspecialchars($doctor['Photo']); ?>" alt="Doctor Photo" class="doctor-photo">
                    </div>
                    <div class="card-right">
                        <h3><?php echo htmlspecialchars($doctor['First_Name'] . " " . $doctor['Last_Name']); ?></h3>
                        <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($doctor['Contact_No']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($doctor['Email']); ?></p>
                        <p><strong>Specialization:</strong> <?php echo htmlspecialchars($doctor['Specialization']); ?></p>
                    </div>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<p id="noResults" style="display:none; text-align:center; color: red;">No matching results are here.</p>

<?php
mysqli_close($con);
?>
</body>
</html>
