<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

// Ensure the culinary_tips table exists
$conn->query("CREATE TABLE IF NOT EXISTS culinary_tips (
    tip_id      INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT DEFAULT NULL,
    author_name VARCHAR(100) DEFAULT 'Anonymous',
    tip_type    ENUM('tip','experience','recipe_hack','technique','other') DEFAULT 'tip',
    title       VARCHAR(150) NOT NULL,
    content     TEXT NOT NULL,
    cuisine     VARCHAR(50) DEFAULT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
)");

$author  = trim($_POST['author'] ?? '');
$tipType = trim($_POST['tip_type'] ?? 'tip');
$title   = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');
$cuisine = trim($_POST['cuisine'] ?? '');

if (!$title || !$content) {
    echo json_encode(['success' => false, 'message' => 'Title and content are required.']);
    exit;
}

$allowedTypes = ['tip', 'experience', 'recipe_hack', 'technique', 'other'];
if (!in_array($tipType, $allowedTypes)) $tipType = 'tip';

$userId = null;
if (isset($_SESSION['user'])) {
    $uStmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $uStmt->bind_param("s", $_SESSION['user']);
    $uStmt->execute();
    $uRow = $uStmt->get_result()->fetch_assoc();
    $uStmt->close();
    if ($uRow) $userId = $uRow['user_id'];
}

if (!$author) $author = 'Anonymous Chef';

$stmt = $conn->prepare("INSERT INTO culinary_tips (user_id, author_name, tip_type, title, content, cuisine) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssss", $userId, $author, $tipType, $title, $content, $cuisine);

if ($stmt->execute()) {
    $tipId = $conn->insert_id;
    echo json_encode([
        'success'     => true,
        'tip_id'      => $tipId,
        'author_name' => htmlspecialchars($author),
        'tip_type'    => $tipType,
        'title'       => htmlspecialchars($title),
        'content'     => htmlspecialchars($content),
        'cuisine'     => htmlspecialchars($cuisine)
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error. Please try again.']);
}
$stmt->close();
exit;
?>
