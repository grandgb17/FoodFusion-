<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in to save recipes.']);
    exit();
}

$recipe_id = (int)($_POST['recipe_id'] ?? 0);
if ($recipe_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid recipe.']);
    exit();
}

$uStmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
$uStmt->bind_param("s", $_SESSION['user']);
$uStmt->execute();
$uRow = $uStmt->get_result()->fetch_assoc();
$user_id = $uRow['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'User not found.']);
    exit();
}

$chk = $conn->prepare("SELECT id FROM saved_recipes WHERE user_id=? AND recipe_id=?");
$chk->bind_param("ii", $user_id, $recipe_id);
$chk->execute();
$exists = $chk->get_result()->num_rows > 0;

if ($exists) {
    $del = $conn->prepare("DELETE FROM saved_recipes WHERE user_id=? AND recipe_id=?");
    $del->bind_param("ii", $user_id, $recipe_id);
    $del->execute();
    echo json_encode(['success' => true, 'saved' => false]);
} else {
    $ins = $conn->prepare("INSERT INTO saved_recipes (user_id, recipe_id) VALUES (?,?)");
    $ins->bind_param("ii", $user_id, $recipe_id);
    $ins->execute();
    echo json_encode(['success' => true, 'saved' => true]);
}

$conn->close();
?>
