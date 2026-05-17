<?php 
session_start();
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
    <title>Gym Management System</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="css/bootstrap.min.css"/>
    <link rel="stylesheet" href="css/font-awesome.min.css"/>
    <link rel="stylesheet" href="css/user.css"/>
</head>

<body class="contact-page">

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
    <h1>CONTACT US</h1>
    <p>Push harder. Train smarter. Become unstoppable.</p>
</div>

<!-- CONTENT -->
<div class="section">
    <div class="card">

        <h3>GET IN TOUCH</h3>

        <div class="info"><strong>Email:</strong> gymfitness@gmail.com</div>
        <div class="info"><strong>Contact:</strong> 09925965016</div>
        <div class="info"><strong>Address:</strong> Rizal St.</div>

    </div>
</div>

<!-- FOOTER -->
<div class="footer">
    © 2026 Gym Management System
</div>

</body>
</html>