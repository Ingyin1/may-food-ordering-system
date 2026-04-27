<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $itemId = $_POST['itemId'];
    $itemName = $_POST['itemName'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $status = $_POST['status'];
    $catId = $_POST['catId']; 
    if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != "") {
        $fileName = time() . '_' . basename($_FILES["image"]["name"]);
        $target_dir = "../uploads/";
        $target_file = $target_dir . $fileName;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image = $fileName;
        } else {
            $image = $_POST['existingImage'];
        }
    } else {
        $image = $_POST['existingImage'];
    }
   
    $sql = "UPDATE menuitem SET itemName=?, description=?, price=?, status=?, catId=?, image=? WHERE itemId=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsisi", $itemName, $description, $price, $status, $catId, $image, $itemId);
    
    if ($stmt->execute()) {
        echo '<script>alert("Item updated successfully."); window.location.href="admin_menu.php";</script>';
        exit();
    } else {
        echo "Error updating item: " . $conn->error;
    }
}
?>