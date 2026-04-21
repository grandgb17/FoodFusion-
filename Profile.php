<?php
session_start();
require 'db.php';

// Generate CSRF token if not set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if(!isset($_SESSION['user'])){
    header("Location: Home.php");
    exit();
}

$email = $_SESSION['user'];

// Fetch user
$stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if(!$user){ session_destroy(); header("Location: Home.php"); exit(); }

// Count recipes posted
$rStmt = $conn->prepare("SELECT COUNT(*) as total FROM recipes WHERE user_id=?");
$rStmt->bind_param("i", $user['user_id']);
$rStmt->execute();
$recipeCount = $rStmt->get_result()->fetch_assoc()['total'] ?? 0;

// Total likes received on user's recipes
$lStmt = $conn->prepare("SELECT COALESCE(SUM(likes),0) as total FROM recipes WHERE user_id=?");
$lStmt->bind_param("i", $user['user_id']);
$lStmt->execute();
$totalLikes = $lStmt->get_result()->fetch_assoc()['total'] ?? 0;

// Saved recipes
$sStmt = $conn->prepare(
    "SELECT r.* FROM recipes r
     INNER JOIN saved_recipes s ON r.recipe_id = s.recipe_id
     WHERE s.user_id = ?
     ORDER BY s.saved_at DESC"
);
$sStmt->bind_param("i", $user['user_id']);
$sStmt->execute();
$savedRecipes = $sStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Success/error message
$msg = '';
if (isset($_GET['updated'])) $msg = 'success';
if (isset($_GET['error']))   $msg = 'error';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FoodFusion | Profile</title>
<link rel="stylesheet" href="Home1.css">
<link rel="stylesheet" href="profile.css">
</head>
<body>

<header>
    <div class="logo">🍽 FoodFusion</div>
        <nav>
        <a href="Home.php">Home</a>
        <a href="about.php">About</a>
        <a href="Recipe.php">Recipes</a>
        <a href="COOKBOOK.php">CookBook</a>
        <a href="culinary_resources.php">Resources</a>
        <a href="educational_resources.php">Learn</a>
        <a href="contact.php">Contact</a>
        <button class="nav-btn logout-btn" onclick="window.location.href='logout.php'">Logout</button>
    </nav>
        <button class="hamburger" id="hamburger" onclick="toggleNav()" aria-label="Toggle navigation">
            <span></span><span></span><span></span>
        </button>
</header>

<section class="profile-container">

<div class="profile-card">

    <?php if($msg === 'success'): ?>
    <div style="background:#d4edda;color:#155724;padding:12px 16px;border-radius:10px;margin-bottom:16px;font-size:14px;">✅ Profile updated successfully.</div>
    <?php elseif($msg === 'error'): ?>
    <div style="background:#f8d7da;color:#721c24;padding:12px 16px;border-radius:10px;margin-bottom:16px;font-size:14px;">❌ Could not update profile. Please try again.</div>
    <?php endif; ?>

    <h2>Welcome, <?php echo htmlspecialchars($user['first_name']); ?>! 👋</h2>

    <div class="stats">
        <div class="stat-box">
            <h3>Recipes Posted</h3>
            <p><?php echo $recipeCount; ?></p>
        </div>
        <div class="stat-box">
            <h3>Total Likes</h3>
            <p><?php echo $totalLikes; ?></p>
        </div>
        <div class="stat-box">
            <h3>Saved Recipes</h3>
            <p><?php echo count($savedRecipes); ?></p>
        </div>
    </div>

    <h3>Your Details</h3>

    <form action="update_profile.php" method="POST" id="profileForm">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

        <label class="field-label">First Name</label>
        <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" disabled>

        <label class="field-label">Last Name</label>
        <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" disabled>

        <label class="field-label">Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>

        <label class="field-label">New Password</label>
        <input type="password" name="password" placeholder="Leave blank to keep current" disabled>

        <p class="field-label">Dietary Preference</p>
        <div class="diet-options">
            <label><input type="radio" name="diet" value="Vegetarian" <?php if($user['preference']==="Vegetarian") echo "checked"; ?> disabled> Vegetarian</label>
            <label><input type="radio" name="diet" value="Non-Vegetarian" <?php if($user['preference']==="Non-Vegetarian") echo "checked"; ?> disabled> Non-Vegetarian</label>
            <label><input type="radio" name="diet" value="Vegan" <?php if($user['preference']==="Vegan") echo "checked"; ?> disabled> Vegan</label>
        </div>

        <br>
        <button type="button" id="editBtn" onclick="enableEdit()">Edit Profile</button>
        <button type="submit" id="saveBtn" style="display:none;">Save Changes</button>
        <button type="button" id="cancelBtn" style="display:none;" onclick="cancelEdit()">Cancel</button>

    </form>
