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

// Fetch community recipes from DB
$sql    = "SELECT * FROM recipes WHERE source='community' ORDER BY created_at DESC";
$result = $conn->query($sql);
$dbRecipes = [];
if ($result) {
    while ($row = $result->fetch_assoc()) $dbRecipes[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FoodFusion | Community Cookbook</title>
<link rel="stylesheet" href="Home1.css">
<link rel="stylesheet" href="cookbook.css">
</head>
<body data-user-preference="<?php echo htmlspecialchars($userPreference); ?>">

<header>
    <div class="logo">🍽 FoodFusion</div>
        <nav>
        <a href="Home.php">Home</a>
        <a href="about.php">About</a>
        <a href="Recipe.php">Recipes</a>
        <a href="COOKBOOK.php" class="active">CookBook</a>
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

<!-- HERO -->
<section class="cookbook-hero">
    <div class="hero-inner">
        <span class="hero-eyebrow">✨ Community Cookbook</span>
        <h1>From One Kitchen<br>to a Thousand Plates<br><span>& Millions of Hearts</span></h1>
        <p>Every great dish starts with a personal touch. Share your secret recipes, cherished family traditions, and unique twists — and let the world taste your story.</p>
        <div class="hero-btns">
            <a href="#share" class="btn-primary" onclick="smoothScrollTo('recipes-section')">📖 Share Your Recipe</a>
            <a href="#recipes-section" class="btn-ghost" onclick="smoothScrollTo('recipes-section')">🍽 Browse Recipes</a>
        </div>
    </div>
    <div class="floating-emojis">
        <span style="top:15%;left:8%;animation-delay:0s;">🍝</span>
        <span style="top:60%;left:3%;animation-delay:0.5s;">🍕</span>
        <span style="top:30%;right:6%;animation-delay:1s;">🍜</span>
        <span style="top:70%;right:8%;animation-delay:1.5s;">🥘</span>
        <span style="top:80%;left:15%;animation-delay:2s;">🧁</span>
        <span style="top:10%;right:20%;animation-delay:2.5s;">🌮</span>
    </div>
</section>


<!-- CULINARY TIPS & EXPERIENCES FORM -->
<section class="inspiration">
    <h2>What Will You Share Today?</h2>
    <p class="inspo-subtitle">Share your culinary tips, kitchen experiences, recipe hacks, or cooking techniques with our community.</p>

    <div class="tip-form-container">
        <?php if(!isset($_SESSION['user'])): ?>
        <div class="tip-login-notice">
            🔒 Please <a href="Login.php">log in</a> to share your culinary wisdom. Your tip will be credited to your name.
        </div>
        <?php endif; ?>

        <div id="tipSuccess" style="display:none;" class="tip-success-msg">
            ✅ Your culinary tip has been shared with the community!
        </div>

        <form id="tipForm" class="tip-form">
            <div class="tip-form-row">
                <div class="tip-form-group">
                    <label>Your Name</label>
                    <input type="text" id="tipAuthor" placeholder="Chef name (or leave blank)">
                </div>
                <div class="tip-form-group">
                    <label>Type of Share *</label>
                    <select id="tipType">
                        <option value="tip">💡 Culinary Tip</option>
                        <option value="experience">🌟 Personal Experience</option>
                        <option value="recipe_hack">🔧 Recipe Hack</option>
                        <option value="technique">🔪 Cooking Technique</option>
                        <option value="other">💬 Other</option>
                    </select>
                </div>
            </div>

            <div class="tip-form-row">
                <div class="tip-form-group">
                    <label>Cuisine (optional)</label>
                    <input type="text" id="tipCuisine" placeholder="e.g. Italian, Indian, Mexican">
                </div>
            </div>

            <div class="tip-form-group tip-full-width">
                <label>Title *</label>
                <input type="text" id="tipTitle" placeholder="e.g. The secret to perfect caramelised onions" required>
            </div>

            <div class="tip-form-group tip-full-width">
                <label>Share your tip or experience *</label>
                <textarea id="tipContent" rows="5" placeholder="Tell the community what you've learned, discovered, or want to share about cooking..." required></textarea>
            </div>

            <button type="submit" class="tip-submit-btn" id="tipSubmitBtn">✨ Share with Community</button>
        </form>
    </div>

    <!-- LIVE TIPS FEED -->
    <div class="cb-tips-feed-wrap">
        <h3 class="cb-tips-feed-heading">🌟 Recently Shared Tips</h3>
        <div id="cbTipsFeed" class="cb-tips-feed">
            <?php
            // Load existing tips from DB (newest first, max 12)
            $conn->query("CREATE TABLE IF NOT EXISTS culinary_tips (
                tip_id      INT AUTO_INCREMENT PRIMARY KEY,
                user_id     INT DEFAULT NULL,
                author_name VARCHAR(100) DEFAULT 'Anonymous',
                tip_type    ENUM('tip','experience','recipe_hack','technique','other') DEFAULT 'tip',
                title       VARCHAR(150) NOT NULL,
                content     TEXT NOT NULL,
                cuisine     VARCHAR(50) DEFAULT NULL,
                created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            $tipRows = [];
            $tRes = $conn->query("SELECT * FROM culinary_tips ORDER BY created_at DESC LIMIT 12");
            if ($tRes) { while ($tr = $tRes->fetch_assoc()) $tipRows[] = $tr; }

            $typeEmoji = ['tip'=>'💡','experience'=>'🌟','recipe_hack'=>'🔧','technique'=>'🔪','other'=>'💬'];
            $typeLabel = ['tip'=>'Culinary Tip','experience'=>'Experience','recipe_hack'=>'Recipe Hack','technique'=>'Technique','other'=>'Share'];

            if (empty($tipRows)): ?>
            <div class="cb-tips-empty" id="cbTipsEmpty">
                <span>🌿</span>
                <p>No tips shared yet — be the first!</p>
            </div>
            <?php else:
                foreach ($tipRows as $tr):
                    $em  = $typeEmoji[$tr['tip_type']] ?? '💬';
                    $lbl = $typeLabel[$tr['tip_type']] ?? 'Share';
                    $ago = date('M j, Y', strtotime($tr['created_at']));
            ?>
            <div class="cb-tip-card">
                <div class="cb-tip-card-top">
                    <span class="cb-tip-card-emoji"><?php echo $em; ?></span>
                    <div class="cb-tip-card-badges">
                        <span class="cb-tip-badge-type"><?php echo htmlspecialchars($lbl); ?></span>
                        <?php if ($tr['cuisine']): ?>
                        <span class="cb-tip-badge-cuisine"><?php echo htmlspecialchars($tr['cuisine']); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <h4 class="cb-tip-card-title"><?php echo htmlspecialchars($tr['title']); ?></h4>
                <p class="cb-tip-card-content"><?php echo nl2br(htmlspecialchars($tr['content'])); ?></p>
                <div class="cb-tip-card-footer">
                    <span>👨‍🍳 <?php echo htmlspecialchars($tr['author_name']); ?></span>
                    <span>📅 <?php echo $ago; ?></span>
                </div>
            </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
</section>


<!-- RECIPES SECTION -->
<div id="recipes-section" class="recipes-scroll-anchor"></div>
<section class="recipes-full-section" id="recipes">
    <div class="section-top">
        <h2 class="section-heading">🍴 Recipes from Our Community</h2>
    </div>

    <div class="cookbook-filters">
        <input type="text" id="cbSearch" placeholder="Search recipe..." oninput="applyFilters()">
        <select id="cbCategory" onchange="applyFilters()">
            <option value="all">All Categories</option>
            <option value="pasta">🍝 Pasta</option>
            <option value="curry">🍛 Curry</option>
            <option value="dessert">🍰 Dessert</option>
            <option value="healthy">🥗 Healthy</option>
            <option value="general">🍽 General</option>
        </select>
        <select id="cbSort" onchange="applyFilters()">
            <option value="default">Sort By</option>
            <option value="most-liked">Most Liked</option>
            <option value="newest">Newest First</option>
        </select>
    </div>

    <div id="communityRecipes" class="recipe-grid">
    <?php if (empty($dbRecipes)): ?>
        <p style="color:#aaa; padding:30px 0; grid-column:1/-1; text-align:center;">
            No community recipes yet. Be the first to share one! 👇
        </p>
    <?php else: ?>
        <?php foreach($dbRecipes as $r):
            $emojis = ['pasta'=>'🍝','curry'=>'🍛','dessert'=>'🍰','healthy'=>'🥗','general'=>'🍽','noodles'=>'🍜'];
            $emoji  = $emojis[$r['category']] ?? '🍽';

            $imgSrc = !empty($r['image_path'])
                ? $r['image_path']
                : 'imgs_recipe/' . $r['title'] . '.jpeg';
        ?>
        <div class="recipe-card"
             data-id="<?php echo $r['recipe_id']; ?>"
             data-category="<?php echo htmlspecialchars($r['category']); ?>"
             data-dietary="<?php echo htmlspecialchars($r['dietary'] ?? ''); ?>"
             data-likes="<?php echo (int)$r['likes']; ?>"
             data-time="<?php echo htmlspecialchars($r['cook_time'] ?? ''); ?>">
            <div class="card-banner<?php echo $imgSrc ? ' card-banner--photo' : ''; ?>">
                <?php if ($imgSrc): ?>
                <img src="<?php echo htmlspecialchars($imgSrc); ?>" alt="<?php echo htmlspecialchars($r['title']); ?>" class="card-banner-img">
                <?php else: ?>
                <?php echo $emoji; ?>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div class="card-category"><?php echo htmlspecialchars($r['category']); ?></div>
                <h3><?php echo htmlspecialchars($r['title']); ?></h3>
                <p class="card-description"><?php echo htmlspecialchars($r['description']); ?></p>
                <div class="card-footer">
                    <span class="card-author">👨‍🍳 <?php echo htmlspecialchars($r['author_name']); ?>
                        <?php if($r['cook_time']): ?> · ⏱ <?php echo htmlspecialchars($r['cook_time']); ?><?php endif; ?>
                    </span>
                    <button class="like-btn" data-recipe-id="<?php echo $r['recipe_id']; ?>" onclick="event.stopPropagation(); toggleLike(this)">
                        ❤️ <?php echo (int)$r['likes']; ?>
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
    </div>
</section>

<!-- SUBMIT FORM SECTION -->
<section class="submit-full-section" id="share">
    <div class="submit-section">
        <div class="form-header">
            <span class="form-icon">👨‍🍳</span>
            <h2>Share Your Recipe</h2>
            <p>Your kitchen story deserves to be told</p>
        </div>

        <?php if(!isset($_SESSION['user'])): ?>
        <div style="background:#fff3ec;border:2px solid #ffb38a;border-radius:12px;padding:16px;margin:20px 25px 0;text-align:center;font-size:14px;color:#cc5500;">
            🔒 Please <a href="Login.php" style="color:#ff6f3c;font-weight:700;">log in</a> to publish recipes. Your submission will be saved to your profile.
        </div>
        <?php endif; ?>

        <div id="submitSuccess" style="display:none;background:#d4edda;border-radius:12px;padding:16px;margin:20px 25px 0;text-align:center;color:#155724;font-weight:600;">
            ✅ Recipe published! It now appears in the community feed.
        </div>

        <form id="recipeForm" enctype="multipart/form-data">
        <div class="form-body">

            <!-- ── Basic Info ── -->
            <div class="form-section-title">📋 Basic Info</div>

            <div class="form-row">
                <div class="form-group">
                    <label>Recipe Title *</label>
                    <input type="text" id="title" placeholder="e.g. Grandma's Secret Lasagna" required>
                </div>
                <div class="form-group">
                    <label>Your Name</label>
                    <input type="text" id="author" placeholder="Chef name (or leave blank)">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Category</label>
                    <select id="category">
                        <option value="general">🍽 General</option>
                        <option value="pasta">🍝 Pasta</option>
                        <option value="curry">🍛 Curry</option>
                        <option value="dessert">🍰 Dessert</option>
                        <option value="healthy">🥗 Healthy</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Cuisine</label>
                    <input type="text" id="cuisine" placeholder="e.g. Italian, Indian, Mexican">
                </div>
            </div>

            <div class="form-group full-width">
                <label>Description *</label>
                <textarea id="description" placeholder="Tell us about this dish — what makes it special?" required></textarea>
            </div>

            <div class="form-group full-width">
                <label>Story / Personal Note</label>
                <textarea id="story" placeholder="Share the story behind this recipe (optional)..."></textarea>
            </div>

            <!-- ── Time & Servings ── -->
            <div class="form-section-title">⏱ Time & Servings</div>

            <div class="form-row form-row-4">
                <div class="form-group">
                    <label>Prep Time</label>
                    <input type="text" id="preptime" placeholder="e.g. 15 mins">
                </div>
                <div class="form-group">
                    <label>Cook Time</label>
                    <input type="text" id="cooktime" placeholder="e.g. 30 mins">
                </div>
                <div class="form-group">
                    <label>Serves</label>
                    <input type="text" id="serves" placeholder="e.g. 4">
                </div>
                <div class="form-group">
                    <label>Difficulty</label>
                    <select id="difficulty">
                        <option value="">Select...</option>
                        <option value="Easy">Easy</option>
                        <option value="Medium">Medium</option>
                        <option value="Hard">Hard</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Dietary</label>
                    <select id="dietary">
                        <option value="">Select...</option>
                        <option value="Vegetarian">Vegetarian</option>
                        <option value="Non-Vegetarian">Non-Vegetarian</option>
                        <option value="Vegan">Vegan</option>
                    </select>
                </div>
            </div>

            <!-- ── Ingredients & Instructions ── -->
            <div class="form-section-title">🥗 Ingredients & Instructions</div>

            <div class="form-group full-width">
                <label>Ingredients * <span class="field-hint">(one per line)</span></label>
                <textarea id="ingredients" placeholder="Spaghetti 400g&#10;Egg yolks 4&#10;Pecorino Romano 80g&#10;Black pepper to taste" rows="6" required></textarea>
            </div>

            <div class="form-group full-width">
                <label>Instructions * <span class="field-hint">(one step per line)</span></label>
                <textarea id="instructions" placeholder="Boil spaghetti in heavily salted water until al dente.&#10;Fry guanciale in a dry pan until crispy. Remove from heat.&#10;Whisk egg yolks with grated Pecorino and plenty of black pepper." rows="6" required></textarea>
            </div>

            <div class="form-group full-width">
                <label>Chef's Tips <span class="field-hint">(one tip per line, optional)</span></label>
                <textarea id="chef_tips" placeholder="Take the pan off heat before adding eggs.&#10;Add pasta water gradually — you may not need it all." rows="4"></textarea>
            </div>

            <!-- ── Nutrition (Optional) ── -->
            <div class="form-section-title">🥦 Nutrition Info <span class="optional-label">(optional, per serving)</span></div>

            <div class="form-row form-row-4">
                <div class="form-group">
                    <label>Calories</label>
                    <input type="text" id="calories" placeholder="e.g. 520">
                </div>
                <div class="form-group">
                    <label>Protein</label>
                    <input type="text" id="protein" placeholder="e.g. 28g">
                </div>
                <div class="form-group">
                    <label>Carbs</label>
                    <input type="text" id="carbs" placeholder="e.g. 68g">
                </div>
                <div class="form-group">
                    <label>Fat</label>
                    <input type="text" id="fat" placeholder="e.g. 14g">
                </div>
            </div><!-- /.form-row nutrition -->

            <!-- ── Recipe Photo ── -->
            <div class="form-section-title">📸 Recipe Photo <span class="optional-label">(optional — one photo only)</span></div>

            <div class="form-group full-width">
                <label style="font-size:13px;font-weight:700;color:#555;letter-spacing:0.5px;text-transform:uppercase;display:block;margin-bottom:10px;">Upload a Photo of Your Dish</label>

                <!-- Drop zone -->
                <div id="photoUploadArea" onclick="document.getElementById('recipeImage').click()"
                    style="border:2.5px dashed #ffb38a;border-radius:18px;background:#fffaf7;cursor:pointer;
                           min-height:200px;display:flex;align-items:center;justify-content:center;
                           flex-direction:column;position:relative;overflow:hidden;transition:border-color .2s,background .2s;">

                    <!-- Placeholder (shown when no image) -->
                    <div id="photoPlaceholder" style="display:flex;flex-direction:column;align-items:center;
                                                      justify-content:center;gap:12px;padding:40px 24px;text-align:center;pointer-events:none;">
                        <div style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#ff6f3c,#ffb347);
                                    display:flex;align-items:center;justify-content:center;font-size:30px;box-shadow:0 6px 20px rgba(255,111,60,.25);">
                            📷
                        </div>
                        <div>
                            <p style="margin:0 0 4px;font-size:15px;font-weight:700;color:#cc5500;">Click to upload a photo</p>
                            <span style="font-size:12.5px;color:#c0967a;">JPG, PNG or WEBP &nbsp;·&nbsp; Max 5 MB</span>
                        </div>
                        <div style="display:flex;gap:8px;flex-wrap:wrap;justify-content:center;">
                            <span style="background:#fff1e8;color:#d94f00;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;">JPG</span>
                            <span style="background:#fff1e8;color:#d94f00;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;">PNG</span>
                            <span style="background:#fff1e8;color:#d94f00;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;">WEBP</span>
                        </div>
                    </div>

                    <!-- Preview image (hidden until photo chosen) -->
                    <img id="photoPreview" alt="Recipe preview"
                        style="display:none;width:100%;max-height:340px;object-fit:cover;border-radius:15px;">

                    <!-- Hover overlay hint -->
                    <div id="photoHoverHint" style="display:none;position:absolute;inset:0;background:rgba(255,111,60,.55);
                                                     border-radius:15px;align-items:center;justify-content:center;
                                                     color:#fff;font-size:15px;font-weight:700;pointer-events:none;">
                        🔄 Click to change photo
                    </div>
                </div>

                <!-- Hidden file input -->
                <input type="file" id="recipeImage" name="recipe_image"
                       accept="image/jpeg,image/png,image/webp" style="display:none;">

                <!-- Remove button — only visible after upload -->
                <div id="photoMeta" style="display:none;margin-top:12px;display:none;align-items:center;justify-content:space-between;
                                           background:#fff5ef;border:1.5px solid #ffe0cc;border-radius:12px;padding:10px 16px;">
                    <span id="photoFilename" style="font-size:13px;color:#7a4020;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:70%;"></span>
                    <button type="button" onclick="removePhoto()"
                            style="background:none;border:1.5px solid #ffb38a;border-radius:20px;padding:5px 16px;
                                   font-size:12.5px;color:#cc5500;font-weight:700;cursor:pointer;white-space:nowrap;flex-shrink:0;">
                        ✕ Remove
                    </button>
                </div>
            </div>

            <button type="submit" class="submit-btn" id="submitBtn">🚀 Publish My Recipe</button>
        </div><!-- /.form-body -->
        </form>
    </div>
</section>

<section class="quote-banner">
    <blockquote>"Cooking is not just about food — it's about passing down love, culture, and memories one plate at a time."</blockquote>
    <cite>— The FoodFusion Community</cite>
</section>

<footer>
    <div class="social">
        <a href="https://www.facebook.com/foodfusion" target="_blank" rel="noopener noreferrer">Facebook</a>
        <a href="https://www.instagram.com/foodfusion" target="_blank" rel="noopener noreferrer">Instagram</a>
        <a href="https://www.youtube.com/foodfusion" target="_blank" rel="noopener noreferrer">YouTube</a>
    </div>
    <p><a href="culinary_resources.php">Culinary Resources</a> | <a href="educational_resources.php">Educational Resources</a> | <a href="privacy_policy.php">Privacy Policy</a> | &copy; 2026 FoodFusion</p>
</footer>

<script src="cookbook1.js"></script>

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