<?php
require '../connections/connection.php';



// Check connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'] ?? '';

    if ($date) {
        // Prepare the query using prepared statements
        $query = "
            SELECT Room_Number 
            FROM rooms 
            WHERE Room_Number NOT IN (
                SELECT Room_Number 
                FROM clinic 
                WHERE Date = ? AND Current_State = 'OnGoing'
            )
        ";

        if ($stmt = mysqli_prepare($con, $query)) {
            // Bind parameters
            mysqli_stmt_bind_param($stmt, 's', $date);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            // Fetch available rooms
            $availableRooms = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $availableRooms[] = $row['Room_Number'];
            }

            // Close statement
            mysqli_stmt_close($stmt);
        } else {
            echo "<option value=''>Error preparing the statement</option>";
        }

        // Output available rooms
        if (!empty($availableRooms)) {
            foreach ($availableRooms as $room) {
                echo "<option value='$room'>$room</option>";
            }
        } else {
            echo "<option value=''>No Available Rooms</option>";
        }
    } else {
        echo "<option value=''>Date not provided</option>";
    }

    // Close connection
    mysqli_close($con);
}
?>
