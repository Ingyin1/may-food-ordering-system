<?php
include 'db_connection.php';

$sql = "SELECT COUNT(*) AS totalReservations FROM reservations";
$result = $conn->query($sql);

if ($result) {
    $row = $result->fetch_assoc();
    $totalReservations = $row["totalReservations"] ?? 0;
} else {
    $totalReservations = 0;
}

header('Content-Type: application/json');
echo json_encode(['totalReservations' => $totalReservations]);
?>
