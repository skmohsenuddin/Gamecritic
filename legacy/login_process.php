<?php
session_start();
require_once "db/db.php";


$email = trim($_POST['email']);
$password = trim($_POST['password']);


$stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        echo "Login successful";
    } else {
        echo "Invalid email or password";
    }
} else {
    echo "Invalid email or password";
}

if ($result->num_rows == 1) {
    $_SESSION['user_email'] = $email;


    if ($email === "admin@gamecritic.com") {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: user_home.php");
    }

    exit();
} else {

    header("Location: login.php?error=invalid_credentials");
    exit();
}




