<?php
session_start();
require 'db.php';

// Get recipe ID from URL
$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$source = isset($_GET['src']) ? $_GET['src'] : 'collection'; // 'collection' or 'community'

$recipe = null;

if ($id > 0) {
    $stmt = $conn->prepare("SELECT * FROM recipes WHERE recipe_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $recipe = $result->fetch_assoc();
    }
}

// Redirect back if recipe not found
if (!$recipe) {
    header("Location: " . ($source === 'community' ? "COOKBOOK.php" : "Recipe.php"));
    exit();
}

// Parse pipe-delimited OR newline-delimited fields (handles both collection and community recipes)
function parseField($value) {
    if (empty($value)) return [];
    // Normalise: replace newlines with pipes, then split on pipe
    $normalised = str_replace(["\r\n", "\r", "\n"], '|', $value);
    return array_values(array_filter(array_map('trim', explode('|', $normalised))));
}

$ingredients  = parseField($recipe['ingredients']);
$instructions = parseField($recipe['instructions']);
$tips         = parseField($recipe['tips']);

$backLink  = $source === 'community' ? 'COOKBOOK.php' : 'Recipe.php';
$backLabel = $source === 'community' ? '← Back to Cookbook' : '← Back to Recipes';

$diffClass = strtolower($recipe['difficulty'] ?? 'easy');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FoodFusion | <?php echo htmlspecialchars($recipe['title']); ?></title>
<link rel="stylesheet" href="Home1.css">
<link rel="stylesheet" href="recipe_details.css">
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

<!-- BACK BUTTON -->
<div class="back-bar">
    <a href="<?php echo $backLink; ?>" class="back-btn"><?php echo $backLabel; ?></a>
</div>

<!-- HERO BANNER -->
<section class="recipe-hero" <?php if(!empty($recipe['image'])): ?>style="background-image: linear-gradient(rgba(0,0,0,0.55), rgba(0,0,0,0.55)), url('imgs_recipe/<?php echo rawurlencode($recipe['image']); ?>'); background-size: cover; background-position: center;"<?php endif; ?>>
    <div class="hero-emoji"><?php echo $recipe['emoji'] ?? '🍽'; ?></div>
    <div class="hero-content">
        <div class="recipe-tag"><?php echo strtoupper(htmlspecialchars($recipe['cuisine'] ?? 'Recipe')); ?></div>
        <h1><?php echo htmlspecialchars($recipe['title']); ?></h1>
        <p class="recipe-subtitle"><?php echo htmlspecialchars($recipe['description']); ?></p>
        <div class="meta-pills">
            <span class="pill">👨‍🍳 <?php echo htmlspecialchars($recipe['author_name'] ?? 'Chef'); ?></span>
            <?php if($recipe['cook_time']): ?>
            <span class="pill">⏱ <?php echo htmlspecialchars($recipe['cook_time']); ?></span>
            <?php endif; ?>
            <?php if($recipe['serves']): ?>
            <span class="pill">🍽 Serves <?php echo htmlspecialchars($recipe['serves']); ?></span>
            <?php endif; ?>
            <?php if($recipe['difficulty']): ?>
            <span class="pill">📊 <?php echo htmlspecialchars($recipe['difficulty']); ?></span>
            <?php endif; ?>
            <?php if($recipe['dietary']): ?>
            <span class="pill">🥗 <?php echo htmlspecialchars($recipe['dietary']); ?></span>
            <?php endif; ?>
        </div>
        <div style="display:flex; gap:12px; flex-wrap:wrap;">
            <button class="like-hero-btn" id="likeBtn"
                    data-recipe-id="<?php echo $recipe['recipe_id']; ?>"
                    data-likes="<?php echo (int)$recipe['likes']; ?>"
                    onclick="toggleLike(this)">
                ❤️ <?php echo (int)$recipe['likes']; ?> Likes
            </button>
            <button class="like-hero-btn" id="saveBtn"
                    data-recipe-id="<?php echo $recipe['recipe_id']; ?>"
                    onclick="toggleSave(this)"
                    style="background:rgba(255,255,255,0.15);border:2px solid rgba(255,255,255,0.5);">
                ⭐ Save Recipe
            </button>
            <button class="like-hero-btn" id="downloadBtn"
                    onclick="downloadRecipe()"
                    style="background:rgba(255,255,255,0.15);border:2px solid rgba(255,255,255,0.5);">
                ⬇️ Download PDF
            </button>
        </div>
    </div>
</section>

