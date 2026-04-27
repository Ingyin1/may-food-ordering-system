<?php
include 'auth_functions.php';

echo "=== Unit Testing: Login ===\n";
$plain_password = "Anna123!@#"; 
$hashed_password = password_hash($plain_password, PASSWORD_DEFAULT); 

echo "\n[Visualizing Password Transformation]\n";
echo "BEFORE Hashing (Plain Text) : " . $plain_password . "\n";
echo "AFTER  Hashing (DB String)  : " . $hashed_password . "\n";
echo"===========================================================\n";
echo "[Test 1: Verification Logic]\n";
if (verifyUserPassword($plain_password, $hashed_password)) {
    echo "Success: Verification matches the hash correctly.\n";
} else {
    echo "Fail: Verification failed.";
}
if (!verifyUserPassword("wrong_pass", $hashed_password)) {
    echo "Success: Incorrect password was properly rejected.\n";
} else {
    echo "Fail: System accepted a wrong password!\n";
}
echo "\n[Test 2: Role Redirection Logic]\n";
echo "Admin Routing: " . (getRedirectLocationByRole('Admin') === "Admin/index.php" ? "PASS" : "FAIL") . "\n";
echo "Customer Routing : " . (getRedirectLocationByRole('Customer') === "index.php" ? "PASS" : "FAIL") . "\n";
?>