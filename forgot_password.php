<?php
session_start();
require 'db.php';

$step    = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$message = '';
$msgType = '';

/* ── STEP 1: Submit email ───────────────────────────────────────────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_reset'])) {
    $email = trim($_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address.';
        $msgType = 'error';
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            /* Generate a 6-digit OTP */
            $otp     = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $expires = time() + 180; /* 3 minutes */

            /* Store OTP in session (no email server needed for localhost) */
            $_SESSION['reset_email']   = $email;
            $_SESSION['reset_otp']     = $otp;
            $_SESSION['reset_expires'] = $expires;

            /* ── For localhost: show OTP on screen ── */
            $_SESSION['show_otp'] = $otp;

            header("Location: forgot_password.php?step=2");
            exit();
        } else {
            /* Don't reveal whether email exists */
            $_SESSION['reset_email']   = $email;
            $_SESSION['reset_otp']     = '000000';
            $_SESSION['reset_expires'] = time() + 180;
            $_SESSION['show_otp']      = null;
            header("Location: forgot_password.php?step=2");
            exit();
        }
    }
}

/* ── STEP 2: Verify OTP ─────────────────────────────────────────────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_otp'])) {
    $entered = trim($_POST['otp']);

    if (!isset($_SESSION['reset_otp'])) {
        header("Location: forgot_password.php");
        exit();
    }

    if (time() > $_SESSION['reset_expires']) {
        $message = 'The code has expired. Please request a new one.';
        $msgType = 'error';
        $step    = 2;
    } elseif ($entered === $_SESSION['reset_otp']) {
        $_SESSION['otp_verified'] = true;
        header("Location: forgot_password.php?step=3");
        exit();
    } else {
        $message = 'Incorrect code. Please try again.';
        $msgType = 'error';
        $step    = 2;
    }
}

/* ── STEP 3: Set new password ───────────────────────────────────────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['set_password'])) {
    if (!isset($_SESSION['otp_verified']) || !$_SESSION['otp_verified']) {
        header("Location: forgot_password.php");
        exit();
    }

    $newPass     = $_POST['new_password'];
    $confirmPass = $_POST['confirm_password'];

    if (strlen($newPass) < 6) {
        $message = 'Password must be at least 6 characters.';
        $msgType = 'error';
        $step    = 3;
    } elseif ($newPass !== $confirmPass) {
        $message = 'Passwords do not match.';
        $msgType = 'error';
        $step    = 3;
    } else {
        $hashed = password_hash($newPass, PASSWORD_DEFAULT);
        $email  = $_SESSION['reset_email'];

        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashed, $email);

        if ($stmt->execute()) {
            /* Clear all reset session data */
            unset($_SESSION['reset_email'], $_SESSION['reset_otp'],
                  $_SESSION['reset_expires'], $_SESSION['otp_verified'],
                  $_SESSION['show_otp']);

            $message = 'success';
        } else {
            $message = 'Something went wrong. Please try again.';
            $msgType = 'error';
            $step    = 3;
        }
    }
}

