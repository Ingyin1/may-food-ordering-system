<?php
include 'db_connection.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $noOfGuests = $_POST['noOfGuests'];
    $reservedTime = $_POST['reservedTime'];
    $reservedDate = $_POST['reservedDate'];
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 6; 

    $reservedTimeWithSeconds = date('H:i:s', strtotime($reservedTime));
    $sql = "INSERT INTO reservations (user_id, name, contact, noOfGuests, reservedTime, reservedDate, status) 
            VALUES (?, ?, ?, ?, ?, ?, 'Pending')";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ississ", $user_id, $name, $contact, $noOfGuests, $reservedTimeWithSeconds, $reservedDate);

    if ($stmt->execute()) {
        echo '<script>alert("Reservation successful!"); window.location.href="reservations.php";</script>';
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>