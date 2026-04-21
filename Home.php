<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FoodFusion | Home</title>
<link rel="stylesheet" href="Home1.css">
<script>const isLoggedIn = <?php echo isset($_SESSION['user']) ? 'true' : 'false'; ?>;</script>
<script defer src="Home.js"></script>
</head>
<body>

<!-- NAVBAR (IDENTICAL) -->
<header>
    <div class="logo">🍽 FoodFusion</div>
        <nav>
        <a href="Home.php" class="active">Home</a>
        <a href="about.php">About</a>
        <a href="Recipe.php">Recipes</a>
        <a href="COOKBOOK.php">CookBook</a>
        <a href="culinary_resources.php">Resources</a>
        <a href="educational_resources.php">Learn</a>
        <a href="contact.php">Contact</a>
        <?php if(isset($_SESSION['user'])){ ?>
        <button class="nav-btn" onclick="window.location.href='profile.php'">Profile</button>
        <?php } else { ?>
        <button class="nav-btn" onclick="openPopup()">Join Us</button>
        <?php } ?>
    </nav>
        <button class="hamburger" id="hamburger" onclick="toggleNav()" aria-label="Toggle navigation">
            <span></span><span></span><span></span>
        </button>
</header>

<!-- HERO SECTION -->
<section class="hero">
    <div class="hero-text">
        <h1>Welcome To FoodFusion</h1>
        <p>Your home for recipes, culinary tips &amp; a passionate food community 🍴</p>

        <?php if(isset($_SESSION['user'])){ ?>
        <button onclick="window.location.href='profile.php'">My Profile</button>
        <?php } else { ?>
        <button onclick="openPopup()">Join Us — It's Free</button>
        <?php } ?>

        <div class="hero-stats">
            <div class="hero-stat"><span class="hero-stat-num">10K+</span><span class="hero-stat-label">Members</span></div>
            <div class="hero-stat-divider"></div>
            <div class="hero-stat"><span class="hero-stat-num">5K+</span><span class="hero-stat-label">Recipes</span></div>
            <div class="hero-stat-divider"></div>
            <div class="hero-stat"><span class="hero-stat-num">120+</span><span class="hero-stat-label">Cuisines</span></div>
        </div>
    </div>
</section>

<!-- FEATURES STRIP -->
<section class="features">
    <div class="feature-card">
        <div class="feature-icon">🍜</div>
        <h3>Explore Recipes</h3>
        <p>Browse hundreds of curated dishes from over 120 world cuisines, filtered by diet &amp; difficulty.</p>
        <a href="Recipe.php" class="feature-link">Browse Recipes &rarr;</a>
    </div>
    <div class="feature-card">
        <div class="feature-icon">&#128104;&#8205;&#127859;</div>
        <h3>Join the Community</h3>
        <p>Share your own recipes, cooking tips and culinary stories with thousands of fellow food lovers.</p>
        <a href="COOKBOOK.php" class="feature-link">Open Cookbook &rarr;</a>
    </div>
    <div class="feature-card">
        <div class="feature-icon">&#128218;</div>
        <h3>Learn &amp; Grow</h3>
        <p>Download recipe cards, watch tutorials and access guides on techniques and kitchen hacks.</p>
        <a href="culinary_resources.php" class="feature-link">Go to Resources &rarr;</a>
    </div>
</section>

<!-- NEWS FEED -->
<section class="news">
    <h2>&#128293; Featured Recipes &amp; Trends</h2>
    <p class="news-sub">Fresh from our collection &mdash; try something new today</p>
    <div class="news-container">
<?php
include "db.php";
$news_sql = "SELECT recipe_id, title, description, cuisine, dietary, difficulty, image_path FROM recipes WHERE source='collection' ORDER BY created_at DESC LIMIT 3";
$news_res = $conn->query($news_sql);
if ($news_res && $news_res->num_rows > 0):
    while($nr = $news_res->fetch_assoc()):
        $img = !empty($nr['image_path']) ? htmlspecialchars($nr['image_path']) : 'imgs/Home.png';
?>
        <div class="news-card">
            <div class="news-card-img" style="background-image:url('<?php echo $img; ?>')"></div>
            <div class="news-card-body">
                <div class="news-card-tags">
                    <span class="news-tag news-tag-cuisine"><?php echo htmlspecialchars($nr['cuisine']); ?></span>
                    <span class="news-tag news-tag-diff"><?php echo htmlspecialchars($nr['difficulty']); ?></span>
                </div>
                <h3><?php echo htmlspecialchars($nr['title']); ?></h3>
                <p><?php echo htmlspecialchars(mb_substr($nr['description'], 0, 90)) . (mb_strlen($nr['description']) > 90 ? '&hellip;' : ''); ?></p>
                <a href="recipe_detail.php?id=<?php echo (int)$nr['recipe_id']; ?>" class="news-card-link">View Recipe &rarr;</a>
            </div>
        </div>
