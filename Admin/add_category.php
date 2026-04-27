<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $catName = $_POST['catName'];
    $stmt = $conn->prepare("INSERT INTO menucategory (catName) VALUES (?)");
    $stmt->bind_param("s", $catName);
    
    if ($stmt->execute()) {
        echo '<script>alert("Category added successfully."); window.location.href="admin_menu.php";</script>';
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>