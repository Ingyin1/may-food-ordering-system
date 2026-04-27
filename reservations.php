<?php
session_start();
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['user_id'])) {
        echo '<script>alert("Please login first!"); window.location.href="login.php";</script>';
        exit();
    }

    $user_id = $_SESSION['user_id']; 
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $noOfGuests = $_POST['noOfGuests'];
    $reservedTime = date('H:i:s', strtotime($_POST['reservedTime']));
    $reservedDate = $_POST['reservedDate'];

    $sql = "INSERT INTO reservations (user_id, name, contact, noOfGuests, reservedTime, reservedDate, status) 
            VALUES (?, ?, ?, ?, ?, ?, 'Pending')";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ississ", $user_id, $name, $contact, $noOfGuests, $reservedTime, $reservedDate);
    
    if ($stmt->execute()) {
        echo '<script>alert("Reservation Successful!"); window.location.href="index.php";</script>';
    }
}
?>