<?php
    endwhile;
else:
?>
        <div class="news-card"><div class="news-card-body"><h3>Plant-Based Revolution</h3><p>Discover trending vegan dishes loved worldwide.</p><a href="Recipe.php" class="news-card-link">View Recipes &rarr;</a></div></div>
        <div class="news-card"><div class="news-card-body"><h3>5-Minute Breakfast Ideas</h3><p>Quick and healthy meals to start your day right.</p><a href="Recipe.php" class="news-card-link">View Recipes &rarr;</a></div></div>
        <div class="news-card"><div class="news-card-body"><h3>Street Food Specials</h3><p>Explore authentic street food flavors.</p><a href="Recipe.php" class="news-card-link">View Recipes &rarr;</a></div></div>
<?php endif; ?>
    </div>
    <div class="news-cta">
        <a href="Recipe.php" class="news-all-btn">View All Recipes &rarr;</a>
    </div>
</section>

<!-- EVENT CAROUSEL -->
<section class="carousel">
    <h2>🎉 Upcoming Cooking Events</h2>
    <p class="carousel-sub">Join us live &mdash; spaces are limited</p>
    <div class="carousel-wrapper">
        <button class="carousel-btn carousel-prev" onclick="moveSlide(-1)" aria-label="Previous event">&#8249;</button>
        <div class="carousel-container">
            <div class="slide active">
                <span class="slide-date">25 Mar</span>
                <span class="slide-title">Italian Cooking Workshop</span>
                <span class="slide-desc">Master fresh pasta, risotto &amp; classic sauces with our resident chef.</span>
            </div>
            <div class="slide">
                <span class="slide-date">2 Apr</span>
                <span class="slide-title">Healthy Baking Masterclass</span>
                <span class="slide-desc">Low-sugar, high-flavour baking techniques for guilt-free treats.</span>
            </div>
            <div class="slide">
                <span class="slide-date">10 Apr</span>
                <span class="slide-title">Indian Spice Secrets</span>
                <span class="slide-desc">Unlock the art of spice blending and authentic curry making.</span>
            </div>
        </div>
        <button class="carousel-btn carousel-next" onclick="moveSlide(1)" aria-label="Next event">&#8250;</button>
    </div>
    <div class="carousel-dots" id="carouselDots"></div>
</section>

<!-- FOOTER -->
<footer>
    <div class="social">
        <a href="https://www.facebook.com/foodfusion" target="_blank" rel="noopener noreferrer">Facebook</a>
        <a href="https://www.instagram.com/foodfusion" target="_blank" rel="noopener noreferrer">Instagram</a>
        <a href="https://www.youtube.com/foodfusion" target="_blank" rel="noopener noreferrer">YouTube</a>
    </div>
    <p><a href="culinary_resources.php">Culinary Resources</a> | <a href="educational_resources.php">Educational Resources</a> | <a href="privacy_policy.php">Privacy Policy</a> | &copy; 2026 FoodFusion</p>
</footer>

<!-- JOIN POPUP -->
<div id="popup" class="popup">
    <div class="popup-content">
        <span class="close" onclick="closePopup()">&times;</span>
        <h2>Become a Member</h2>

        <form action="register.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

            <input type="text" name="first_name" placeholder="First Name" required>
            <input type="text" name="last_name" placeholder="Last Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password (min. 6 characters)" required minlength="6">

            <p>Dietary Preference:</p>

            <label>
                <input type="radio" name="diet" value="Vegetarian" required> Vegetarian
            </label>

            <label>
                <input type="radio" name="diet" value="Non-Vegetarian"> Non-Vegetarian
            </label>

            <label>
                <input type="radio" name="diet" value="Vegan"> Vegan
            </label>

            <br><br>

            <button type="submit">Register</button>
        </form>
    </div>
</div>

<!-- COOKIE CONSENT -->
<div class="cookie" id="cookieBox">
    <p>This website uses cookies to improve your experience. <a href="privacy_policy.php" class="cookie-link">Learn more</a></p>
    <div class="cookie-btns">
        <button class="cookie-accept" onclick="acceptCookies()">Accept</button>
        <button class="cookie-decline" onclick="declineCookies()">Decline</button>
    </div>
</div>


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