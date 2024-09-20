<?php
require '../connections/connectionpdo.php';
require '../connections/connection.php';

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

// Retrieve the QR data from the POST request
$data = json_decode(file_get_contents('php://input'), true);
$qr_data = $data['qr_data'];

// Parse the QR data
parse_str($qr_data, $decoded_data);

$CID = $decoded_data['CID'];
$SID = $decoded_data['SID'];
$QueueNo = $decoded_data['QueueNo'];

// Fetch the necessary details using INNER JOIN
$query = "
    SELECT c.Room_Number, c.Date, s.Target_Patient_Type
    FROM clinic c
    INNER JOIN schedule s ON c.SID = s.SID
    WHERE c.CID = ? AND c.SID = ?
";
$stmt = $pdo->prepare($query);
$stmt->execute([$CID, $SID]);
$details = $stmt->fetch();

if ($details) {
    $Room_Number = $details['Room_Number'];
    $Patient_Type = $details['Target_Patient_Type'];
    $clinic_date = $details['Date'];

    // Get the system date
    $system_date = date('Y-m-d');

    // Compare the clinic date with the system date
    if ($clinic_date === $system_date) {
        // Delete existing queue data for the same CID, SID, and Room_Number
        $delete_query = "
            DELETE FROM viewqueue 
            WHERE CID = ? AND SID = ? AND Room_Number = ?
        ";
        $stmt = $pdo->prepare($delete_query);
        $stmt->execute([$CID, $SID, $Room_Number]);

        // Generate a unique VQID by combining CID, SID, and an incremented ID
        $query_max_id = "SELECT MAX(SUBSTRING(VQID, 7)) AS max_id 
                         FROM viewqueue 
                         WHERE VQID LIKE CONCAT('VQ_', ?, '_%')";
        $stmt = $pdo->prepare($query_max_id);
        $stmt->execute([$CID]);
        $row = $stmt->fetch();
        $max_id = $row['max_id'];

        if ($max_id === null) {
            // Start with 001 for a new clinic
            $next_id = 'VQ_' . $CID . '_' . '001';
        } else {
            // Increment the last found ID
            $next_id = 'VQ_' . $CID . '_' . str_pad((int)$max_id + 1, 3, '0', STR_PAD_LEFT);
        }

        // Insert the new queue data
        $insert_query = "
            INSERT INTO viewqueue (VQID,CID, SID, Patient_Type, Room_Number, Ongoing_Queue_Number) 
            VALUES (?,?, ?, ?, ?, ?)
        ";
        $stmt = $pdo->prepare($insert_query);
        if ($stmt->execute([$next_id,$CID, $SID, $Patient_Type, $Room_Number, $QueueNo])) {
            echo "<script>alert('QR code data inserted successfully.');</script>";
        } else {
            echo "<script>alert('Error inserting QR code data.');</script>";
        }

    
    } else {
        // Clinic is not scheduled for today
        echo "<script>alert('Clinic is scheduled to be held on $clinic_date.');</script>";
    }
} else {
    echo "<script>alert('No matching clinic or schedule found.');</script>";
}
$con->close();
?>
