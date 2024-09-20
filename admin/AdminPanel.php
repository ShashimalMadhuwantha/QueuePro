<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="icon" href="../pictures/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>

    <link rel="stylesheet" href="../css/navstyle.css">
    <link rel="stylesheet" type="text/css" href="../bootstraplibraries/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script> <!-- For exporting -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script>
        function toggleNav() {
            var nav = document.querySelector('.nav-items ul');
            nav.classList.toggle('show');
        }

        function exportToExcel() {
            fetch('combinedReport.php')
                .then(response => response.json())
                .then(data => {
                    if (!data.today && !data.daily) {
                        console.warn('No data available');
                        return;
                    }

                    const workbook = XLSX.utils.book_new();

                    const todayData = data.today.map(item => ({
                        'Doctor': item.First_Name + ' ' + item.Last_Name,
                        'Appointments': item.count,
                    }));
                    const todaySheet = XLSX.utils.json_to_sheet(todayData);
                    XLSX.utils.book_append_sheet(workbook, todaySheet, 'Today\'s Doctor Report');

                    const dailyData = data.daily.map(item => ({
                        'Date': item.date,
                        'Appointments': item.count
                    }));
                    const dailySheet = XLSX.utils.json_to_sheet(dailyData);
                    XLSX.utils.book_append_sheet(workbook, dailySheet, 'Daily Report');

                    XLSX.writeFile(workbook, 'Appointment_Report.xlsx');
                })
                .catch(error => console.error('Error fetching report data:', error));
        }

        function exportToPDF() {
            fetch('combinedReport.php')
                .then(response => response.json())
                .then(data => {
                    if (!data.today && !data.daily) {
                        console.warn('No data available');
                        return;
                    }

                    const { jsPDF } = window.jspdf;
                    const doc = new jsPDF();

                    doc.setFontSize(18);
                    doc.text("Today's Doctor Appointment Report", 20, 20);

                    doc.setFontSize(12);
                    const todayData = data.today.map(item => `${item.First_Name} ${item.Last_Name}: Number Of appointments for Today ${item.count}`);
                    todayData.forEach((line, index) => {
                        doc.text(line, 20, 30 + index * 10);
                    });

                    doc.addPage();
                    doc.setFontSize(18);
                    doc.text("Daily Appointment Report", 20, 20);

                    doc.setFontSize(12);
                    const dailyData = data.daily.map(item => `${item.date}:- Number Of appointments ${item.count}`);
                    dailyData.forEach((line, index) => {
                        doc.text(line, 20, 30 + index * 10);
                    });

                    doc.save('Appointment_Report.pdf');
                })
                .catch(error => console.error('Error fetching report data:', error));
        }

        document.addEventListener('DOMContentLoaded', function() {
            fetch('combinedReport.php')
                .then(response => response.json())
                .then(data => {
                    if (!data.today && !data.daily) {
                        console.warn('No data available');
                        return;
                    }

                    const todayDoctors = data.today.map(item => item.First_Name + ' ' + item.Last_Name);
                    const todayCounts = data.today.map(item => item.count);


                    const ctxToday = document.getElementById('todayDoctorChart').getContext('2d');
                    new Chart(ctxToday, {
                        type: 'bar',
                        data: {
                            labels: todayDoctors,
                            datasets: [{
                                label: 'Number of Appointments',
                                data: todayCounts,
                                backgroundColor: '#4b70f5',
                                borderColor: '#003d7a',
                                borderWidth: 2,
                                borderRadius: 5,
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        title: function (tooltipItems) {
                                            return 'Doctor: ' + tooltipItems[0].label;
                                        },
                                        label: function (tooltipItem) {
                                            return 'Appointments: ' + tooltipItem.raw;
                                        }
                                    },
                                    backgroundColor: '#ffffff',
                                    titleColor: '#4b70f5',
                                    bodyColor: '#333',
                                    borderColor: '#4b70f5',
                                    borderWidth: 1,
                                    padding: 8,
                                    boxPadding: 8,
                                    cornerRadius: 4
                                }
                            },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    ticks: {
                                        color: '#4b70f5',
                                        font: {
                                            weight: 'bold',
                                        }
                                    },
                                    grid: {
                                        color: '#e5e7eb'
                                    },
                                    barPercentage: 0.6,
                                    categoryPercentage: 0.8
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        color: '#4b70f5',
                                        stepSize: 5,
                                        callback: function (value) {
                                            return Number.isInteger(value) ? value : '';
                                        }
                                    },
                                    grid: {
                                        color: '#e5e7eb'
                                    },
                                    suggestedMax: 50
                                }
                            }
                        }
                    });

                    const dailyDates = data.daily.map(item => item.date);
                    const dailyCounts = data.daily.map(item => item.count);

                    const ctxDaily = document.getElementById('dailyReportChart').getContext('2d');
                    new Chart(ctxDaily, {
                        type: 'bar',
                        data: {
                            labels: dailyDates,
                            datasets: [{
                                label: 'Number of Appointments',
                                data: dailyCounts,
                                backgroundColor: '#76aaff',
                                borderColor: '#003d7a',
                                borderWidth: 2,
                                borderRadius: 5,
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        title: function (tooltipItems) {
                                            return 'Date: ' + tooltipItems[0].label;
                                        },
                                        label: function (tooltipItem) {
                                            return 'Appointments: ' + tooltipItem.raw;
                                        }
                                    },
                                    backgroundColor: '#ffffff',
                                    titleColor: '#4b70f5',
                                    bodyColor: '#333',
                                    borderColor: '#4b70f5',
                                    borderWidth: 1,
                                    padding: 8,
                                    boxPadding: 8,
                                    cornerRadius: 4
                                }
                            },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    ticks: {
                                        color: '#4b70f5',
                                        font: {
                                            weight: 'bold',
                                        }
                                    },
                                    grid: {
                                        color: '#e5e7eb'
                                    },
                                    barPercentage: 0.6,
                                    categoryPercentage: 0.8
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        color: '#4b70f5',
                                        stepSize: 5,
                                        callback: function (value) {
                                            return Number.isInteger(value) ? value : '';
                                        }
                                    },
                                    grid: {
                                        color: '#e5e7eb'
                                    },
                                    suggestedMax: 50
                                }
                            }
                        }
                    });
                })
                .catch(error => console.error('Error fetching report data:', error));
        });
    </script>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f5f7;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: row; /* Align charts in a row */
            gap: 2rem;
        }

        .chart-container {
            flex: 1; /* Make each chart take up equal space */
            padding: 2rem;
            border-radius: 8px;
            background: #f9f9f9;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 2.5rem;
            color: #4b70f5;
            margin-bottom: 1rem;
            text-align: center;
        }

        .chart-title {
            text-align: center;
            font-size: 1.2rem;
            margin-bottom: 1rem;
            color: #6b7280;
        }

        .chart-wrapper {
            position: relative;
            height: 400px;
        }

        .chart-container canvas {
            display: block;
            max-width: 100%;
            height: auto;
        }

        .export-button {
            display: block;
            width: 200px;
            padding: 10px;
            margin: 20px auto;
            background-color: gray;
            color: #fff;
            text-align: center;
            border-radius: 5px;
            cursor: pointer;
        }

        .export-button :hover
        {
            background-color: green;
        }
    </style>
