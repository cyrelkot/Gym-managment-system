<?php
include 'include/config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>About Our Gym</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/user.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"/>
<link rel="icon" type="image/png" href="icon-fonts/gym-logo.png">
</head>

<body class="about-page">

<!-- NAVBAR -->
<div class="navbar">
    <div class="logo">GYM</div>
    <?php if (isset($_SESSION['uid'])) { ?>
    <div class="nav-center">
        <a href="index.php">Home</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
        <a href="booking-history.php">Booking History</a>
    </div>
    <div class="nav-right">
        <div class="user-menu">
            <div class="user-trigger">
                <span class="user-avatar"><?php echo htmlspecialchars(strtoupper(substr($_SESSION['fname'], 0, 1)), ENT_QUOTES, 'UTF-8'); ?></span>
                <span class="user-name"><?php echo htmlspecialchars($_SESSION['fname'], ENT_QUOTES, 'UTF-8'); ?></span>
                <span class="user-caret">&#9660;</span>
            </div>
            <div class="user-dropdown">
                <a href="profile.php">Profile</a>
                <a href="changepassword.php">Change Password</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </div>
    <?php } else { ?>
    <div class="nav-center">
        <a href="index.php">Home</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
        <a href="admin/">Admin</a>
    </div>
    <?php } ?>
</div>

<!-- HERO -->
<div class="hero">
    <h1>About Our Fitness Center</h1>
</div>

<!-- ABOUT -->
<div class="about">
    <h2>Who We Are</h2>
    <p>
        Our gym is dedicated to helping people achieve their health and fitness goals.
        We provide modern gym equipment, certified trainers, and flexible workout programs.
        Whether you are a beginner or an experienced athlete, our gym offers the perfect
        environment to improve your strength, endurance, and overall well-being.
    </p>
</div>

<!-- FEATURES -->
<div class="features">

    <div class="feature-box">
        <i class="fa fa-bolt"></i>
        <h3>Modern Equipment</h3>
        <p>Train with high-quality machines and fitness equipment designed for effective workouts.</p>
    </div>

    <div class="feature-box">
        <i class="fa fa-user"></i>
        <h3>Professional Trainers</h3>
        <p>Our experienced trainers guide members to achieve their fitness goals safely.</p>
    </div>

    <div class="feature-box">
        <i class="fa fa-calendar"></i>
        <h3>Flexible Programs</h3>
        <p>Choose workout programs that fit your schedule and personal fitness goals.</p>
    </div>

</div>

<!-- FOOTER -->
<footer class="footer">
    <div class="footer-brand">GYM</div>
    <div class="footer-tagline">Train harder. Live better.</div>
    <div class="footer-copy">© 2026 Gym Management System. All rights reserved.</div>
</footer>

<script>
(function() {
    var trigger = document.querySelector('.user-trigger');
    if (!trigger) return;
    var menu = trigger.closest('.user-menu');
    trigger.addEventListener('click', function(e) {
        e.stopPropagation();
        menu.classList.toggle('open');
    });
    document.addEventListener('click', function() {
        menu.classList.remove('open');
    });
})();
</script>

</body>
</html>
