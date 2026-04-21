<?php
session_start();
require 'db.php';

// Generate CSRF token if not set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$success = "";
$error = "";

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    // CSRF validation
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = "Security check failed. Please try again.";
    } else {
    $name    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    if(!empty($name) && !empty($email) && !empty($subject) && !empty($message) && filter_var($email, FILTER_VALIDATE_EMAIL)){
        $sql = "INSERT INTO contact_messages (name, email, subject, message) VALUES (?,?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $email, $subject, $message);
        if($stmt->execute()){
            $success = "Thank you, $name! Your message has been sent. We'll get back to you soon.";
        } else {
            $error = "Something went wrong. Please try again.";
        }
    } else {
        $error = "Please fill in all fields with a valid email address.";
    }
    } // end CSRF check
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FoodFusion | Contact Us</title>
<link rel="stylesheet" href="Home1.css">
<style>
/* HERO */
.contact-hero {
    background: linear-gradient(rgba(255,111,60,0.85), rgba(255,90,20,0.85)),
                url("imgs/global.jpeg") center/cover no-repeat;
    padding: 90px 40px;
    text-align: center;
    color: white;
}
.contact-hero h1 { font-size: 46px; margin-bottom: 12px; }
.contact-hero p  { font-size: 17px; opacity: 0.92; max-width: 550px; margin: 0 auto; }

/* CONTACT LAYOUT */
.contact-section {
    display: flex;
    gap: 40px;
    padding: 70px 60px;
    background: #FAF7F2;
    flex-wrap: wrap;
    justify-content: center;
}

/* INFO PANEL */
.contact-info {
    background: linear-gradient(160deg, #ff6f3c, #ff9a3c);
    color: white;
    border-radius: 20px;
    padding: 40px 35px;
    width: 280px;
    box-shadow: 0 15px 40px rgba(255,111,60,0.3);
}
.contact-info h2 { font-size: 24px; margin-bottom: 8px; }
.contact-info p  { opacity: 0.9; font-size: 14px; margin-bottom: 35px; line-height: 1.6; }
.info-item {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    margin-bottom: 25px;
}
.info-item .info-icon {
    font-size: 22px;
    margin-top: 2px;
}
.info-item h4 { margin: 0 0 4px; font-size: 15px; }
.info-item span { font-size: 13px; opacity: 0.88; }
.social-icons { margin-top: 35px; display: flex; gap: 12px; }
.social-icons a {
    background: rgba(255,255,255,0.2);
    color: white;
    text-decoration: none;
    padding: 8px 14px;
    border-radius: 10px;
    font-size: 13px;
    transition: background 0.3s;
}
.social-icons a:hover { background: rgba(255,255,255,0.35); }

/* FORM PANEL */
.contact-form-box {
    background: white;
    border-radius: 20px;
    padding: 40px 35px;
    flex: 1;
    min-width: 320px;
    max-width: 560px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.07);
}
.contact-form-box h2 { font-size: 26px; color: #333; margin-bottom: 5px; }
.contact-form-box .sub { color: #999; font-size: 14px; margin-bottom: 25px; }

.form-row {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}
.form-group {
    display: flex;
    flex-direction: column;
    flex: 1;
    min-width: 200px;
    margin-bottom: 18px;
}
.form-group label {
    font-size: 13px;
    font-weight: 600;
    color: #555;
    margin-bottom: 6px;
}
.form-group input,
.form-group select,
.form-group textarea {
    padding: 12px 14px;
    border: 1.5px solid #e8e8e8;
    border-radius: 10px;
    font-size: 14px;
    font-family: Arial, sans-serif;
    transition: border 0.3s, box-shadow 0.3s;
    background: #fafafa;
    color: #333;
}
.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: #ff6f3c;
    outline: none;
    box-shadow: 0 0 0 3px rgba(255,111,60,0.12);
    background: white;
}
.form-group textarea { resize: vertical; min-height: 130px; }

.submit-btn {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #ff6f3c, #ff9a3c);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
    margin-top: 5px;
}
.submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(255,111,60,0.35);
}

/* ALERTS */
.alert-success {
    background: #d4edda; color: #155724;
    padding: 14px 18px; border-radius: 10px;
    margin-bottom: 20px; font-size: 14px;
    border-left: 4px solid #28a745;
}
.alert-error {
    background: #f8d7da; color: #721c24;
    padding: 14px 18px; border-radius: 10px;
    margin-bottom: 20px; font-size: 14px;
    border-left: 4px solid #dc3545;
}

