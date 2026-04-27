<?php
include 'db_connection.php';
$input = file_get_contents('php://input');
$data = json_decode($input, true);
$reviewId = $data['reviewId'] ?? ($_POST['reviewId'] ?? '');

if (empty($reviewId)) {
    echo json_encode(['success' => false, 'message' => 'No Review ID received.']);
    exit();
}
$stmt = $conn->prepare("DELETE FROM reviews WHERE review_id = ?");
$stmt->bind_param("i", $reviewId);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Review deleted successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Review not found in database. ID: ' . $reviewId]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Database Error: ' . $conn->error]);
}
?>