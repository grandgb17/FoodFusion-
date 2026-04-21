<?php
/**
 * video_proxy.php  –  FoodFusion real-video downloader
 * Streams Wikimedia Commons cooking videos via cURL to the browser.
 * Usage: video_proxy.php?id=scrambled_eggs
 */

$videos = [
    'scrambled_eggs' => [
        'url'      => 'https://upload.wikimedia.org/wikipedia/commons/c/ce/Scrambled_eggs_with_mushrooms_and_cheese.webm',
        'filename' => 'Scrambled_Eggs_with_Mushrooms.webm',
        'mime'     => 'video/webm',
    ],
    'salmon' => [
        'url'      => 'https://upload.wikimedia.org/wikipedia/commons/2/2a/Salmon_20200410_113456~2.webm',
        'filename' => 'Pan_Seared_Salmon.webm',
        'mime'     => 'video/webm',
    ],
    'sourdough' => [
        'url'      => 'https://upload.wikimedia.org/wikipedia/commons/b/b3/Rye_sourdough_starter_culture_rising.webm',
        'filename' => 'Sourdough_Starter_Rising.webm',
        'mime'     => 'video/webm',
    ],
    'cooking_lunch' => [
        'url'      => 'https://upload.wikimedia.org/wikipedia/commons/1/1d/Cooking_lunch.webm',
        'filename' => 'Cooking_Lunch_Basics.webm',
        'mime'     => 'video/webm',
    ],
    'food_safety' => [
        'url'      => 'https://upload.wikimedia.org/wikipedia/commons/4/42/Gear_Up_for_Food_Safety.webm',
        'filename' => 'Food_Safety_Guide.webm',
        'mime'     => 'video/webm',
    ],
    'food_safety2' => [
        'url'      => 'https://upload.wikimedia.org/wikipedia/commons/e/eb/Food_Safety_Video.webm',
        'filename' => 'Food_Safety_Basics.webm',
        'mime'     => 'video/webm',
    ],
    'pineapple_prep' => [
        'url'      => 'https://upload.wikimedia.org/wikipedia/commons/c/c4/Preparing_pineapple_-_01.ogv',
        'filename' => 'Preparing_a_Pineapple.ogv',
        'mime'     => 'video/ogg',
    ],
    'cooking_show' => [
        'url'      => 'https://upload.wikimedia.org/wikipedia/commons/b/bb/Rae_Dawn_Chong_-_Cooking_Show_Pilot%2C_2008.webm',
        'filename' => 'Full_Cooking_Show_Episode.webm',
        'mime'     => 'video/webm',
    ],
    'fermentation' => [
        'url'      => 'https://upload.wikimedia.org/wikipedia/commons/b/b3/Rye_sourdough_starter_culture_rising.webm',
        'filename' => 'Fermentation_in_Action.webm',
        'mime'     => 'video/webm',
    ],
];

// Validate ID
$id = trim($_GET['id'] ?? '');
if (!$id || !isset($videos[$id])) {
    http_response_code(404);
    die('Video not found.');
}

$v         = $videos[$id];
$remoteUrl = $v['url'];
$filename  = $v['filename'];
$mime      = $v['mime'];

if (!function_exists('curl_init')) {
    http_response_code(500);
    die('cURL extension is not available on this server.');
}

// Step 1: HEAD request to get file size
$ch = curl_init($remoteUrl);
curl_setopt_array($ch, [
    CURLOPT_NOBODY         => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_MAXREDIRS      => 5,
    CURLOPT_TIMEOUT        => 20,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; FoodFusion/1.0)',
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_SSL_VERIFYHOST => 2,
]);
curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$filesize = (int) curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
$curlErr  = curl_error($ch);
curl_close($ch);

if ($httpCode !== 200) {
    http_response_code(502);
    die("Could not reach video source (HTTP $httpCode). " . htmlspecialchars($curlErr));
}

// Step 2: Clear output buffers
while (ob_get_level()) ob_end_clean();

// Step 3: Send download headers
header('Content-Type: ' . $mime);
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
header('X-Content-Type-Options: nosniff');
if ($filesize > 0) {
    header('Content-Length: ' . $filesize);
}

// Step 4: Stream the file to the browser
set_time_limit(0);
ignore_user_abort(false);

$ch = curl_init($remoteUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => false,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_MAXREDIRS      => 5,
    CURLOPT_TIMEOUT        => 0,
    CURLOPT_CONNECTTIMEOUT => 20,
    CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; FoodFusion/1.0)',
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_SSL_VERIFYHOST => 2,
    CURLOPT_BUFFERSIZE     => 262144, // 256 KB chunks
    CURLOPT_WRITEFUNCTION  => function($ch, $chunk) {
        if (connection_aborted()) return -1;
        echo $chunk;
        flush();
        return strlen($chunk);
    },
]);

$ok = curl_exec($ch);
if (!$ok && !headers_sent()) {
    http_response_code(502);
    echo 'Stream error: ' . htmlspecialchars(curl_error($ch));
}
curl_close($ch);
exit;
?>