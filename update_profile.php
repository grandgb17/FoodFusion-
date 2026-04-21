<?php
session_start();
require 'db.php';

if(!isset($_SESSION['user'])){
    header("Location: Home.php");
    exit();
}

// CSRF validation
if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
    header("Location: profile.php?error=1");
    exit();
}

$currentEmail = $_SESSION['user'];

$first = trim($_POST['first_name'] ?? '');
$last  = trim($_POST['last_name']  ?? '');
$email = trim($_POST['email']      ?? '');
$diet  = $_POST['diet']            ?? '';
$pass  = trim($_POST['password']   ?? '');

// Validate
if(empty($first) || empty($last) || empty($email)){
    header("Location: profile.php?error=1");
    exit();
}
if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
    header("Location: profile.php?error=1");
    exit();
}
if(!in_array($diet, ['Vegetarian','Non-Vegetarian','Vegan'])){
    header("Location: profile.php?error=1");
    exit();
}
if(!empty($pass) && strlen($pass) < 6){
    header("Location: profile.php?error=1");
    exit();
}

if(!empty($pass)){
    $hashed = password_hash($pass, PASSWORD_DEFAULT);
    $sql    = "UPDATE users SET first_name=?, last_name=?, email=?, password=?, preference=? WHERE email=?";
    $stmt   = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $first, $last, $email, $hashed, $diet, $currentEmail);
} else {
    $sql  = "UPDATE users SET first_name=?, last_name=?, email=?, preference=? WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $first, $last, $email, $diet, $currentEmail);
}

if($stmt->execute()){
    $_SESSION['user'] = $email;
    header("Location: profile.php?updated=1");
} else {
    header("Location: profile.php?error=1");
}
exit();
?>
