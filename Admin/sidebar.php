<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_connection.php';

$admin_email = isset($_SESSION['email']) ? $_SESSION['email'] : '';

if (empty($admin_email)) {
    die("Admin email not found in session.");
}

function getAdminInfo($email)
{
    global $conn;
    $stmt = $conn->prepare("SELECT firstName, lastName, email, profile_image, role FROM users WHERE email = ? AND role = 'Admin'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return [
        'firstName' => $row['firstName'] ?? '',
        'lastName' => $row['lastName'] ?? '',
        'email' => $row['email'] ?? '',
        'profile_image' => $row['profile_image'] ?? 'default.jpg'
    ];
}

$admin_info = getAdminInfo($admin_email);
?>
