<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $contact = $_POST['contact'];
    $address = $_POST['address']; 
    $password = $_POST['password'];

   
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (email, firstName, lastName, contact, address, password) 
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $email, $firstName, $lastName, $contact, $address, $hashed_password);

    if ($stmt->execute()) {
        echo '<script>alert("User Added successfully!"); window.location.href="users.php";</script>';
    } else {
        echo "Error: " . $conn->error;
    }
}
?>