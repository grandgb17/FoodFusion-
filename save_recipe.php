<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

// Catch any PHP errors and return as JSON instead of HTML
set_error_handler(function($errno, $errstr) {
    echo json_encode(['success' => false, 'message' => "PHP Error: $errstr"]);
    exit();
});

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}

// ── Required fields ──────────────────────────────────────────
$title    = trim($_POST['title']       ?? '');
$desc     = trim($_POST['description'] ?? '');
$category = trim($_POST['category']    ?? 'general');
$author   = trim($_POST['author']      ?? '');

// ── Optional fields ──────────────────────────────────────────
$cooktime   = trim($_POST['cooktime']   ?? '');
$preptime   = trim($_POST['preptime']   ?? '');
$serves     = trim($_POST['serves']     ?? '');
$cuisine    = trim($_POST['cuisine']    ?? '');
$story      = trim($_POST['story']      ?? '');

// ENUM-safe fields — must match DB allowed values
$difficulty_raw = trim($_POST['difficulty'] ?? '');
$dietary_raw    = trim($_POST['dietary']    ?? '');
$difficulty = in_array($difficulty_raw, ['Easy','Medium','Hard'])                     ? $difficulty_raw : 'Easy';
$dietary    = in_array($dietary_raw,    ['Vegetarian','Non-Vegetarian','Vegan'])       ? $dietary_raw    : 'Non-Vegetarian';

// ── Pipe-separated text fields ───────────────────────────────
$ingredients_raw  = trim($_POST['ingredients'] ?? '');
$instructions_raw = trim($_POST['instructions'] ?? '');
$chef_tips_raw    = trim($_POST['chef_tips']    ?? '');

$ingredients  = implode('|', array_filter(array_map('trim', explode("\n", $ingredients_raw))));
$instructions = implode('|', array_filter(array_map('trim', explode("\n", $instructions_raw))));
$tips         = implode('|', array_filter(array_map('trim', explode("\n", $chef_tips_raw))));

// ── Nutrition (all optional) ─────────────────────────────────
$calories = trim($_POST['calories'] ?? '');
$protein  = trim($_POST['protein']  ?? '');
$carbs    = trim($_POST['carbs']    ?? '');
$fat      = trim($_POST['fat']      ?? '');

// ── Image upload ──────────────────────────────────────────────
$image_path = null;
if (!empty($_FILES['recipe_image']['tmp_name'])) {
    $file     = $_FILES['recipe_image'];
    $allowed  = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $maxBytes = 5 * 1024 * 1024; // 5 MB

    // Validate MIME type using finfo (not just extension)
    $finfo    = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    if (!array_key_exists($mimeType, $allowed)) {
        echo json_encode(['success' => false, 'message' => 'Only JPG, PNG, or WEBP images are allowed.']);
        exit();
    }
    if ($file['size'] > $maxBytes) {
        echo json_encode(['success' => false, 'message' => 'Image must be under 5 MB.']);
        exit();
    }

    $uploadDir = 'imgs/recipes/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $ext       = $allowed[$mimeType];
    $filename  = 'recipe_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
    $destPath  = $uploadDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        echo json_encode(['success' => false, 'message' => 'Could not save image. Check folder permissions.']);
        exit();
    }
    $image_path = $destPath;
}

// ── Validation ───────────────────────────────────────────────
if (empty($title) || empty($desc) || empty($ingredients) || empty($instructions)) {
    echo json_encode(['success' => false, 'message' => 'Title, description, ingredients and instructions are required.']);
    exit();
}

// ── Resolve user ─────────────────────────────────────────────
$user_id = null;
if (isset($_SESSION['user'])) {
    $uStmt = $conn->prepare("SELECT user_id, first_name FROM users WHERE email = ?");
    if ($uStmt) {
        $uStmt->bind_param("s", $_SESSION['user']);
        $uStmt->execute();
        $uRow = $uStmt->get_result()->fetch_assoc();
        $user_id = $uRow['user_id'] ?? null;
        if ($user_id && empty($author)) {
            $author = $uRow['first_name'] ?? '';
        }
        $uStmt->close();
    }
}
if (empty($author)) $author = 'Anonymous Chef';

// ── Emoji map ────────────────────────────────────────────────
$emojiMap = ['pasta'=>'🍝','curry'=>'🍛','dessert'=>'🍰','healthy'=>'🥗','noodles'=>'🍜','general'=>'🍽'];
$emoji    = $emojiMap[$category] ?? '🍽';

// ── Insert ───────────────────────────────────────────────────
$sql = "INSERT INTO recipes
            (user_id, title, description, category, cook_time, prep_time, serves,
             difficulty, cuisine, dietary, story, ingredients, instructions, tips,
             calories, protein, carbs, fat, author_name, emoji, image_path, source)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'community')";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'DB prepare error: ' . $conn->error]);
    exit();
}

$stmt->bind_param(
    "issssssssssssssssssss",
    $user_id, $title, $desc, $category,
    $cooktime, $preptime, $serves,
    $difficulty, $cuisine, $dietary, $story,
    $ingredients, $instructions, $tips,
    $calories, $protein, $carbs, $fat,
    $author, $emoji, $image_path
);

if ($stmt->execute()) {
    $newId = $conn->insert_id;
    echo json_encode([
        'success'     => true,
        'message'     => 'Recipe published!',
        'recipe_id'   => $newId,
        'title'       => $title,
        'description' => $desc,
        'category'    => $category,
        'cooktime'    => $cooktime,
        'author'      => $author,
        'image_path'  => $image_path,
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'DB execute error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>