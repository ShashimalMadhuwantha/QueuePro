<?php
session_start();
require '../connections/connectionpdo.php';
header('Content-Type: application/json');



// Setup PDO connection
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

// Fetch today's appointments grouped by doctor
$queryToday = "
    SELECT d.First_Name, d.Last_Name, COUNT(a.Appointment_ID) as count
    FROM appointment a
    INNER JOIN doctorlogin d ON a.Doctor_ID = d.DID
    WHERE DATE(a.Date_and_Time) = CURDATE()
    GROUP BY d.DID
    ORDER BY count DESC
";
$stmtToday = $pdo->prepare($queryToday);
$stmtToday->execute();
$dataToday = $stmtToday->fetchAll();

// Fetch appointment count for the last 7 days
$queryDaily = "
    SELECT DATE(Date_and_Time) as date, COUNT(*) as count
    FROM appointment
    WHERE DATE(Date_and_Time) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY DATE(Date_and_Time)
    ORDER BY DATE(Date_and_Time)
";
$stmtDaily = $pdo->prepare($queryDaily);
$stmtDaily->execute();
$dataDaily = $stmtDaily->fetchAll();

// Output JSON with both datasets
echo json_encode([
    'today' => $dataToday,
    'daily' => $dataDaily
]);


?>


