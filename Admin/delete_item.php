<?php
include 'db_connection.php';

if (isset($_GET['id'])) {
    $itemId = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM menuitem WHERE itemId = ?");
    $stmt->bind_param("i", $itemId);
    
    if ($stmt->execute()) {
        echo "Record deleted successfully";
    } else {
        echo "Error: " . $conn->error;
    }

    header("Location: admin_menu.php");
    exit();
}
?>