/* ── Determine display step from URL ───────────────────────────────── */
if ($step === 1 && empty($message)) {
    /* default */
}
if ($message === 'success') { $step = 4; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FoodFusion | Reset Password</title>
<link rel="stylesheet" href="Home1.css">
<link rel="stylesheet" href="login.css">
<style>
.reset-page {
    min-height: calc(100vh - 120px);
    display: flex;
    align-items: center;
    justify-content: center;
    background: #FAF7F2;
    padding: 40px 20px;
}
.reset-box {
    background: white;
    border-radius: 24px;
    padding: 48px 44px;
    width: 100%;
    max-width: 460px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.1);
    text-align: center;
}
.reset-icon { font-size: 56px; margin-bottom: 16px; }
.reset-box h2 { font-size: 26px; color: #333; margin: 0 0 8px; }
.reset-box .sub { color: #999; font-size: 15px; margin-bottom: 30px; line-height: 1.5; }

/* Steps indicator */
.steps { display: flex; justify-content: center; gap: 0; margin-bottom: 36px; }
.step-dot {
    width: 32px; height: 32px;
    border-radius: 50%;
    background: #f0ece5;
    color: #bbb;
    font-size: 13px;
    font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    position: relative;
}
.step-dot.active { background: #ff6f3c; color: white; }
.step-dot.done   { background: #28a745; color: white; }
.step-line {
    width: 60px; height: 3px;
    background: #f0ece5;
    align-self: center;
    margin: 0 4px;
}
.step-line.done { background: #28a745; }

/* Form elements */
.input-group {
    display: flex;
    align-items: center;
    gap: 12px;
    border: 2px solid #e8e8e8;
    border-radius: 12px;
    padding: 12px 16px;
    margin-bottom: 16px;
    transition: border 0.3s;
    background: #fafafa;
}
.input-group:focus-within { border-color: #ff6f3c; background: white; box-shadow: 0 0 0 3px rgba(255,111,60,0.1); }
.input-group span { font-size: 18px; }
.input-group input {
    flex: 1; border: none; background: none;
    font-size: 15px; color: #333; outline: none;
}
.input-group input::placeholder { color: #bbb; }

/* OTP input */
.otp-inputs { display: flex; gap: 10px; justify-content: center; margin-bottom: 20px; }
.otp-inputs input {
    width: 48px; height: 56px;
    border: 2px solid #e8e8e8;
    border-radius: 12px;
    text-align: center;
    font-size: 22px;
    font-weight: 700;
    color: #333;
    outline: none;
    transition: all 0.3s;
    background: #fafafa;
}
.otp-inputs input:focus { border-color: #ff6f3c; background: white; box-shadow: 0 0 0 3px rgba(255,111,60,0.1); }
.otp-inputs input.filled { border-color: #ff6f3c; background: #fff8f5; }

.btn-primary {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #ff6f3c, #ff9a3c);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s;
    margin-top: 8px;
}
.btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(255,111,60,0.35); }

.btn-secondary {
    display: inline-block;
    margin-top: 16px;
    color: #ff6f3c;
    font-size: 14px;
    text-decoration: none;
    font-weight: 600;
}
.btn-secondary:hover { text-decoration: underline; }

/* Alerts */
.alert {
    padding: 12px 16px;
    border-radius: 10px;
    font-size: 14px;
    margin-bottom: 20px;
    text-align: left;
}
.alert.error   { background: #fdecea; color: #c0392b; border-left: 4px solid #e74c3c; }
.alert.success { background: #d4edda; color: #155724; border-left: 4px solid #28a745; }

/* OTP notice box (localhost only) */
.dev-notice {
    background: #fffbea;
    border: 2px dashed #f0c040;
    border-radius: 12px;
    padding: 14px 18px;
    margin-bottom: 20px;
    font-size: 13px;
    color: #7a5c00;
    text-align: left;
}
.dev-notice strong { display: block; margin-bottom: 4px; font-size: 14px; }
.dev-otp { font-size: 28px; font-weight: 800; color: #ff6f3c; letter-spacing: 8px; text-align: center; margin-top: 8px; }

/* Timer */
.otp-timer { font-size: 13px; color: #aaa; margin-bottom: 16px; }
.otp-timer span { color: #ff6f3c; font-weight: 700; }

/* Password strength */
.strength-bar { height: 4px; border-radius: 4px; background: #f0ece5; margin-bottom: 4px; overflow: hidden; }
.strength-fill { height: 100%; border-radius: 4px; width: 0; transition: all 0.4s; }
.strength-label { font-size: 12px; color: #aaa; text-align: left; margin-bottom: 16px; }

/* Success screen */
.success-screen { padding: 20px 0; }
.success-screen .big-check { font-size: 72px; margin-bottom: 16px; }
.success-screen h2 { color: #28a745; }
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
        <a href="Login.php">Login</a>
    </nav>
        <button class="hamburger" id="hamburger" onclick="toggleNav()" aria-label="Toggle navigation">
            <span></span><span></span><span></span>
        </button>
</header>

<div class="reset-page">
<div class="reset-box">

    <!-- STEPS INDICATOR -->
    <div class="steps">
        <div class="step-dot <?php echo $step >= 1 ? ($step > 1 ? 'done' : 'active') : ''; ?>">
            <?php echo $step > 1 ? '✓' : '1'; ?>
        </div>
        <div class="step-line <?php echo $step > 1 ? 'done' : ''; ?>"></div>
        <div class="step-dot <?php echo $step >= 2 ? ($step > 2 ? 'done' : 'active') : ''; ?>">
            <?php echo $step > 2 ? '✓' : '2'; ?>
        </div>
        <div class="step-line <?php echo $step > 2 ? 'done' : ''; ?>"></div>
        <div class="step-dot <?php echo $step >= 3 ? ($step > 3 ? 'done' : 'active') : ''; ?>">
            <?php echo $step > 3 ? '✓' : '3'; ?>
        </div>
    </div>

    <?php if (!empty($message) && $msgType === 'error'): ?>
    <div class="alert error">⚠️ <?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <!-- ── STEP 1: Enter Email ── -->
    <?php if ($step === 1): ?>
    <div class="reset-icon">🔑</div>
    <h2>Forgot Password?</h2>
    <p class="sub">Enter the email address linked to your account and we'll send you a reset code.</p>
    <form method="POST">
        <div class="input-group">
            <span>📧</span>
            <input type="email" name="email" placeholder="Your email address" required
                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        </div>
        <button type="submit" name="request_reset" class="btn-primary">📨 Send Reset Code</button>
    </form>
    <a href="Login.php" class="btn-secondary">← Back to Login</a>

    <!-- ── STEP 2: Enter OTP ── -->
    <?php elseif ($step === 2): ?>
    <div class="reset-icon">📲</div>
    <h2>Enter Reset Code</h2>
    <p class="sub">We've generated a 6-digit code for <strong><?php echo htmlspecialchars($_SESSION['reset_email'] ?? ''); ?></strong></p>

    <?php if (!empty($_SESSION['show_otp'])): ?>
    <div class="dev-notice">
        <strong>🛠 Localhost Dev Mode</strong>
        In production, this code would be emailed. For now, your code is:
        <div class="dev-otp"><?php echo $_SESSION['show_otp']; ?></div>
    </div>
    <?php endif; ?>

    <div class="otp-timer">Code expires in: <span id="countdown">3:00</span></div>

    <form method="POST" id="otpForm">
        <div class="otp-inputs">
            <input type="text" maxlength="1" class="otp-digit" id="d1" inputmode="numeric">
            <input type="text" maxlength="1" class="otp-digit" id="d2" inputmode="numeric">
            <input type="text" maxlength="1" class="otp-digit" id="d3" inputmode="numeric">
            <input type="text" maxlength="1" class="otp-digit" id="d4" inputmode="numeric">
            <input type="text" maxlength="1" class="otp-digit" id="d5" inputmode="numeric">
            <input type="text" maxlength="1" class="otp-digit" id="d6" inputmode="numeric">
        </div>
        <input type="hidden" name="otp" id="otpHidden">
        <button type="submit" name="verify_otp" class="btn-primary">✅ Verify Code</button>
    </form>
    <a href="forgot_password.php" class="btn-secondary">↺ Request a new code</a>

    <!-- ── STEP 3: New Password ── -->
    <?php elseif ($step === 3): ?>
    <div class="reset-icon">🔒</div>
    <h2>Set New Password</h2>
    <p class="sub">Choose a strong new password for your account.</p>
    <form method="POST" id="passForm">
        <div class="input-group">
            <span>🔑</span>
            <input type="password" name="new_password" id="newPass" placeholder="New password (min. 6 chars)" required oninput="checkStrength(this.value)">
        </div>
        <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
        <div class="strength-label" id="strengthLabel">Password strength</div>
        <div class="input-group">
            <span>🔒</span>
            <input type="password" name="confirm_password" id="confirmPass" placeholder="Confirm new password" required>
        </div>
        <button type="submit" name="set_password" class="btn-primary">💾 Save New Password</button>
    </form>

    <!-- ── STEP 4: Success ── -->
    <?php elseif ($step === 4): ?>
    <div class="success-screen">
        <div class="big-check">✅</div>
        <h2>Password Reset!</h2>
        <p class="sub">Your password has been updated successfully. You can now log in with your new password.</p>
        <a href="Login.php" class="btn-primary" style="display:block; text-decoration:none; padding:14px; margin-top:10px;">
            🍴 Go to Login
        </a>
    </div>
    <?php endif; ?>

</div>
</div>

<script>
/* ── OTP digit auto-advance ── */
const digits = document.querySelectorAll('.otp-digit');
digits.forEach((input, i) => {
    input.addEventListener('input', () => {
        input.value = input.value.replace(/\D/g, '');
        if (input.value && i < digits.length - 1) digits[i + 1].focus();
        input.classList.toggle('filled', !!input.value);
        assembleOtp();
    });
    input.addEventListener('keydown', e => {
        if (e.key === 'Backspace' && !input.value && i > 0) digits[i - 1].focus();
    });
    input.addEventListener('paste', e => {
        e.preventDefault();
        const paste = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g,'').slice(0,6);
        paste.split('').forEach((ch, idx) => {
            if (digits[idx]) { digits[idx].value = ch; digits[idx].classList.add('filled'); }
        });
        assembleOtp();
        if (digits[paste.length]) digits[paste.length].focus();
    });
});

function assembleOtp() {
    const hidden = document.getElementById('otpHidden');
    if (hidden) hidden.value = [...digits].map(d => d.value).join('');
}

/* ── Countdown timer ── */
const countdownEl = document.getElementById('countdown');
if (countdownEl) {
    const expires = <?php echo isset($_SESSION['reset_expires']) ? (int)$_SESSION['reset_expires'] : 0; ?>;
    function tick() {
        const remaining = expires - Math.floor(Date.now() / 1000);
        if (remaining <= 0) {
            countdownEl.textContent = '0:00';
            countdownEl.style.color = '#e74c3c';
            return;
        }
        const m = Math.floor(remaining / 60);
        const s = remaining % 60;
        countdownEl.textContent = m + ':' + String(s).padStart(2, '0');
        setTimeout(tick, 1000);
    }
    tick();
}

/* ── Password strength ── */
function checkStrength(val) {
    const fill  = document.getElementById('strengthFill');
    const label = document.getElementById('strengthLabel');
    if (!fill) return;
    let score = 0;
    if (val.length >= 6)  score++;
    if (val.length >= 10) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    const levels = [
        { pct:'20%', color:'#e74c3c', text:'Very weak' },
        { pct:'40%', color:'#e67e22', text:'Weak' },
        { pct:'60%', color:'#f1c40f', text:'Fair' },
        { pct:'80%', color:'#2ecc71', text:'Strong' },
        { pct:'100%',color:'#27ae60', text:'Very strong ✓' }
    ];
    const lvl = levels[Math.min(score, 4)];
    fill.style.width = lvl.pct;
    fill.style.background = lvl.color;
    label.textContent = lvl.text;
    label.style.color = lvl.color;
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
