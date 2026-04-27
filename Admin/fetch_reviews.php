<?php
header('Content-Type: application/json');

include 'db_connection.php';
$sql = "SELECT DATE(created_at) as review_date, rating, COUNT(*) as count 
        FROM reviews 
        GROUP BY DATE(created_at), rating 
        ORDER BY review_date";

$result = $conn->query($sql);

if ($result) {
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode($data);
} else {
    echo json_encode(['error' => $conn->error]);
}
?>