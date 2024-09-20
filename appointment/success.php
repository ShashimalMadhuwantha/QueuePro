<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Success</title>
    <link rel="title icon" href="../pictures/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.4.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/main.css" />
    <style>
        .success-message {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
            flex-direction: column;
        }
        .success-icon {
            font-size: 50px;
            color: green;
        }
         /* From Uiverse.io by AqFox */ 
.spinner {
 width: 56px;
 height: 56px;
 display: grid;
 color: #004dff;
 background: radial-gradient(farthest-side, currentColor calc(100% - 7px),#0000 calc(100% - 6px) 0);
 -webkit-mask: radial-gradient(farthest-side,#0000 calc(100% - 15px),#000 calc(100% - 13px));
 border-radius: 50%;
 animation: spinner-sm4bhi 2.5s infinite linear;
}

.spinner::before,
.spinner::after {
 content: "";
 grid-area: 1/1;
 background: linear-gradient(currentColor 0 0) center,
          linear-gradient(currentColor 0 0) center;
 background-size: 100% 11px,11px 100%;
 background-repeat: no-repeat;
}

.spinner::after {
 transform: rotate(45deg);
}

@keyframes spinner-sm4bhi {
 100% {
  transform: rotate(1turn);
 }
}
            </style>
</head>
<body>
<div class="success-message">
    <div class="success-icon">
        <i class="ri-check-line"></i>
    </div>
    <h2>Appointment Successfully Created!</h2>
    <br><br>

    <div class="spinner"></div>
</div>
</div>
<script>
    setTimeout(function() {
        window.location.href = '../index.php';
    }, 2000);
</script>
</body>
</html>
