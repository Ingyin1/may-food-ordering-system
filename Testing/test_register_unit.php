<?php
include 'auth_functions.php';

echo "=== Unit Testing: Registration & Security Logic ===\n\n";
echo "[Test 1: Email Check]\n";
echo "Valid Email (anna@mail.com): " . (isValidEmail("anna@mail.com") ? "PASS" : "FAIL") . "\n";
echo "Invalid Email (anna-mail): " . (!isValidEmail("anna-mail") ? "PASS (Rejected)" : "FAIL") ."\n";

echo "\n[Test 2: Phone Number Check]\n";
echo "Valid Phone (0912345678): " . (isValidContact("0912345678") ? "PASS" : "FAIL") . "\n";
echo "Too Short Phone (123): " . (!isValidContact("123") ? "PASS (Rejected)" : "FAIL") . "\n";

echo "\n[Test 3: Empty Fields Check]\n";
$form_data_valid = ['Anna', 'Taylor', 'anna@mail.com', '0912345678', 'Yangon', 'Anna123!@#'];
$form_data_invalid = ['Anna', '', 'anna@mail.com', '', 'Yangon', ''];

echo "Full Data: " . (!hasEmptyFields($form_data_valid) ? "PASS" : "FAIL") . "\n";
echo "Empty Data: " . (hasEmptyFields($form_data_invalid) ? "PASS (Detected)" : "FAIL") . "\n";

echo "\n[Test 4: Password Strength Check]\n";
$strong_pass = "Anna123!@#";
$weak_pass = "12345";

echo "Strong Password ($strong_pass): " . (isStrongPassword($strong_pass) ? "PASS (Strong)" : "FAIL") . "\n";
echo "Weak Password ($weak_pass): " . (!isStrongPassword($weak_pass) ? "PASS (Rejected)" : "FAIL") . "\n";

echo "\n[Test 5: Password Hashing Visualizer]\n";
$plain_password = "Anna123!@#";
$hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

echo "BEFORE Hashing (Plain): " . $plain_password . "\n";
echo "AFTER  Hashing (Hash) : " . $hashed_password . "\n";
echo "Verification Test     : " . (verifyUserPassword($plain_password, $hashed_password) ? "Success" : "Fail") . "\n";
?>