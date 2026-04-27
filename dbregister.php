<?php
session_start();
include 'db_connection.php';
include 'Testing/auth_functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = trim($_POST['firstName']);
    $lastName  = trim($_POST['lastName']);
    $email     = trim($_POST['email']);
    $contact   = trim($_POST['contact']);
    $address   = trim($_POST['address']); 
    $password  = $_POST['password'];
    if (hasEmptyFields([$firstName, $lastName, $email, $contact, $password])) {
        echo '<script>alert("All fields are required!"); window.history.back();</script>';
        exit();
    }
    if (!isValidEmail($email)) {
        echo '<script>alert("Invalid email format!"); window.history.back();</script>';
        exit();
    }
    if (!isValidContact($contact)) {
        echo '<script>alert("Phone number must be digits and between 9-11 characters!"); window.history.back();</script>';
        exit();
    }
    if (!isStrongPassword($password)) {
        echo '<script>alert("Password is too weak! Must include uppercase, lowercase, number, and symbol."); window.history.back();</script>';
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    try {
        $sql = "INSERT INTO users (email, firstName, lastName, contact, address, password) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $email, $firstName, $lastName, $contact, $address, $hashedPassword);

        if ($stmt->execute()) {
            echo '<script>alert("Registered successfully!"); window.location.href="login.php";</script>';
            exit();
        }
    } catch (mysqli_sql_exception $e) {
        if ($conn->errno == 1062) {
            echo '<script>alert("Error: This email is already registered."); window.history.back();</script>';
        } else {
            echo '<script>alert("Registration failed. Please try again later."); window.history.back();</script>';
        }
    }
}
$conn->close();
?>