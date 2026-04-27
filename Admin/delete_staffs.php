<?php
session_start();
if (!isset($_SESSION['adminloggedin'])) {
    http_response_code(403);
    exit();
}
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    $stmt = $conn->prepare("DELETE FROM staff WHERE email = ?");
    $stmt->bind_param("s", $email);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo 'Staff deleted successfully';
        } else {
            echo 'Staff not found';
        }
    } else {
        http_response_code(500);
        echo 'Error deleting staff';
    }
}
?>