</div>

<!-- SAVED RECIPES -->
<div class="saved-recipes">
    <h2>Saved Recipes ⭐</h2>
    <?php if(empty($savedRecipes)): ?>
    <div class="recipe-grid">
        <div class="recipe-card" style="text-align:center; padding:30px;">
            <h4 style="color:#aaa;">No saved recipes yet</h4>
            <p style="color:#ccc; font-size:13px;">Browse <a href="Recipe.php" style="color:#ff6f3c;">Recipes</a> or the <a href="COOKBOOK.php" style="color:#ff6f3c;">Cookbook</a> and double-click any recipe to save it.</p>
        </div>
    </div>
    <?php else: ?>
    <div class="recipe-grid">
        <?php
        $emojis = ['pasta'=>'🍝','curry'=>'🍛','dessert'=>'🍰','healthy'=>'🥗','general'=>'🍽','noodles'=>'🍜'];
        foreach($savedRecipes as $r):
            $emoji = $emojis[$r['category']] ?? '🍽';
            $src   = $r['source'] === 'community' ? 'community' : 'collection';
        ?>
        <div class="recipe-card" style="cursor:pointer;"
             ondblclick="window.location.href='recipe_detail.php?id=<?php echo $r['recipe_id']; ?>&src=<?php echo $src; ?>'">
            <div style="font-size:40px; text-align:center; padding:20px 0 10px; background:#FFF1EB;"><?php echo $emoji; ?></div>
            <div style="padding:14px 16px;">
                <h4 style="margin:0 0 6px; color:#333; font-size:15px;"><?php echo htmlspecialchars($r['title']); ?></h4>
                <p style="font-size:12px; color:#888; margin:0 0 10px;"><?php echo htmlspecialchars($r['cuisine'] ?? ''); ?> · <?php echo htmlspecialchars($r['difficulty'] ?? ''); ?></p>
                <button onclick="unsaveRecipe(<?php echo $r['recipe_id']; ?>, this)"
                    style="background:#fde0e0;color:#c0392b;border:none;border-radius:8px;padding:5px 12px;font-size:12px;cursor:pointer;font-weight:600;">
                    🗑 Remove
                </button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

</section>

<script>
const originalValues = {};

function enableEdit(){
    document.querySelectorAll('#profileForm input').forEach(input => {
        originalValues[input.name] = input.type === 'radio' ? input.checked : input.value;
        input.disabled = false;
    });
    document.getElementById('saveBtn').style.display   = 'inline-block';
    document.getElementById('cancelBtn').style.display = 'inline-block';
    document.getElementById('editBtn').style.display   = 'none';
}

function cancelEdit(){
    document.querySelectorAll('#profileForm input').forEach(input => {
        input.disabled = true;
        if(input.type === 'radio') input.checked = originalValues[input.name];
        else input.value = originalValues[input.name] || '';
    });
    document.getElementById('saveBtn').style.display   = 'none';
    document.getElementById('cancelBtn').style.display = 'none';
    document.getElementById('editBtn').style.display   = 'inline-block';
}

function unsaveRecipe(recipeId, btn) {
    const data = new FormData();
    data.append('recipe_id', recipeId);
    fetch('toggle_save.php', { method: 'POST', body: data })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                btn.closest('.recipe-card').remove();
            }
        });
}
</script>

<footer>
    <div class="social">
        <a href="https://www.facebook.com/foodfusion" target="_blank" rel="noopener noreferrer">Facebook</a>
        <a href="https://www.instagram.com/foodfusion" target="_blank" rel="noopener noreferrer">Instagram</a>
        <a href="https://www.youtube.com/foodfusion" target="_blank" rel="noopener noreferrer">YouTube</a>
    </div>
    <p><a href="culinary_resources.php">Culinary Resources</a> | <a href="educational_resources.php">Educational Resources</a> | <a href="privacy_policy.php">Privacy Policy</a> | &copy; 2026 FoodFusion</p>
</footer>


<script>
function toggleNav() {
    var nav = document.querySelector('header nav');
    var btn = document.getElementById('hamburger');
    nav.classList.toggle('open');
    btn.classList.toggle('open');
}
// Close menu when a nav link is clicked
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('header nav a').forEach(function(link) {
        link.addEventListener('click', function() {
            document.querySelector('header nav').classList.remove('open');
            document.getElementById('hamburger').classList.remove('open');
        });
    });
});
</script>
</body>
</html>
