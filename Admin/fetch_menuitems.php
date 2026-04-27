<?php
include 'db_connection.php';

$sql = "SELECT COUNT(*) AS totalItems FROM menuitem";
$result = $conn->query($sql);

if ($result) {
    $row = $result->fetch_assoc();
    $totalItems = $row["totalItems"] ?? 0;
} else {
    $totalItems = 0;
}

header('Content-Type: application/json');
echo json_encode(['totalItems' => $totalItems]);
?>
