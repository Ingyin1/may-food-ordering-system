<?php
session_start();
include 'db_connection.php';
include 'Testing/auth_functions.php';
$max_attempts = 5;      
$lockout_time = 60;     

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] >= $max_attempts) {
        $last_attempt_time = $_SESSION['last_attempt_time'] ?? 0;
        $elapsed_time = time() - $last_attempt_time;

        if ($elapsed_time < $lockout_time) {
            $seconds_left = $lockout_time - $elapsed_time;
            header("Location: login.php?error=too_many_attempts&wait=$seconds_left");
            exit();
        } else {

            $_SESSION['login_attempts'] = 0;
        }
    }
    if (hasEmptyFields([$email, $password])) {
        header("Location: login.php?error=empty_fields");
        exit();
    }
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && verifyUserPassword($password, $user['password'])) {
        unset($_SESSION['login_attempts']);
        unset($_SESSION['last_attempt_time']);

        session_regenerate_id(true);

        $_SESSION['email'] = $user['email'];
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['firstName'] = $user['firstName'];
        $_SESSION['role'] = $user['role']; 

        if ($user['role'] === 'Admin') {
            $_SESSION['adminloggedin'] = true;
        } else {
            $_SESSION['userloggedin'] = true;
        }

        header("Location: " . getRedirectLocationByRole($user['role']));
        exit();
    } else {
        $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
        $_SESSION['last_attempt_time'] = time();

        $error_type = $user ? 'wrong_password' : 'email_not_found';
        header("Location: login.php?error=$error_type");
        exit();
    }
}
?>