</head>
<body>
    <div class="nav">
        <div class="head">
            <h2>Admin Panel</h2>
        </div>
        <div class="nav-toggle" onclick="toggleNav()">
            <ion-icon name="menu-outline"></ion-icon>
        </div>
        <div class="nav-items">
            <ul>           
            <li class="nav-item"><a class="nav-link" href="AdminPanel.php">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="addadmins.php">Add Admins</a></li>
            <li class="nav-item"><a class="nav-link" href="adddoctors.php">Add Doctors</a></li>
            <li class="nav-item"><a class="nav-link" href="addclinics.php">Add Clinics</a></li>
            <li class="nav-item"><a class="nav-link" href="assingschedule.php">Assign Schedule</a></li>
            <li class="nav-item"><a class="nav-link" href="managelogins.php">Manage Logins</a></li>
            <li class="nav-item"><a class="nav-link" href="../qr/qrscanner.php">Scanner</a></li>
            <li class="nav-item"><a class="nav-link" href="Adminlogout.php">Logout</a></li>
            </ul>
        </div>
    </div>

    <div class="container">
        <div class="chart-container">
            <h1 class="chart-title">Today's Doctor Appointment Report</h1>
            <div class="chart-wrapper">
                <canvas id="todayDoctorChart"></canvas>
            </div>
        </div>

        <div class="chart-container">
            <h1 class="chart-title">Daily Appointment Report</h1>
            <div class="chart-wrapper">
                <canvas id="dailyReportChart"></canvas>
            </div>
        </div>
    </div>
<center>   <button class="btn btn-success" onclick="exportToExcel()">Export To Excel</button> <br><br>
<button class="btn btn-danger" onclick="exportToPDF()">Export To PDF</button>
<br><br>
</center>
    
</body>
</html>