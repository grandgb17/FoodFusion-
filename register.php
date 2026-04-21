<?php
session_start();
include "db.php";

// Generate CSRF token if not set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // CSRF validation
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        echo "<script>alert('Security check failed. Please try again.'); window.history.back();</script>";
        exit();
    }

    $first = trim($_POST["first_name"]);
    $last  = trim($_POST["last_name"]);
    $email = trim($_POST["email"]);
    $diet  = $_POST["diet"];
    $pass  = $_POST["password"];

    // Server-side validation
    if (empty($first) || empty($last) || empty($email) || empty($pass)) {
        echo "<script>alert('All fields are required.'); window.history.back();</script>";
        exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Please enter a valid email address.'); window.history.back();</script>";
        exit();
    }
    if (strlen($pass) < 6) {
        echo "<script>alert('Password must be at least 6 characters.'); window.history.back();</script>";
        exit();
    }
    if (!in_array($diet, ['Vegetarian','Non-Vegetarian','Vegan'])) {
        echo "<script>alert('Please select a dietary preference.'); window.history.back();</script>";
        exit();
    }

    $hashed = password_hash($pass, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, preference) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $first, $last, $email, $hashed, $diet);

    if ($stmt->execute()) {
        // Regenerate CSRF token and log user in immediately after registration
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['user'] = $email;
        $_SESSION['attempts'] = 0;
        echo "<script>alert('Registration Successful! Welcome to FoodFusion.'); window.location='Home.php';</script>";
    } else {
        echo "<script>alert('An account with that email already exists.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