<!-- MAIN CONTENT -->
<div class="detail-layout">

    <!-- LEFT: Story + Ingredients + Steps -->
    <div class="detail-main">

        <!-- STORY -->
        <?php if(!empty($recipe['story'])): ?>
        <div class="detail-card">
            <h2 class="section-title">📖 The Story</h2>
            <p class="story-text"><?php echo nl2br(htmlspecialchars($recipe['story'])); ?></p>
        </div>
        <?php endif; ?>

        <!-- INGREDIENTS -->
        <?php if(!empty($ingredients)): ?>
        <div class="detail-card">
            <h2 class="section-title">🛒 Ingredients</h2>
            <div class="ingredients-grid">
                <div class="ingredient-group">
                    <ul>
                        <?php foreach($ingredients as $ing): ?>
                        <li><span class="qty">•</span> <?php echo htmlspecialchars(trim($ing)); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- INSTRUCTIONS -->
        <?php if(!empty($instructions)): ?>
        <div class="detail-card">
            <h2 class="section-title">👩‍🍳 Instructions</h2>
            <ol class="steps-list">
                <?php foreach($instructions as $i => $step): ?>
                <li class="step">
                    <div class="step-num"><?php echo $i + 1; ?></div>
                    <div class="step-body">
                        <p><?php echo htmlspecialchars(trim($step)); ?></p>
                    </div>
                </li>
                <?php endforeach; ?>
            </ol>
        </div>
        <?php endif; ?>

    </div>

    <!-- RIGHT: Sidebar -->
    <div class="detail-sidebar">

        <!-- Quick Info -->
        <div class="sidebar-card">
            <h3>⚡ Quick Info</h3>
            <ul class="quick-info">
                <?php if($recipe['prep_time']): ?>
                <li><span>Prep Time</span><strong><?php echo htmlspecialchars($recipe['prep_time']); ?></strong></li>
                <?php endif; ?>
                <?php if($recipe['cook_time']): ?>
                <li><span>Cook Time</span><strong><?php echo htmlspecialchars($recipe['cook_time']); ?></strong></li>
                <?php endif; ?>
                <?php if($recipe['serves']): ?>
                <li><span>Serves</span><strong><?php echo htmlspecialchars($recipe['serves']); ?></strong></li>
                <?php endif; ?>
                <?php if($recipe['difficulty']): ?>
                <li><span>Difficulty</span>
                    <strong class="diff-badge <?php echo $diffClass; ?>"><?php echo htmlspecialchars($recipe['difficulty']); ?></strong>
                </li>
                <?php endif; ?>
                <?php if($recipe['cuisine']): ?>
                <li><span>Cuisine</span><strong><?php echo htmlspecialchars($recipe['cuisine']); ?></strong></li>
                <?php endif; ?>
                <?php if($recipe['dietary']): ?>
                <li><span>Dietary</span><strong><?php echo htmlspecialchars($recipe['dietary']); ?></strong></li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Tips -->
        <?php if(!empty($tips)): ?>
        <div class="sidebar-card tips-card">
            <h3>💡 Chef's Tips</h3>
            <ul class="tips-list">
                <?php foreach($tips as $tip): ?>
                <li><?php echo htmlspecialchars(trim($tip)); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <!-- Nutrition -->
        <?php if($recipe['calories'] || $recipe['protein'] || $recipe['carbs'] || $recipe['fat']): ?>
        <div class="sidebar-card nutrition-card">
            <h3>🥗 Nutrition <span class="per-serving">per serving</span></h3>
            <div class="nutrition-grid">
                <?php if($recipe['calories']): ?>
                <div class="nutrient"><strong><?php echo htmlspecialchars($recipe['calories']); ?></strong><span>Calories</span></div>
                <?php endif; ?>
                <?php if($recipe['protein']): ?>
                <div class="nutrient"><strong><?php echo htmlspecialchars($recipe['protein']); ?></strong><span>Protein</span></div>
                <?php endif; ?>
                <?php if($recipe['carbs']): ?>
                <div class="nutrient"><strong><?php echo htmlspecialchars($recipe['carbs']); ?></strong><span>Carbs</span></div>
                <?php endif; ?>
                <?php if($recipe['fat']): ?>
                <div class="nutrient"><strong><?php echo htmlspecialchars($recipe['fat']); ?></strong><span>Fat</span></div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
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

<script>
function toggleLike(btn) {
    const recipeId = btn.dataset.recipeId;
    if (!recipeId) return;
    const data = new FormData();
    data.append('recipe_id', recipeId);
    fetch('toggle_like.php', { method: 'POST', body: data })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                btn.textContent = '❤️ ' + res.likes + ' Likes';
                btn.classList.toggle('liked', res.liked);
            } else {
                alert(res.message);
            }
        });
}

function toggleSave(btn) {
    const recipeId = btn.dataset.recipeId;
    if (!recipeId) return;
    const data = new FormData();
    data.append('recipe_id', recipeId);
    fetch('toggle_save.php', { method: 'POST', body: data })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                btn.textContent = res.saved ? '⭐ Saved!' : '⭐ Save Recipe';
                btn.style.background = res.saved ? 'white' : 'rgba(255,255,255,0.15)';
                btn.style.color = res.saved ? '#ff6f3c' : '';
            } else {
                alert(res.message);
            }
        });
}

function downloadRecipe() {
    const btn = document.getElementById('downloadBtn');
    btn.textContent = '⏳ Preparing...';
    btn.disabled = true;
    setTimeout(() => {
        window.print();
        btn.textContent = '⬇️ Download PDF';
        btn.disabled = false;
    }, 150);
}
</script>


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