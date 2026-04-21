<?php
session_start();
require 'db.php';

// Generate CSRF token if not set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = "";

if(isset($_POST['login'])){

// CSRF validation
if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    $message = "Security check failed. Please try again.";
} else {

$email = $_POST['email'];
$password = $_POST['password'];

if(isset($_SESSION['lock_time']) && time() < $_SESSION['lock_time']){
$remaining = $_SESSION['lock_time'] - time();
$message = "Too many failed attempts. Try again in $remaining seconds.";
} else {

$sql = "SELECT * FROM users WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s",$email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 1){
$user = $result->fetch_assoc();

if(password_verify($password,$user['password'])){
$_SESSION['user'] = $user['email'];
$_SESSION['attempts'] = 0;
header("Location: Home.php");
exit();

}else{
$_SESSION['attempts'] = ($_SESSION['attempts'] ?? 0) + 1;
if($_SESSION['attempts'] >= 3){
$_SESSION['lock_time'] = time() + 180;
$message = "Account locked for 3 minutes.";
}else{
$remaining_attempts = 3 - $_SESSION['attempts'];
$message = "Invalid password. $remaining_attempts attempt(s) remaining.";
}
}

}else{
$message = "No account found with that email.";
}
}
} // end CSRF check
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FoodFusion | Login</title>
<link rel="stylesheet" href="Home1.css">
<link rel="stylesheet" href="login1.css">
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
        <a href="Login.php" class="active">Login</a>
    </nav>
        <button class="hamburger" id="hamburger" onclick="toggleNav()" aria-label="Toggle navigation">
            <span></span><span></span><span></span>
        </button>
</header>

<div class="login-page">

    <!-- LEFT: Food image panel -->
    <div class="login-left">
        <div class="overlay-content">
            <h1>🍽 FoodFusion</h1>
            <p class="tagline">Where Passion Meets Flavour</p>
            <div class="food-badges">
                <span>🍕 Italian</span>
                <span>🍜 Asian</span>
                <span>🌮 Mexican</span>
                <span>🍛 Indian</span>
                <span>🥗 Healthy</span>
                <span>🍰 Desserts</span>
            </div>
            <div class="stats-row">
                <div class="stat"><strong>10K+</strong><small>Members</small></div>
                <div class="stat"><strong>5K+</strong><small>Recipes</small></div>
                <div class="stat"><strong>120+</strong><small>Cuisines</small></div>
            </div>
        </div>
    </div>

    <!-- RIGHT: Login form -->
    <div class="login-right">
        <form method="POST" class="login-box" id="loginForm">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

            <div class="chef-icon">👨‍🍳</div>

            <div class="login-header">
                <h2>Welcome Back, Chef!</h2>
                <p>Sign in to access your recipes & community</p>
            </div>

            <?php if($message != ""){ ?>
            <div class="alert <?php echo strpos($message,'locked') !== false ? 'alert-lock' : 'alert-error'; ?>">
                <?php echo strpos($message,'locked') !== false ? '🔒' : '⚠️'; ?>
                <?php echo $message; ?>
            </div>
            <?php } ?>

            <div class="input-group">
                <span class="input-icon">📧</span>
                <input type="email" name="email" placeholder="Your email address" required
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>

            <div class="input-group">
                <span class="input-icon">🔑</span>
                <input type="password" name="password" id="passwordField" placeholder="Your password" required minlength="6">
                <span class="toggle-pass" onclick="togglePassword()" title="Show/hide password">👁</span>
            </div>

            <div style="text-align:right; margin: -8px 0 16px;">
                <a href="forgot_password.php" style="color:#ff6f3c; font-size:13px; text-decoration:none; font-weight:600;">
                    Forgot Password?
                </a>
            </div>

            <button type="submit" name="login" class="login-btn">
                🍴 Let's Cook!
            </button>

            <div class="divider"><span>new to foodfusion?</span></div>

            <a href="Home.php" class="register-btn">🧑‍🍳 Create Free Account</a>

            <p class="footer-note">Join thousands of food lovers sharing recipes every day</p>

        </form>
    </div>

</div>

<script>
function togglePassword(){
    const field = document.getElementById('passwordField');
    const icon  = document.querySelector('.toggle-pass');
    if(field.type === 'password'){
        field.type = 'text';
        icon.textContent = '🙈';
    } else {
        field.type = 'password';
        icon.textContent = '👁';
    }
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