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

    <style>
        body{
            margin:0;
            font-family:'Segoe UI', sans-serif;
            background:#000;
            color:#fff;
        }

        .navbar{
            display:grid;
            grid-template-columns: 1fr auto 1fr;
            align-items:center;
            padding:18px 50px;
            background:#000;
            border-bottom:2px solid #ff6a00;
            position:sticky;
            top:0;
            z-index:1000;
        }

        .logo{
            font-size:24px;
            font-weight:900;
            color:#ff6a00;
        }

        .nav-links{
            display:flex;
            justify-content:center;
            gap:25px;
        }

        .nav-links a{
            color:#fff;
            text-decoration:none;
            font-weight:500;
        }

        .nav-links a:hover{
            color:#ff6a00;
        }

        .hero{
            height:50vh;
            display:flex;
            flex-direction:column;
            justify-content:center;
            align-items:center;
            text-align:center;
            background:url('https://images.unsplash.com/photo-1554284126-aa88f22d8b74?auto=format&fit=crop&w=1600&q=80') center/cover;
            position:relative;
        }

        .hero::before{
            content:"";
            position:absolute;
            width:100%;
            height:100%;
            background:rgba(0,0,0,0.7);
        }

        .hero h1, .hero p{
            position:relative;
            z-index:1;
        }

        .hero h1{
            font-size:52px;
            color:#ff6a00;
        }

        .section{
            padding:60px 20px;
            display:flex;
            justify-content:center;
        }

        .card{
            width:100%;
            max-width:650px;
            background:#111;
            padding:35px;
            border-top:4px solid #ff6a00;
            border-radius:16px;
        }

        .card h3{
            text-align:center;
            color:#ff6a00;
        }

        .info{
            padding:14px 0;
            border-bottom:1px solid #222;
        }

        .info strong{
            color:#ff6a00;
        }

        .footer{
            text-align:center;
            padding:20px;
            color:#777;
        }

        @media(max-width:768px){
            .navbar{
                grid-template-columns:1fr;
                text-align:center;
            }

            .nav-links{
                flex-wrap:wrap;
            }
        }
    </style>
</head>

<body>

<!-- NAVBAR -->
<div class="navbar">

    <div class="logo">GYM</div>

    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>

        <!-- 🔥 ADMIN / LOGOUT SWITCH -->
        <?php if (isset($_SESSION['uid'])) { ?>
            <a href="logout.php">Logout</a>
        <?php } else { ?>
            <a href="admin/login.php">Admin</a>
        <?php } ?>

    </div>

    <div></div>

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