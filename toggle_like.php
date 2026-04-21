<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in to like recipes.']);
    exit();
}

$recipe_id = (int)($_POST['recipe_id'] ?? 0);
if ($recipe_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid recipe.']);
    exit();
}

// Get user_id
$uStmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
$uStmt->bind_param("s", $_SESSION['user']);
$uStmt->execute();
$uRow = $uStmt->get_result()->fetch_assoc();
$user_id = $uRow['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'User not found.']);
    exit();
}

// Check if already liked
$chk = $conn->prepare("SELECT id FROM recipe_likes WHERE user_id=? AND recipe_id=?");
$chk->bind_param("ii", $user_id, $recipe_id);
$chk->execute();
$exists = $chk->get_result()->num_rows > 0;

if ($exists) {
    // Unlike
    $del = $conn->prepare("DELETE FROM recipe_likes WHERE user_id=? AND recipe_id=?");
    $del->bind_param("ii", $user_id, $recipe_id);
    $del->execute();
    $conn->query("UPDATE recipes SET likes = GREATEST(0, likes - 1) WHERE recipe_id = $recipe_id");
    $liked = false;
} else {
    // Like
    $ins = $conn->prepare("INSERT INTO recipe_likes (user_id, recipe_id) VALUES (?,?)");
    $ins->bind_param("ii", $user_id, $recipe_id);
    $ins->execute();
    $conn->query("UPDATE recipes SET likes = likes + 1 WHERE recipe_id = $recipe_id");
    $liked = true;
}

// Get updated like count
$lRow = $conn->query("SELECT likes FROM recipes WHERE recipe_id = $recipe_id")->fetch_assoc();
echo json_encode(['success' => true, 'liked' => $liked, 'likes' => (int)$lRow['likes']]);
$conn->close();
?>