/* MAP SECTION */
.map-section {
    background: #FFF1EB;
    padding: 60px 40px;
    text-align: center;
}
.map-section h2 { font-size: 30px; color: #ff6f3c; margin-bottom: 30px; }
.map-placeholder {
    background: #ffe8dc;
    border: 2px dashed #ff6f3c;
    border-radius: 16px;
    height: 220px;
    display: flex;
    align-items: center;
    justify-content: center;
    max-width: 700px;
    margin: 0 auto;
    font-size: 40px;
    flex-direction: column;
    gap: 10px;
    color: #ff6f3c;
}
.map-placeholder p { font-size: 16px; font-weight: 600; margin: 0; }
.map-placeholder span { font-size: 13px; color: #aaa; }

/* FAQ */
.faq-section {
    padding: 70px 60px;
    background: #FAF7F2;
    max-width: 800px;
    margin: 0 auto;
}
.faq-section h2 { font-size: 30px; color: #ff6f3c; margin-bottom: 30px; text-align: center; }
.faq-item {
    border-bottom: 1px solid #eee;
    padding: 18px 0;
    cursor: pointer;
}
.faq-item h4 {
    font-size: 15px; color: #333;
    display: flex; justify-content: space-between; align-items: center;
    margin: 0;
}
.faq-item h4 span { font-size: 20px; color: #ff6f3c; transition: transform 0.3s; }
.faq-item p {
    font-size: 14px; color: #777; line-height: 1.7;
    margin: 10px 0 0; display: none;
}
.faq-item.open h4 span { transform: rotate(45deg); }
.faq-item.open p { display: block; }

footer { background:#333; color:white; text-align:center; padding:20px; }
footer a { color:#ff9966; text-decoration:none; margin:0 10px; }
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
        <a href="contact.php" class="active">Contact</a>
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
<section class="contact-hero">
    <h1>Get In Touch</h1>
    <p>Have a question, recipe idea, or just want to say hello? We'd love to hear from you!</p>
</section>

<!-- CONTACT SECTION -->
<section class="contact-section">

    <!-- INFO PANEL -->
    <div class="contact-info">
        <h2>Contact Info</h2>
        <p>Reach out to us through any of the channels below and we'll respond as soon as possible.</p>

        <div class="info-item">
            <div class="info-icon">📍</div>
            <div>
                <h4>Our Location</h4>
                <span>12 Culinary Lane, London, UK</span>
            </div>
        </div>
        <div class="info-item">
            <div class="info-icon">📧</div>
            <div>
                <h4>Email Us</h4>
                <span>hello@foodfusion.com</span>
            </div>
        </div>
        <div class="info-item">
            <div class="info-icon">📞</div>
            <div>
                <h4>Call Us</h4>
                <span>+44 20 1234 5678</span>
            </div>
        </div>
        <div class="info-item">
            <div class="info-icon">🕐</div>
            <div>
                <h4>Working Hours</h4>
                <span>Mon – Fri: 9am – 6pm</span>
            </div>
        </div>

        <div class="social-icons">
            <a href="https://www.facebook.com/foodfusion" target="_blank" rel="noopener noreferrer">FB</a>
            <a href="https://www.instagram.com/foodfusion" target="_blank" rel="noopener noreferrer">IG</a>
            <a href="https://www.youtube.com/foodfusion" target="_blank" rel="noopener noreferrer">YT</a>
        </div>
    </div>

    <!-- FORM -->
    <div class="contact-form-box">
        <h2>Send a Message</h2>
        <p class="sub">Fill in the form below and we'll get back to you within 24 hours.</p>

        <?php if($success){ echo "<div class='alert-success'>✅ $success</div>"; } ?>
        <?php if($error)  { echo "<div class='alert-error'>❌ $error</div>"; } ?>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <div class="form-row">
                <div class="form-group">
                    <label>Your Name</label>
                    <input type="text" name="name" placeholder="John Smith" required
                           value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="john@email.com" required
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
            </div>
            <div class="form-group">
                <label>Subject</label>
                <select name="subject">
                    <option value="">Select a subject...</option>
                    <option value="Recipe Request">Recipe Request</option>
                    <option value="General Enquiry">General Enquiry</option>
                    <option value="Technical Support">Technical Support</option>
                    <option value="Partnership">Partnership</option>
                    <option value="Feedback">Feedback</option>
                </select>
            </div>
            <div class="form-group">
                <label>Message</label>
                <textarea name="message" placeholder="Write your message here..." required></textarea>
            </div>
            <button type="submit" class="submit-btn">🚀 Send Message</button>
        </form>
    </div>

</section>

<!-- MAP PLACEHOLDER -->
<section class="map-section">
    <h2>📍 Find Us</h2>
    <div class="map-placeholder">
        <span style="font-size:50px;">🗺️</span>
        <p>12 Culinary Lane, London, UK</p>
        <span>Map integration coming soon</span>
    </div>
</section>

<!-- FAQ -->
<section class="faq-section">
    <h2>Frequently Asked Questions</h2>
    <div class="faq-item">
        <h4>How do I submit my own recipe? <span>+</span></h4>
        <p>Log in to your account, head to the Community Cookbook, and click "Add Recipe". Fill in the details and submit — it's that easy!</p>
    </div>
    <div class="faq-item">
        <h4>Is FoodFusion free to use? <span>+</span></h4>
        <p>Yes! Registering and browsing FoodFusion is completely free. Simply create an account to access all features.</p>
    </div>
    <div class="faq-item">
        <h4>How long does it take to get a response? <span>+</span></h4>
        <p>We aim to respond to all enquiries within 24 hours on business days.</p>
    </div>
    <div class="faq-item">
        <h4>Can I request a specific recipe? <span>+</span></h4>
        <p>Absolutely! Use the contact form above and select "Recipe Request" as the subject. Our chefs will do their best to help.</p>
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
document.querySelectorAll('.faq-item').forEach(item => {
    item.addEventListener('click', () => {
        item.classList.toggle('open');
    });
});
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
