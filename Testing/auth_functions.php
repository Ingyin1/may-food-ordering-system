<?php
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}
function isValidContact($contact) {
    return preg_match('/^[0-9]{9,11}$/', $contact);
}
function hasEmptyFields($data) {
    foreach ($data as $value) {
        if (empty(trim($value))) return true;
    }
    return false;
}
function isStrongPassword($password) {
    $strongRegex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*])(?=.{8,})/';
    return preg_match($strongRegex, $password);
}
function verifyUserPassword($plainPassword, $hashedPasswordFromDB) {
    return password_verify($plainPassword, $hashedPasswordFromDB);
}

function getRedirectLocationByRole($role) {
    if ($role === 'Admin') {
        return "Admin/index.php";
    } elseif ($role === 'Customer' || $role === 'User') {
        return "index.php";
    } else {
        return "login.php?error=invalid_role";
    }
}
?>