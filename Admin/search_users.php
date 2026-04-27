<?php
session_start();
if (!isset($_SESSION['adminloggedin'])) {
    header("Location: ../login.php");
    exit();
}
include 'db_connection.php';

$search = '';
if (isset($_POST['search'])) {
    $search = $_POST['search'];
}

if (!empty($search)) {
    $searchParam = '%' . $search . '%';
    $sql = "SELECT * FROM users WHERE email LIKE ? OR firstName LIKE ? OR lastName LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $searchParam, $searchParam, $searchParam);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT * FROM users";
    $result = $conn->query($sql);
}

if ($result) {
    $results = [];
    while ($row = $result->fetch_assoc()) {
        $results[] = $row;
    }
    
    if (count($results) > 0) {
        $counter = 1;
        foreach ($results as $row) {
            $passwordMasked = str_repeat('*', strlen($row['password']));
            echo "<tr>
                    <td>{$counter}</td>
                    <td>{$row['dateCreated']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['firstName']}</td>
                    <td>{$row['lastName']}</td>
                    <td>{$row['contact']}</td>
                    <td>
                        <span class='password-masked'>{$passwordMasked}</span>
                        <span class='password-visible' style='display: none;'>{$row['password']}</span>
                        <i class='fas fa-eye toggle-password' onclick='togglePassword(this)'></i>
                    </td>
                    <td>
                        <button id='editbtn' onclick='openEditUserModal(this)' data-email='{$row['email']}' data-firstname='{$row['firstName']}' data-lastname='{$row['lastName']}' data-contact='{$row['contact']}' data-password='{$row['password']}'><i class='fas fa-edit'></i></button>
                        <button id='deletebtn' onclick=\"deleteItem('{$row['email']}')\"><i class='fas fa-trash'></i></button>
                    </td>
                  </tr>";
            $counter++;
        }
    } else {
        echo "<tr><td colspan='8' style='text-align: center;'>No Users Found</td></tr>";
    }
} else {
    echo "<tr><td colspan='8' style='text-align: center;'>Error: " . $conn->error . "</td></tr>";
}
?>
