<?php
session_start();
require 'db.php';

// Fetch all recipes (both collection and community) for the downloadable cards
$allRecipes = [];
$rResult = $conn->query("SELECT recipe_id, title, description, category, cuisine, difficulty, dietary, cook_time, prep_time, serves, emoji, author_name, source FROM recipes ORDER BY source ASC, created_at DESC");
if ($rResult) {
    while ($row = $rResult->fetch_assoc()) $allRecipes[] = $row;
}

// Fetch community-submitted culinary tips for the Techniques tab
$allTips = [];
$tResult = $conn->query("
    CREATE TABLE IF NOT EXISTS culinary_tips (
        tip_id      INT AUTO_INCREMENT PRIMARY KEY,
        user_id     INT DEFAULT NULL,
        author_name VARCHAR(100) DEFAULT 'Anonymous',
        tip_type    ENUM('tip','experience','recipe_hack','technique','other') DEFAULT 'tip',
        title       VARCHAR(150) NOT NULL,
        content     TEXT NOT NULL,
        cuisine     VARCHAR(50) DEFAULT NULL,
        created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");
$tResult = $conn->query("SELECT * FROM culinary_tips ORDER BY created_at DESC");
if ($tResult) {
    while ($row = $tResult->fetch_assoc()) $allTips[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FoodFusion | Culinary Resources</title>
<link rel="stylesheet" href="Home1.css">
<link rel="stylesheet" href="culinary_resources1.css">
</head>
<body>

<header>
    <div class="logo">🍽 FoodFusion</div>
        <nav>
        <a href="Home.php">Home</a>
        <a href="about.php">About</a>
        <a href="Recipe.php">Recipes</a>
        <a href="COOKBOOK.php">CookBook</a>
        <a href="culinary_resources.php" class="active">Resources</a>
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

<section class="cr-hero">
    <div class="cr-hero-inner">
        <span class="cr-eyebrow">📚 Culinary Resources</span>
        <h1>Level Up Your<br><span>Kitchen Skills</span></h1>
        <p>Download recipe cards, watch expert tutorials, and master techniques that transform everyday cooking into culinary artistry.</p>
        <div class="cr-hero-pills">
            <span>🃏 Recipe Cards</span>
            <span>🎬 Video Tutorials</span>
            <span>🔪 Techniques</span>
            <span>🧁 Baking Science</span>
        </div>
    </div>
    <div class="cr-hero-graphic">
        <div class="cr-float-card">📄 <?php echo count($allRecipes); ?>+ Recipes</div>
        <div class="cr-float-card delay1">🎬 30+ Videos</div>
        <div class="cr-float-card delay2">🔥 20+ Techniques</div>
    </div>
</section>

<nav class="cr-tabs">
    <button class="cr-tab active" onclick="showTab('cards', this)">🃏 Recipe Cards</button>
    <button class="cr-tab" onclick="showTab('videos', this)">🎬 Video Tutorials</button>
    <button class="cr-tab" onclick="showTab('techniques', this)">🔪 Techniques & Hacks</button>
</nav>

<!-- RECIPE CARDS TAB -->
<section class="cr-section" id="tab-cards">
    <div class="cr-section-header">
        <h2>Downloadable Recipe Cards</h2>
        <p>Print-ready cards for your kitchen collection. Each card includes ingredients, steps, and chef tips.</p>
    </div>
    <?php
    // Collect unique categories for filter buttons
    $catMap = [
        'pasta'   => ['label'=>'🍝 Pasta',   'key'=>'pasta'],
        'curry'   => ['label'=>'🍛 Curry',   'key'=>'curry'],
        'dessert' => ['label'=>'🍰 Desserts','key'=>'dessert'],
        'healthy' => ['label'=>'🥗 Healthy', 'key'=>'healthy'],
        'general' => ['label'=>'🍽 General', 'key'=>'general'],
    ];
    $usedCats = array_unique(array_column($allRecipes, 'category'));
    ?>
    <div class="cr-filter-bar">
        <button class="cr-filter active" onclick="filterCards('all', this)">All</button>
        <?php foreach($catMap as $key => $info):
            if(in_array($key, $usedCats)): ?>
        <button class="cr-filter" onclick="filterCards('<?php echo $key; ?>', this)"><?php echo $info['label']; ?></button>
        <?php endif; endforeach; ?>
    </div>

    <?php if (empty($allRecipes)): ?>
    <p style="color:#aaa;text-align:center;padding:40px 0;grid-column:1/-1;">
        No recipes found. Add recipes via the Cookbook or run the insert SQL in phpMyAdmin.
    </p>
    <?php endif; ?>

    <div class="cr-cards-grid" id="recipeCards">
    <?php
    $emojiMap = ['pasta'=>'🍝','curry'=>'🍛','dessert'=>'🍰','healthy'=>'🥗','general'=>'🍽','noodles'=>'🍜'];
    foreach($allRecipes as $recipe):
        $emoji = $recipe['emoji'] ?? ($emojiMap[$recipe['category']] ?? '🍽');
        $cat   = $recipe['category'] ?? 'general';
        $desc  = htmlspecialchars(mb_strimwidth($recipe['description'] ?? '', 0, 100, '…'));
        $time  = $recipe['cook_time'] ? htmlspecialchars($recipe['cook_time']) : '—';
        $serv  = $recipe['serves']    ? 'Serves '  . htmlspecialchars($recipe['serves']) : '';
        $diff  = $recipe['difficulty']? htmlspecialchars($recipe['difficulty']) : '';
        $badge = $recipe['source'] === 'community' ? '<span class="cr-community-badge">Community</span>' : '';
    ?>
    <div class="cr-download-card" data-cat="<?php echo htmlspecialchars($cat); ?>">
        <div class="cr-card-icon"><?php echo $emoji; ?></div>
        <div class="cr-card-info">
            <h3><?php echo htmlspecialchars($recipe['title']); ?> <?php echo $badge; ?></h3>
            <p><?php echo $desc; ?></p>
            <div class="cr-card-meta">
                <?php if($time !== '—'): ?><span>🕐 <?php echo $time; ?></span><?php endif; ?>
                <?php if($serv): ?><span>👥 <?php echo $serv; ?></span><?php endif; ?>
                <?php if($diff): ?><span>⭐ <?php echo $diff; ?></span><?php endif; ?>
            </div>
        </div>
        <a href="download_recipe.php?id=<?php echo (int)$recipe['recipe_id']; ?>" class="cr-dl-btn" download>⬇ Download</a>
    </div>
    <?php endforeach; ?>
    </div>
    <div class="cr-bundle-banner">
        <div>
            <h3>📦 Download the Full Recipe Card Bundle</h3>
            <p>All <?php echo count($allRecipes); ?> recipe cards in one beautifully formatted collection — including both our curated picks and community favourites.</p>
        </div>
        <a href="download_all_recipes.php" class="cr-bundle-btn">⬇ Download All (ZIP)</a>
    </div>
</section>

<!-- VIDEOS TAB -->
<section class="cr-section hidden" id="tab-videos">
    <div class="cr-section-header">
        <h2>Downloadable Cooking Video Tutorials</h2>
        <p>Download expert cooking videos to watch offline anytime — perfect for the kitchen where Wi-Fi is unreliable.</p>
    </div>

    <!-- Video filter bar -->
    <div class="cr-filter-bar" style="margin-bottom:32px;">
        <button class="cr-filter active" onclick="filterVideos('all', this)">All</button>
        <button class="cr-filter" onclick="filterVideos('Italian', this)">🍝 Italian</button>
        <button class="cr-filter" onclick="filterVideos('Techniques', this)">🔪 Techniques</button>
        <button class="cr-filter" onclick="filterVideos('Baking', this)">🍞 Baking</button>
        <button class="cr-filter" onclick="filterVideos('Indian', this)">🍛 Indian</button>
        <button class="cr-filter" onclick="filterVideos('Asian', this)">🥢 Asian</button>
        <button class="cr-filter" onclick="filterVideos('Healthy', this)">🥗 Healthy</button>
    </div>

    <div class="cr-video-dl-grid" id="videoDlGrid">

        <?php
        // All videos stream via video_proxy.php → Wikimedia Commons (CC-licensed, real downloads)
        // 'direct' = fallback URL used if proxy fails (links straight to Wikimedia)
        $videos = [
            ['emoji'=>'🍳','color'=>'#ff6f3c','tag'=>'Techniques', 'level'=>'Beginner',    'size'=>'19 MB', 'fmt'=>'WebM','duration'=>'1:36', 'title'=>'Scrambled Eggs with Mushrooms',    'desc'=>'A quick, hands-on cooking demo showing how to make fluffy scrambled eggs with mushrooms and cheese on a home stove.',   'file'=>'video_proxy.php?id=scrambled_eggs','direct'=>'https://upload.wikimedia.org/wikipedia/commons/c/ce/Scrambled_eggs_with_mushrooms_and_cheese.webm'],
            ['emoji'=>'🐟','color'=>'#264653','tag'=>'Techniques', 'level'=>'Intermediate','size'=>'14 MB', 'fmt'=>'WebM','duration'=>'0:33', 'title'=>'Pan-Seared Salmon',                'desc'=>'Watch a piece of fresh salmon go from raw to perfectly seared in under a minute — great technique reference.',           'file'=>'video_proxy.php?id=salmon',        'direct'=>'https://upload.wikimedia.org/wikipedia/commons/2/2a/Salmon_20200410_113456~2.webm'],
            ['emoji'=>'🍞','color'=>'#a0522d','tag'=>'Baking',     'level'=>'Advanced',    'size'=>'21 MB', 'fmt'=>'WebM','duration'=>'0:30', 'title'=>'Sourdough Starter Rising',         'desc'=>'Time-lapse of a rye sourdough starter culture rising — see exactly what an active, healthy starter looks like.',         'file'=>'video_proxy.php?id=sourdough',     'direct'=>'https://upload.wikimedia.org/wikipedia/commons/b/b3/Rye_sourdough_starter_culture_rising.webm'],
            ['emoji'=>'🍽','color'=>'#3c7fff','tag'=>'Techniques', 'level'=>'Beginner',    'size'=>'19 MB', 'fmt'=>'WebM','duration'=>'0:13', 'title'=>'Cooking Lunch — Basics',          'desc'=>'A short real-kitchen clip demonstrating basic food preparation and stovetop cooking technique.',                          'file'=>'video_proxy.php?id=cooking_lunch', 'direct'=>'https://upload.wikimedia.org/wikipedia/commons/1/1d/Cooking_lunch.webm'],
            ['emoji'=>'🛡','color'=>'#2a9d8f','tag'=>'Healthy',    'level'=>'Beginner',    'size'=>'6 MB',  'fmt'=>'WebM','duration'=>'2:03', 'title'=>'Gear Up for Food Safety',         'desc'=>'CDC food safety video covering proper handling, temperature zones, and cross-contamination prevention.',                  'file'=>'video_proxy.php?id=food_safety',   'direct'=>'https://upload.wikimedia.org/wikipedia/commons/4/42/Gear_Up_for_Food_Safety.webm'],
            ['emoji'=>'🧼','color'=>'#457b9d','tag'=>'Healthy',    'level'=>'Beginner',    'size'=>'3 MB',  'fmt'=>'WebM','duration'=>'0:30', 'title'=>'Food Safety Basics',              'desc'=>'Quick visual guide to safe food handling practices — washing, storing, and cooking temperatures.',                         'file'=>'video_proxy.php?id=food_safety2',  'direct'=>'https://upload.wikimedia.org/wikipedia/commons/e/eb/Food_Safety_Video.webm'],
            ['emoji'=>'🍍','color'=>'#e9c46a','tag'=>'Techniques', 'level'=>'Beginner',    'size'=>'48 MB', 'fmt'=>'OGV', 'duration'=>'0:56', 'title'=>'Preparing a Pineapple',           'desc'=>'Step-by-step visual of how to cut, core, and prepare a fresh pineapple efficiently with minimal waste.',                  'file'=>'video_proxy.php?id=pineapple_prep','direct'=>'https://upload.wikimedia.org/wikipedia/commons/c/c4/Preparing_pineapple_-_01.ogv'],
            ['emoji'=>'👩‍🍳','color'=>'#e63946','tag'=>'Techniques','level'=>'Intermediate','size'=>'191 MB','fmt'=>'WebM','duration'=>'34:56','title'=>'Full Cooking Show Episode',        'desc'=>'A complete 35-minute cooking show pilot — watch a professional chef work through multiple dishes from prep to plate.',    'file'=>'video_proxy.php?id=cooking_show',  'direct'=>'https://upload.wikimedia.org/wikipedia/commons/b/bb/Rae_Dawn_Chong_-_Cooking_Show_Pilot%2C_2008.webm'],
            ['emoji'=>'🫙','color'=>'#6a4c93','tag'=>'Baking',     'level'=>'Intermediate','size'=>'21 MB', 'fmt'=>'WebM','duration'=>'0:30', 'title'=>'Fermentation in Action',          'desc'=>'Watch live fermentation culture activity — perfect companion to learning sourdough and fermented food techniques.',        'file'=>'video_proxy.php?id=fermentation',  'direct'=>'https://upload.wikimedia.org/wikipedia/commons/b/b3/Rye_sourdough_starter_culture_rising.webm'],
        ];
        $levelColors = ['Beginner'=>'#2a9d8f','Intermediate'=>'#e9c46a','Advanced'=>'#e63946'];
        foreach($videos as $v):
            $levelColor = $levelColors[$v['level']] ?? '#aaa';
        ?>
        <div class="cr-vdl-card" data-tag="<?php echo $v['tag']; ?>">
            <div class="cr-vdl-thumb" style="background: linear-gradient(135deg, <?php echo $v['color']; ?>22, <?php echo $v['color']; ?>44);">
                <span class="cr-vdl-emoji"><?php echo $v['emoji']; ?></span>
                <span class="cr-vdl-duration">⏱ <?php echo $v['duration']; ?></span>
            </div>
            <div class="cr-vdl-body">
                <div class="cr-vdl-tags">
                    <span class="cr-video-tag"><?php echo $v['tag']; ?></span>
                    <span class="cr-vdl-level" style="background:<?php echo $levelColor; ?>22;color:<?php echo $levelColor; ?>;"><?php echo $v['level']; ?></span>
                </div>
                <h3><?php echo $v['title']; ?></h3>
                <p><?php echo $v['desc']; ?></p>
                <div class="cr-vdl-meta">
                    <span>🎞 <?php echo $v['fmt']; ?></span>
                    <span>💾 <?php echo $v['size']; ?></span>
                </div>
                <a href="<?php echo $v['file']; ?>" class="cr-vdl-btn" download="<?php echo htmlspecialchars($v['title']); ?>">
                    <span class="cr-vdl-btn-icon">⬇</span> Download Video
                </a>
                <a href="<?php echo htmlspecialchars($v['direct']); ?>" class="cr-vdl-alt" target="_blank" rel="noopener">
                    Open in browser
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Attribution note for Wikimedia Commons videos -->
    <div class="cr-bundle-banner" style="margin-top:48px;">
        <div>
            <h3>📋 About These Videos</h3>
            <p>All videos are freely licensed from <strong>Wikimedia Commons</strong> (Creative Commons / Public Domain). Each download streams directly from Wikimedia's servers — no sign-up needed.</p>
        </div>
        <a href="https://commons.wikimedia.org/wiki/Category:Videos_of_food_preparation" target="_blank" rel="noopener" class="cr-bundle-btn">🌐 Browse More on Commons</a>
    </div>
</section>

<!-- TECHNIQUES TAB -->
<section class="cr-section hidden" id="tab-techniques">
    <div class="cr-section-header">
        <h2>Techniques & Kitchen Hacks</h2>
        <p>Quick-reference guides for the skills every home cook should know.</p>
    </div>
    <div class="cr-techniques-grid">
        <div class="cr-technique-card"><div class="cr-technique-icon">🔪</div><h3>The Rock Chop</h3><p>Keep the tip of the blade on the board and rock through vegetables for fast, even cuts. Curl fingers into a "claw" to protect them.</p><span class="cr-level easy">Easy</span></div>
        <div class="cr-technique-card"><div class="cr-technique-icon">🧄</div><h3>Smash-Peel Garlic</h3><p>Place flat of knife on clove and smash with heel of hand — skin slides right off. Saves 30 seconds every single time.</p><span class="cr-level easy">Hack</span></div>
        <div class="cr-technique-card"><div class="cr-technique-icon">🍳</div><h3>The Dry Sear</h3><p>Pat proteins completely dry before searing. Moisture is the enemy of browning. Hot pan + dry surface = perfect Maillard crust.</p><span class="cr-level medium">Intermediate</span></div>
        <div class="cr-technique-card"><div class="cr-technique-icon">🥚</div><h3>Perfect Soft-Boiled Eggs</h3><p>Boil exactly 6½ minutes from room temperature, then ice bath for 3 minutes. Jammy yolk every time without fail.</p><span class="cr-level easy">Easy</span></div>
        <div class="cr-technique-card"><div class="cr-technique-icon">🧅</div><h3>No-Tear Onion Cutting</h3><p>Chill onions 30 mins before cutting. Cut root last — it holds the onion together and releases the fewest irritants.</p><span class="cr-level easy">Hack</span></div>
        <div class="cr-technique-card"><div class="cr-technique-icon">🍋</div><h3>Microwave to Juice More</h3><p>Microwave citrus 15 seconds, then roll under palm before cutting. Get 30–50% more juice out of every fruit.</p><span class="cr-level easy">Hack</span></div>
        <div class="cr-technique-card"><div class="cr-technique-icon">🫕</div><h3>Braising Low & Slow</h3><p>Brown meat first for flavour, then cook partially submerged in liquid at 160°C for 2–4 hours. Collagen converts to gelatin.</p><span class="cr-level medium">Intermediate</span></div>
        <div class="cr-technique-card"><div class="cr-technique-icon">🧈</div><h3>Beurre Blanc Sauce</h3><p>Reduce white wine and shallots, then whisk cold butter in off the heat piece by piece. Creates a glossy French butter sauce.</p><span class="cr-level hard">Advanced</span></div>
        <div class="cr-technique-card"><div class="cr-technique-icon">🍞</div><h3>Windowpane Test</h3><p>Stretch bread dough thin — if translucent without tearing, gluten is properly developed. Most reliable indicator of readiness.</p><span class="cr-level medium">Intermediate</span></div>
    </div>
    <div class="cr-hack-download">
        <h3>📥 Download Our Kitchen Hacks Cheat Sheet</h3>
        <p>A printable A4 reference with 30 essential hacks, conversions, and substitution guides.</p>
        <a href="#" class="cr-bundle-btn" onclick="simulateDownload(event, this)">⬇ Download Cheat Sheet (PDF)</a>
    </div>

    <!-- COMMUNITY TIPS SECTION -->
    <div class="cr-section-header" style="margin-top:56px;">
        <h2>💬 Community Tips & Experiences</h2>
        <p>Culinary wisdom shared by fellow home cooks — tips, hacks, and kitchen stories from our community.</p>
    </div>

    <?php if (empty($allTips)): ?>
    <div class="cr-no-tips">
        <span>🌿</span>
        <p>No community tips yet. Share yours on the <a href="COOKBOOK.php">CookBook page</a>!</p>
    </div>
    <?php else: ?>
    <div class="cr-community-tips-grid" id="communityTipsGrid">
        <?php
        $typeEmoji = [
            'tip'         => '💡',
            'experience'  => '🌟',
            'recipe_hack' => '🔧',
            'technique'   => '🔪',
            'other'       => '💬',
        ];
        $typeLabel = [
            'tip'         => 'Culinary Tip',
            'experience'  => 'Experience',
            'recipe_hack' => 'Recipe Hack',
            'technique'   => 'Technique',
            'other'       => 'Share',
        ];
        foreach ($allTips as $tip):
            $emoji = $typeEmoji[$tip['tip_type']] ?? '💬';
            $label = $typeLabel[$tip['tip_type']] ?? 'Share';
            $date  = date('M j, Y', strtotime($tip['created_at']));
        ?>
        <div class="cr-tip-card">
            <div class="cr-tip-header">
                <span class="cr-tip-emoji"><?php echo $emoji; ?></span>
                <div>
                    <span class="cr-tip-type-badge"><?php echo htmlspecialchars($label); ?></span>
                    <?php if($tip['cuisine']): ?>
                    <span class="cr-tip-cuisine"><?php echo htmlspecialchars($tip['cuisine']); ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <h3 class="cr-tip-title"><?php echo htmlspecialchars($tip['title']); ?></h3>
            <p class="cr-tip-content"><?php echo nl2br(htmlspecialchars($tip['content'])); ?></p>
            <div class="cr-tip-footer">
                <span class="cr-tip-author">👨‍🍳 <?php echo htmlspecialchars($tip['author_name']); ?></span>
                <span class="cr-tip-date">📅 <?php echo $date; ?></span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="cr-tip-cta">
        <p>Have a tip or experience to share? Head over to the CookBook page!</p>
        <a href="COOKBOOK.php" class="cr-bundle-btn">✨ Share Your Tip</a>
    </div>
</section>

<footer>
    <div class="social">
        <a href="https://www.facebook.com/foodfusion" target="_blank" rel="noopener noreferrer">Facebook</a>
        <a href="https://www.instagram.com/foodfusion" target="_blank" rel="noopener noreferrer">Instagram</a>
        <a href="https://www.youtube.com/foodfusion" target="_blank" rel="noopener noreferrer">YouTube</a>
    </div>
    <p><a href="culinary_resources.php">Culinary Resources</a> | <a href="educational_resources.php">Educational Resources</a> | <a href="privacy_policy.php">Privacy Policy</a> | &copy; 2026 FoodFusion</p>
</footer>

<script>
function showTab(tab, btn) {
    document.querySelectorAll('.cr-section').forEach(s => s.classList.add('hidden'));
    document.querySelectorAll('.cr-tab').forEach(t => t.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.remove('hidden');
    btn.classList.add('active');
}
function filterCards(cat, btn) {
    document.querySelectorAll('.cr-filter').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.cr-download-card').forEach(card => {
        card.style.display = (cat === 'all' || card.dataset.cat === cat) ? 'flex' : 'none';
    });
}
function simulateDownload(e, el) {
    e.preventDefault();
    const orig = el.textContent;
    el.textContent = '⏳ Preparing...';
    el.style.opacity = '0.7';
    setTimeout(() => {
        el.textContent = '✅ Ready!';
        setTimeout(() => { el.textContent = orig; el.style.opacity = '1'; }, 1500);
    }, 1200);
}
function filterVideos(tag, btn) {
    document.querySelectorAll('#tab-videos .cr-filter').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.cr-vdl-card').forEach(card => {
        card.style.display = (tag === 'all' || card.dataset.tag === tag) ? 'flex' : 'none';
    });
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