<?php
session_start();
include 'include/config.php';
?>

<!DOCTYPE html>
<html>
<head>
<title>About Our Gym</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>

body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background:#0f0f0f;
    color:white;
}

.navbar{
    position:relative;
    display:flex;
    padding:20px;
    border-bottom:2px solid #ff6a00;
}

.logo{
    color:#ff6a00;
    font-weight:bold;
    font-size:22px;
}

.nav-center{
    position:absolute;
    left:50%;
    transform:translateX(-50%);
}

.nav-center a{
    color:#fff;
    margin:0 15px;
    text-decoration:none;
}

.nav-center a:hover{ color:#ff6a00; }

.hero{
    height:350px;
    background:url('https://images.unsplash.com/photo-1554284126-aa88f22d8b74') center/cover no-repeat;
    display:flex;
    align-items:center;
    justify-content:center;
    text-align:center;
}

.hero h1{
    font-size:40px;
    background:rgba(0,0,0,0.6);
    padding:20px 40px;
    border-radius:10px;
}

.about{
    max-width:900px;
    margin:70px auto;
    text-align:center;
    padding:0 20px;
}

.about h2{
    color:#ff6600;
    margin-bottom:20px;
}

.about p{
    line-height:1.8;
    color:#ddd;
}

.features{
    display:flex;
    justify-content:center;
    gap:40px;
    flex-wrap:wrap;
    padding:50px 20px;
}

.feature-box{
    background:#1a1a1a;
    padding:30px;
    border-radius:10px;
    width:250px;
    text-align:center;
    border-top:3px solid #ff6600;
}

.feature-box h3{
    color:#ff6600;
}

.feature-box p{
    color:#ccc;
}

.footer{
    text-align:center;
    padding:20px;
    background:#000;
    border-top:1px solid #222;
    color:#aaa;
    margin-top:40px;
}

</style>
</head>

<body>

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