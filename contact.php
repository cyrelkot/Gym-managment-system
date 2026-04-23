<?php 
session_start();
error_reporting(0);
include 'include/config.php';
$uid=$_SESSION['uid'];

if(isset($_POST['submit']))
{ 
    $pid=$_POST['pid'];

    $sql="INSERT INTO tblbooking (package_id,userid) VALUES (:pid,:uid)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':pid',$pid,PDO::PARAM_STR);
    $query->bindParam(':uid',$uid,PDO::PARAM_STR);
    $query->execute();

    echo "<script>alert('Package has been booked.');</script>";
    echo "<script>window.location.href='booking-history.php'</script>";
}
?>

<!DOCTYPE html>
<html lang="zxx">
<head>
    <title>Gym Management System</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="css/bootstrap.min.css"/>
    <link rel="stylesheet" href="css/font-awesome.min.css"/>

    <!-- MODERN UI STYLE -->
    <style>
        body{
            margin:0;
            font-family: 'Segoe UI', sans-serif;
            background:#0b0f19;
            color:#fff;
        }

        /* HEADER */
        .header{
            display:flex;
            justify-content:space-between;
            align-items:center;
            padding:18px 40px;
            background:rgba(20,20,20,0.95);
            border-bottom:1px solid #1f2937;
            position:sticky;
            top:0;
            z-index:1000;
        }

        .logo{
            font-size:22px;
            font-weight:bold;
            color:#00ff99;
            letter-spacing:2px;
        }

        .nav a{
            color:#fff;
            text-decoration:none;
            margin-left:20px;
            font-weight:500;
            transition:0.3s;
        }

        .nav a:hover{
            color:#00ff99;
        }

        /* HERO */
        .hero{
            text-align:center;
            padding:90px 20px;
            background:linear-gradient(135deg,#0f172a,#111827);
        }

        .hero h2{
            font-size:42px;
            color:#00ff99;
            margin-bottom:10px;
        }

        .hero p{
            color:#cbd5e1;
        }

        /* CARD SECTION */
        .section{
            padding:60px 20px;
        }

        .card-box{
            background:#111827;
            max-width:650px;
            margin:auto;
            padding:35px;
            border-radius:15px;
            box-shadow:0 10px 30px rgba(0,0,0,0.4);
            border:1px solid #1f2937;
        }

        .card-box p{
            font-size:18px;
            margin:15px 0;
            color:#e5e7eb;
        }

        /* BUTTON STYLE */
        .btn-book{
            display:inline-block;
            padding:12px 25px;
            background:#00ff99;
            color:#000;
            border:none;
            border-radius:8px;
            font-weight:bold;
            transition:0.3s;
            cursor:pointer;
        }

        .btn-book:hover{
            background:#00cc77;
            transform:scale(1.05);
        }

        /* FOOTER */
        .footer{
            text-align:center;
            padding:20px;
            background:#0a0a0a;
            border-top:1px solid #1f2937;
            color:#888;
            margin-top:40px;
        }
    </style>

</head>

<body>

<!-- HEADER -->
<div class="header">

    <div class="logo">GYM MS</div>

    <div class="nav">
        <a href="index.php">Home</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>

        <?php if(strlen($_SESSION['uid'])==0): ?>
            <a href="login.php">Login</a>
        <?php else: ?>
            <a href="booking-history.php">My Bookings</a>
            <a href="logout.php">Logout</a>
        <?php endif; ?>
    </div>

</div>

<!-- HERO SECTION -->
<div class="hero">
    <h2>Contact Us</h2>
    <p>We are always ready to help you achieve your fitness goals</p>
</div>

<!-- CONTENT -->
<div class="section">

    <div class="card-box">

        <p><strong>Email:</strong> gymfitness@gmail.com</p>
        <p><strong>Contact No:</strong> 09925965016, 09931098049</p>
        <p><strong>Address:</strong> Test Address</p>

    </div>

</div>

<!-- FOOTER -->
<div class="footer">
    © 2026 Gym Management System | All Rights Reserved
</div>

</body>
</html>