<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'db_connection.php';

    $reservation_id = $_POST['reservation_id'];

    $stmt = $conn->prepare("DELETE FROM reservations WHERE reservation_id = ?");
    $stmt->bind_param("i", $reservation_id);

    if ($stmt->execute()) {
        echo "Reservation deleted successfully";
    } else {
        echo "Error deleting reservation: " . $conn->error;
    }
}
?>
