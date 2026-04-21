<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FoodFusion | Privacy Policy</title>
<link rel="stylesheet" href="Home1.css">
<style>
.policy-hero {
    background: linear-gradient(135deg, #ff6f3c, #ff9a3c);
    padding: 60px 40px;
    text-align: center;
    color: white;
}
.policy-hero h1 { font-size: 40px; margin-bottom: 10px; }
.policy-hero p  { font-size: 15px; opacity: 0.9; }
.policy-body {
    max-width: 860px;
    margin: 0 auto;
    padding: 60px 40px;
}
.policy-section { margin-bottom: 40px; }
.policy-section h2 {
    font-size: 22px;
    color: #ff6f3c;
    margin-bottom: 12px;
    padding-bottom: 8px;
    border-bottom: 2px solid #fff3ec;
}
.policy-section p, .policy-section li {
    font-size: 15px;
    color: #555;
    line-height: 1.8;
    margin-bottom: 10px;
}
.policy-section ul { padding-left: 20px; }
.policy-section li { margin-bottom: 6px; }
.policy-updated {
    text-align: center;
    color: #aaa;
    font-size: 13px;
    padding: 0 0 40px;
}
</style>
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
        <?php if(isset($_SESSION['user'])){ ?>
        <button class="nav-btn" onclick="window.location.href='profile.php'">Profile</button>
        <?php } else { ?>
        <button class="nav-btn" onclick="window.location.href='Home.php'">Join Us</button>
        <?php } ?>
    </nav>
        <button class="hamburger" id="hamburger" onclick="toggleNav()" aria-label="Toggle navigation">
            <span></span><span></span><span></span>
        </button>
</header>

<section class="policy-hero">
    <h1>🔒 Privacy Policy</h1>
    <p>How FoodFusion collects, uses, and protects your information</p>
</section>

<p class="policy-updated">Last updated: March 2026</p>

<div class="policy-body">

    <div class="policy-section">
        <h2>1. Information We Collect</h2>
        <p>When you register on FoodFusion, we collect the following personal information:</p>
        <ul>
            <li>First name and last name</li>
            <li>Email address</li>
            <li>Password (stored securely using bcrypt hashing — never in plain text)</li>
            <li>Dietary preference (Vegetarian, Non-Vegetarian, or Vegan)</li>
        </ul>
        <p>We also collect information you voluntarily submit, such as recipes you share in the Community Cookbook and messages sent through the Contact Us form.</p>
    </div>

    <div class="policy-section">
        <h2>2. How We Use Your Information</h2>
        <p>We use the information we collect to:</p>
        <ul>
            <li>Create and manage your FoodFusion account</li>
            <li>Display your name and profile information</li>
            <li>Associate recipes and community contributions with your account</li>
            <li>Respond to enquiries submitted via our Contact Us form</li>
            <li>Improve the FoodFusion platform and user experience</li>
        </ul>
        <p>We do not sell, trade, or rent your personal information to third parties.</p>
    </div>

    <div class="policy-section">
        <h2>3. Cookies</h2>
        <p>FoodFusion uses cookies to enhance your experience on our platform. Specifically, we use:</p>
        <ul>
            <li><strong>Session cookies</strong> — to keep you logged in during your visit</li>
            <li><strong>Preference cookies</strong> — to remember your cookie consent choice via localStorage</li>
        </ul>
        <p>You may accept or decline cookies via the cookie consent banner displayed on your first visit. Declining cookies may affect certain functionality.</p>
    </div>

    <div class="policy-section">
        <h2>4. Data Security</h2>
        <p>We take the security of your data seriously. Measures we have in place include:</p>
        <ul>
            <li>Passwords hashed using PHP's <code>password_hash()</code> with bcrypt</li>
            <li>Prepared SQL statements to prevent injection attacks</li>
            <li>Session-based authentication with lockout after 3 failed login attempts</li>
            <li>Account lockout reset after 3 minutes to prevent brute-force attacks</li>
        </ul>
    </div>

    <div class="policy-section">
        <h2>5. Your Rights</h2>
        <p>You have the right to:</p>
        <ul>
            <li>Access and update your personal information at any time via your Profile page</li>
            <li>Request deletion of your account and associated data by contacting us</li>
            <li>Withdraw consent for data processing at any time</li>
        </ul>
    </div>

    <div class="policy-section">
        <h2>6. Community Content</h2>
        <p>Recipes and content you share in the Community Cookbook are visible to all FoodFusion users. Please do not include sensitive personal information in recipe submissions. You retain ownership of your submitted content but grant FoodFusion a licence to display it on the platform.</p>
    </div>

    <div class="policy-section">
        <h2>7. Contact Us</h2>
        <p>If you have any questions about this Privacy Policy or how we handle your data, please contact us:</p>
        <ul>
            <li>📧 Email: <a href="mailto:hello@foodfusion.com" style="color:#ff6f3c;">hello@foodfusion.com</a></li>
            <li>📍 Address: 12 Culinary Lane, London, UK</li>
            <li>Or use our <a href="contact.php" style="color:#ff6f3c;">Contact Us</a> form</li>
        </ul>
    </div>

</div>

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
