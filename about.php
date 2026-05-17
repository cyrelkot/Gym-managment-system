<?php
session_start();
include 'include/config.php';
?>

<!DOCTYPE html>
<html>
<head>
<title>About Our Gym</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/user.css"/>
</head>

<body class="about-page">

<!-- NAVBAR -->
<div class="navbar">
    <div class="logo">GYM</div>
    <div class="nav-center">
        <a href="index.php">Home</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
        <?php if (isset($_SESSION['uid'])) { ?>
            <a href="booking-history.php">Booking History</a>
            <a href="logout.php">Logout</a>
        <?php } else { ?>
            <a href="admin/">Admin</a>
        <?php } ?>
    </div>
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
<h3>Modern Equipment</h3>
<p>Train with high-quality machines and fitness equipment designed for effective workouts.</p>
</div>

<div class="feature-box">
<h3>Professional Trainers</h3>
<p>Our experienced trainers guide members to achieve their fitness goals safely.</p>
</div>

<div class="feature-box">
<h3>Flexible Programs</h3>
<p>Choose workout programs that fit your schedule and personal fitness goals.</p>
</div>

</div>

<!-- FOOTER -->
<div class="footer">
© 2026 Gym Management System
</div>

</body>
</html>