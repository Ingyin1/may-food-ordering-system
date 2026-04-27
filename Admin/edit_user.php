<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $contact = $_POST['contact'];
    $address = isset($_POST['address']) ? $_POST['address'] : ''; 
    $stmt = $conn->prepare("UPDATE users SET firstName=?, lastName=?, contact=?, address=? WHERE email=?");
    $stmt->bind_param("sssss", $firstName, $lastName, $contact, $address, $email);
    
    if ($stmt->execute()) {
        header("Location: users.php?status=success");
    } else {
        echo "Error updating user: " . $conn->error;
    }
    $stmt->close();
    $conn->close();
    exit();
}
?>