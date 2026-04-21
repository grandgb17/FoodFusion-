<?php
session_start();
require 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { http_response_code(400); exit('Invalid recipe ID'); }

$stmt = $conn->prepare("SELECT * FROM recipes WHERE recipe_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$r = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$r) { http_response_code(404); exit('Recipe not found'); }

// Build a clean plain-text recipe card content
$lines = [];
$lines[] = str_repeat('=', 60);
$lines[] = '  FOODFUSION RECIPE CARD';
$lines[] = str_repeat('=', 60);
$lines[] = '';
$lines[] = 'RECIPE: ' . $r['title'];
$lines[] = str_repeat('-', 60);

if ($r['cuisine'])    $lines[] = 'Cuisine   : ' . $r['cuisine'];
if ($r['category'])   $lines[] = 'Category  : ' . ucfirst($r['category']);
if ($r['difficulty']) $lines[] = 'Difficulty: ' . $r['difficulty'];
if ($r['dietary'])    $lines[] = 'Dietary   : ' . $r['dietary'];
if ($r['prep_time'])  $lines[] = 'Prep Time : ' . $r['prep_time'];
if ($r['cook_time'])  $lines[] = 'Cook Time : ' . $r['cook_time'];
if ($r['serves'])     $lines[] = 'Serves    : ' . $r['serves'];
$lines[] = 'Author    : ' . ($r['author_name'] ?? 'FoodFusion');
$lines[] = '';

if ($r['description']) {
    $lines[] = 'DESCRIPTION';
    $lines[] = str_repeat('-', 40);
    $lines[] = wordwrap($r['description'], 60, "\n", true);
    $lines[] = '';
}

if ($r['story']) {
    $lines[] = 'STORY';
    $lines[] = str_repeat('-', 40);
    $lines[] = wordwrap($r['story'], 60, "\n", true);
    $lines[] = '';
}

if ($r['ingredients']) {
    $lines[] = 'INGREDIENTS';
    $lines[] = str_repeat('-', 40);
    $items = preg_split('/\r?\n|\|/', $r['ingredients']);
    foreach ($items as $item) {
        $item = trim($item);
        if ($item) $lines[] = '  * ' . $item;
    }
    $lines[] = '';
}

if ($r['instructions']) {
    $lines[] = 'INSTRUCTIONS';
    $lines[] = str_repeat('-', 40);
    $steps = preg_split('/\r?\n|\|/', $r['instructions']);
    $n = 1;
    foreach ($steps as $step) {
        $step = trim($step);
        if ($step) { $lines[] = $n . '. ' . wordwrap($step, 56, "\n   ", true); $n++; }
    }
    $lines[] = '';
}

if ($r['tips']) {
    $lines[] = "CHEF'S TIPS";
    $lines[] = str_repeat('-', 40);
    $tips = preg_split('/\r?\n|\|/', $r['tips']);
    foreach ($tips as $tip) {
        $tip = trim($tip);
        if ($tip) $lines[] = '  - ' . wordwrap($tip, 56, "\n    ", true);
    }
    $lines[] = '';
}

// Nutrition
$hasNutrition = $r['calories'] || $r['protein'] || $r['carbs'] || $r['fat'];
if ($hasNutrition) {
    $lines[] = 'NUTRITION (per serving)';
    $lines[] = str_repeat('-', 40);
    if ($r['calories']) $lines[] = '  Calories : ' . $r['calories'];
    if ($r['protein'])  $lines[] = '  Protein  : ' . $r['protein'];
    if ($r['carbs'])    $lines[] = '  Carbs    : ' . $r['carbs'];
    if ($r['fat'])      $lines[] = '  Fat      : ' . $r['fat'];
    $lines[] = '';
}

$lines[] = str_repeat('=', 60);
$lines[] = '  FoodFusion | foodfusion.com';
$lines[] = '  "Cooking is love made visible."';
$lines[] = str_repeat('=', 60);

$content  = implode("\n", $lines);
$filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $r['title']) . '_recipe.txt';

header('Content-Type: text/plain; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . strlen($content));
header('Cache-Control: no-cache');
echo $content;
exit;
?>
