<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $noOfGuests = $_POST['noOfGuests'];
    $reservedTime = $_POST['reservedTime'];
    $reservedDate = $_POST['reservedDate'];
    $reservation_id = $_POST['reservation_id'];

    $stmt = $conn->prepare("UPDATE reservations SET name=?, contact=?, noOfGuests=?, reservedTime=?, reservedDate=? WHERE reservation_id=?");
    $stmt->bind_param("ssissi", $name, $contact, $noOfGuests, $reservedTime, $reservedDate, $reservation_id);
    
    if ($stmt->execute()) {
        header("Location: reservations.php?msg=updated");
    } else {
        echo "Error Updating Reservation: " . $conn->error;
    }
    exit();
}
?>