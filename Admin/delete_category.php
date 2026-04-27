<?php
include 'db_connection.php';

if (isset($_GET['name'])) {
    $catName = $_GET['name'];
    $stmt = $conn->prepare("DELETE FROM menucategory WHERE catname = ?");
    $stmt->bind_param("s", $catName);
    
    if ($stmt->execute()) {
        header("Location: admin_menu.php?msg=deleted");
    } else {
        echo "Error deleting category: " . $conn->error;
    }
}
?>