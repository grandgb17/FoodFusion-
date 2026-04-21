<?php
session_start();
require 'db.php';

// Fetch logged-in user's dietary preference
$userPreference = 'Non-Vegetarian'; // default — sees everything
if (isset($_SESSION['user'])) {
    $pStmt = $conn->prepare("SELECT preference FROM users WHERE email = ?");
    $pStmt->bind_param("s", $_SESSION['user']);
    $pStmt->execute();
    $pRow = $pStmt->get_result()->fetch_assoc();
    $userPreference = $pRow['preference'] ?? 'Non-Vegetarian';
    $pStmt->close();
}

// Fetch all collection recipes from DB
$sql = "SELECT * FROM recipes WHERE source='collection' ORDER BY created_at DESC";
$result = $conn->query($sql);
$recipes = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $recipes[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FoodFusion | Recipes</title>
<link rel="stylesheet" href="Home1.css">
<link rel="stylesheet" href="Recipes.css">

</head>
<body>

<!-- NAVBAR -->
<header>
    <div class="logo">🍽 FoodFusion</div>
        <nav>
        <a href="Home.php">Home</a>
        <a href="about.php">About</a>
        <a href="Recipe.php" class="active">Recipes</a>
        <a href="COOKBOOK.php">CookBook</a>
        <a href="culinary_resources.php">Resources</a>
        <a href="educational_resources.php">Learn</a>
        <a href="contact.php">Contact</a>
        <?php if(isset($_SESSION['user'])){ ?>
        <button class="nav-btn" onclick="window.location.href='profile.php'">Profile</button>
        <?php } else { ?>
        <button class="nav-btn" onclick="window.location.href='Home.php?join=1'">Join Us</button>
        <?php } ?>
    </nav>
        <button class="hamburger" id="hamburger" onclick="toggleNav()" aria-label="Toggle navigation">
            <span></span><span></span><span></span>
        </button>
</header>

<!-- PAGE TITLE -->
<section class="page-title" style="background: linear-gradient(135deg, #ff6f3c 0%, #ff9a3c 60%, #ffb347 100%);">
    <div class="page-title-inner">
        <span class="page-title-eyebrow">🍽 Our Kitchen</span>
        <h1 style="color:white;">Recipe Collection</h1>
        <p style="color:rgba(255,255,255,0.85);">Explore hand-picked recipes from around the world — click any card to see the full recipe</p>
    </div>
</section>

<!-- FILTERS -->
<section class="filters">
    <input type="text" id="search" placeholder="Search recipe...">

    <select id="cuisineFilter">
        <option value="all">All Cuisines</option>
        <option value="Italian">Italian</option>
        <option value="Indian">Indian</option>
        <option value="Mexican">Mexican</option>
        <option value="Asian">Asian</option>
        <option value="French">French</option>
        <option value="Middle Eastern">Middle Eastern</option>
    </select>

    <select id="difficultyFilter">
        <option value="all">All Difficulty</option>
        <option value="Easy">Easy</option>
        <option value="Medium">Medium</option>
        <option value="Hard">Hard</option>
    </select>
</section>

<!-- Pass user preference to JS -->
<script>
    const USER_PREFERENCE = <?php echo json_encode($userPreference); ?>;
</script>

<!-- RECIPE CARDS -->
<section class="recipes-container" id="recipes">
<?php if (empty($recipes)): ?>
    <div style="grid-column:1/-1; text-align:center; padding:80px 20px;">
        <div style="font-size:64px; margin-bottom:20px;">🍽</div>
        <h3 style="color:#aaa; font-size:22px; margin-bottom:10px;">No recipes yet</h3>
        <p style="color:#ccc; font-size:15px; margin-bottom:24px;">
            Run <code>insert_recipes.sql</code> in phpMyAdmin to load sample recipes.
        </p>
        <a href="COOKBOOK.php" style="background:#ff6f3c;color:white;padding:12px 28px;border-radius:25px;text-decoration:none;font-weight:700;">
            Browse Community Cookbook →
        </a>
    </div>
<?php else: ?>
    <?php foreach($recipes as $r):
        $diffClass = strtolower($r['difficulty']);
    ?>
    <div class="recipe-card"
         data-id="<?php echo $r['recipe_id']; ?>"
         data-cuisine="<?php echo htmlspecialchars($r['cuisine']); ?>"
         data-difficulty="<?php echo htmlspecialchars($r['difficulty']); ?>"
         data-dietary="<?php echo htmlspecialchars($r['dietary']); ?>">
        <div class="recipe-card-img" data-likes="❤️ <?php echo (int)$r['likes']; ?>">
            <?php
            $imgSrc = !empty($r['image'])
                ? 'imgs_recipe/' . htmlspecialchars($r['image'], ENT_QUOTES)
                : 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=400&q=80';
            ?>
            <img src="<?php echo $imgSrc; ?>" alt="<?php echo htmlspecialchars($r['title']); ?>" loading="lazy">
            <div class="recipe-card-emoji-badge"><?php echo $r['emoji']; ?></div>
        </div>
        <div class="recipe-card-body">
            <div class="recipe-card-tags">
                <span class="tag-cuisine"><?php echo htmlspecialchars($r['cuisine']); ?></span>
                <span class="tag-diff <?php echo $diffClass; ?>"><?php echo htmlspecialchars($r['difficulty']); ?></span>
                <?php if($r['dietary'] === 'Vegetarian' || $r['dietary'] === 'Vegan'): ?>
                <span class="tag-diet"><?php echo $r['dietary'] === 'Vegan' ? '🌱 Vegan' : '🥦 Veg'; ?></span>
                <?php endif; ?>
            </div>
            <h3><?php echo htmlspecialchars($r['title']); ?></h3>
            <p><?php echo htmlspecialchars($r['description']); ?></p>
            <div class="recipe-card-meta">
                <?php if($r['cook_time']): ?><span>⏱ <?php echo htmlspecialchars($r['cook_time']); ?></span><?php endif; ?>
                <?php if($r['serves']): ?><span>👥 Serves <?php echo htmlspecialchars($r['serves']); ?></span><?php endif; ?>
            </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>
</section>

<div id="no-results" style="display:none; text-align:center; padding:60px; color:#aaa; font-size:16px;">
    😔 No recipes found. Try a different filter!
</div>

<!-- FOOTER -->
<footer>
    <div class="social">
        <a href="https://www.facebook.com/foodfusion" target="_blank" rel="noopener noreferrer">Facebook</a>
        <a href="https://www.instagram.com/foodfusion" target="_blank" rel="noopener noreferrer">Instagram</a>
        <a href="https://www.youtube.com/foodfusion" target="_blank" rel="noopener noreferrer">YouTube</a>
    </div>
    <p><a href="culinary_resources.php">Culinary Resources</a> | <a href="educational_resources.php">Educational Resources</a> | <a href="privacy_policy.php">Privacy Policy</a> | &copy; 2026 FoodFusion</p>
</footer>

<script src="Recipe1.js"></script>

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