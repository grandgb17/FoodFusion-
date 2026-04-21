<?php
session_start();
require 'db.php';

$recipes = [];
$result  = $conn->query("SELECT * FROM recipes ORDER BY source ASC, created_at DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) $recipes[] = $row;
}

if (empty($recipes)) {
    http_response_code(404);
    echo 'No recipes available to download.';
    exit;
}

// Build individual recipe text content
function buildRecipeText($r) {
    $lines   = [];
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
        foreach (preg_split('/\r?\n|\|/', $r['ingredients']) as $item) {
            $item = trim($item);
            if ($item) $lines[] = '  * ' . $item;
        }
        $lines[] = '';
    }
    if ($r['instructions']) {
        $lines[] = 'INSTRUCTIONS';
        $lines[] = str_repeat('-', 40);
        $n = 1;
        foreach (preg_split('/\r?\n|\|/', $r['instructions']) as $step) {
            $step = trim($step);
            if ($step) { $lines[] = $n . '. ' . wordwrap($step, 56, "\n   ", true); $n++; }
        }
        $lines[] = '';
    }
    if ($r['tips']) {
        $lines[] = "CHEF'S TIPS";
        $lines[] = str_repeat('-', 40);
        foreach (preg_split('/\r?\n|\|/', $r['tips']) as $tip) {
            $tip = trim($tip);
            if ($tip) $lines[] = '  - ' . wordwrap($tip, 56, "\n    ", true);
        }
        $lines[] = '';
    }
    if ($r['calories'] || $r['protein'] || $r['carbs'] || $r['fat']) {
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
    return implode("\n", $lines);
}

// Create a temporary ZIP file
$tmpZip  = sys_get_temp_dir() . '/foodfusion_recipes_' . time() . '.zip';
$zip     = new ZipArchive();

if ($zip->open($tmpZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    http_response_code(500);
    echo 'Could not create ZIP archive.';
    exit;
}

foreach ($recipes as $r) {
    $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $r['title']) . '_recipe.txt';
    $folder   = $r['source'] === 'community' ? 'community/' : 'collection/';
    $zip->addFromString($folder . $filename, buildRecipeText($r));
}

// Add a readme
$readme  = "FOODFUSION RECIPE BUNDLE\n";
$readme .= str_repeat('=', 40) . "\n\n";
$readme .= "This bundle contains " . count($recipes) . " recipes:\n";
$readme .= "  - collection/ : FoodFusion curated recipes\n";
$readme .= "  - community/  : Community-submitted recipes\n\n";
$readme .= "Visit us at foodfusion.com for more!\n";
$zip->addFromString('README.txt', $readme);

$zip->close();

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="FoodFusion_Recipes.zip"');
header('Content-Length: ' . filesize($tmpZip));
header('Cache-Control: no-cache');
readfile($tmpZip);
unlink($tmpZip);
exit;
?>
