<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['catId']) || $_POST['catId'] === "") {
        echo '<script>alert("Error: Please Choose the Category!"); window.history.back();</script>';
        exit();
    }
    $itemName    = $_POST['itemName'];
    $price       = $_POST['price'];
    $description = $_POST['description'];
    $status      = $_POST['status'];
    $catId       = $_POST['catId'];
    $is_popular  = isset($_POST['is_popular']) ? 1 : 0; 

    $target_dir  = "../uploads/";
    if (!isset($_FILES["image"]) || $_FILES["image"]["error"] == 4) {
        echo '<script>alert("Error: Please Select image!"); window.history.back();</script>';
        exit();
    }

    $fileName      = time() . '_' . basename($_FILES["image"]["name"]); 
    $target_file   = $target_dir . $fileName;
    $uploadOk      = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check === false) {
        echo '<script>alert("Error: File is not an image."); window.history.back();</script>';
        $uploadOk = 0;
    }
    if ($_FILES["image"]["size"] > 5000000) {
        echo '<script>alert("Sorry, your file is too large. Max 5MB allowed."); window.history.back();</script>';
        $uploadOk = 0;
    }
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
        echo '<script>alert("Sorry, only JPG, JPEG, PNG & GIF files are allowed."); window.history.back();</script>';
        $uploadOk = 0;
    }
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $stmt = $conn->prepare("INSERT INTO menuitem (itemName, catId, price, status, description, image, is_popular) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sidsssi", $itemName, $catId, $price, $status, $description, $fileName, $is_popular);

            if ($stmt->execute()) {
                echo '<script>alert("New item added successfully."); window.location.href="admin_menu.php";</script>';
                exit();
            } else {
                echo "Database Error: " . $conn->error;
            }
        } else {
            echo '<script>alert("Sorry, there was an error uploading your file."); window.history.back();</script>';
        }
    }
}
?>