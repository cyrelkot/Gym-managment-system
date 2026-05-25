<?php
error_reporting(0);
include 'include/config.php';

$uid = $_SESSION['uid'];

if (isset($_POST['submit'])) {
    $pid = $_POST['pid'];

    $sql = "INSERT INTO tblbooking (package_id, userid) VALUES (:pid, :uid)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':pid', $pid, PDO::PARAM_STR);
    $query->bindParam(':uid', $uid, PDO::PARAM_STR);
    $query->execute();

    echo "<script>alert('Package has been booked.');</script>";
    echo "<script>window.location.href='booking-history.php'</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Gym Management System | Contact</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/user.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"/>
    <link rel="icon" type="image/png" href="icon-fonts/gym-logo.png">
</head>

<body class="contact-page">

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
    <h1>CONTACT US</h1>
    <p>Push harder. Train smarter. Become unstoppable.</p>
</div>

<!-- CONTENT -->
<div class="section">
    <div class="card">

        <h3>GET IN TOUCH</h3>

        <div class="info">
            <i class="fa fa-envelope"></i>
            <span><strong>Email:</strong> gymfitness@gmail.com</span>
        </div>
        <div class="info">
            <i class="fa fa-phone"></i>
            <span><strong>Contact:</strong> 09925965016</span>
        </div>
        <div class="info">
            <i class="fa fa-map-marker"></i>
            <span><strong>Address:</strong> Rizal St.</span>
        </div>

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
