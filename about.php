<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FoodFusion | About Us</title>
<link rel="stylesheet" href="Home1.css">
<link rel="stylesheet" href="about.css">

</head>
<body>

<header>
    <div class="logo">🍽 FoodFusion</div>
        <nav>
        <a href="Home.php">Home</a>
        <a href="about.php" class="active">About</a>
        <a href="Recipe.php">Recipes</a>
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

<!-- HERO -->
<section class="about-hero">
    <h1>About FoodFusion</h1>
    <p>We believe every meal tells a story. FoodFusion is where passionate home cooks come together to share, learn, and celebrate the joy of cooking.</p>
</section>

<!-- MISSION -->
<section class="mission">
    <img src="imgs/cooking.jpeg" alt="Our Mission">
    <div class="mission-text">
        <h2>Our Mission</h2>
        <p>FoodFusion was born from a simple belief — that great food has the power to bring people together. Our mission is to inspire home cooks of all skill levels to explore new cuisines, experiment with bold flavours, and share their culinary creations with a passionate global community.</p>
        <p style="margin-top:15px;">We are committed to making cooking accessible, enjoyable, and exciting — one recipe at a time.</p>
    </div>
</section>

<!-- STATS BANNER -->
<section class="stats-banner">
    <div class="stat-item"><h3>10K+</h3><p>Registered Members</p></div>
    <div class="stat-item"><h3>5K+</h3><p>Recipes Shared</p></div>
    <div class="stat-item"><h3>120+</h3><p>Cuisines Covered</p></div>
    <div class="stat-item"><h3>50+</h3><p>Cooking Events</p></div>
</section>

<!-- VALUES -->
<section class="values">
    <h2>Our Values</h2>
    <div class="values-grid">
        <div class="value-card">
            <div class="icon">🌍</div>
            <h3>Global Flavours</h3>
            <p>We celebrate culinary traditions from every corner of the world.</p>
        </div>
        <div class="value-card">
            <div class="icon">🤝</div>
            <h3>Community First</h3>
            <p>Our members are the heart of FoodFusion — we grow together.</p>
        </div>
        <div class="value-card">
            <div class="icon">🥗</div>
            <h3>Healthy Living</h3>
            <p>We champion nutritious, balanced meals for a better lifestyle.</p>
        </div>
        <div class="value-card">
            <div class="icon">💡</div>
            <h3>Innovation</h3>
            <p>We constantly explore new techniques and modern culinary ideas.</p>
        </div>
        <div class="value-card">
            <div class="icon">♿</div>
            <h3>Accessibility</h3>
            <p>Cooking is for everyone — we make it easy for all skill levels.</p>
        </div>
    </div>
</section>

<!-- TEAM -->
<section class="team">
    <h2>Meet The Team</h2>
    <p class="subtitle">The passionate people behind FoodFusion</p>
    <div class="team-grid">
        <div class="team-card">
            <div class="avatar">👩‍🍳</div>
            <h4>Sofia Marini</h4>
            <span>Founder & CEO</span>
            <p>Italian chef with 15 years of experience turning kitchens into creative spaces.</p>
        </div>
        <div class="team-card">
            <div class="avatar">👨‍💻</div>
            <h4>James Okafor</h4>
            <span>Lead Developer</span>
            <p>Full-stack developer passionate about building tools that connect communities.</p>
        </div>
        <div class="team-card">
            <div class="avatar">👩‍🎨</div>
            <h4>Priya Nair</h4>
            <span>UI/UX Designer</span>
            <p>Designs experiences that are beautiful, intuitive and user-friendly.</p>
        </div>
        <div class="team-card">
            <div class="avatar">👨‍🍳</div>
            <h4>Carlos Rivera</h4>
            <span>Head Chef & Content</span>
            <p>Creates and curates our recipe library with authentic global recipes.</p>
        </div>
    </div>
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
