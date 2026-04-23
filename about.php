<?php
session_start();
include 'include/config.php';

$user = null;
if (!empty($_SESSION['uid'])) {
    $uid = $_SESSION['uid'];

    // Get user info for logged-in users
    $sql = "SELECT * FROM tbluser WHERE id=:uid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':uid', $uid, PDO::PARAM_INT);
    $query->execute();
    $user = $query->fetch(PDO::FETCH_OBJ);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body{
            margin:0;
            font-family:Arial, sans-serif;
            background:#0b1220;
            color:#fff;
        }

        /* HEADER */
        .header{
            display:flex;
            justify-content:space-between;
            align-items:center;
            padding:15px 30px;
            background:#111827;
            border-bottom:1px solid #1f2937;
            position:sticky;
            top:0;
        }

        .logo{
            font-size:20px;
            font-weight:bold;
            color:#00ff99;
        }

        .menu a{
            color:#fff;
            text-decoration:none;
            margin-left:18px;
            transition:0.3s;
            font-weight:500;
        }

        .menu a:hover{
            color:#00ff99;
        }

        /* WELCOME */
        .welcome{
            padding:20px 30px;
            background:linear-gradient(90deg,#111827,#0f172a);
            border-bottom:1px solid #1f2937;
        }

        .welcome h2{
            margin:0;
            color:#00ff99;
        }

        .welcome p{
            margin:5px 0 0;
            color:#cbd5e1;
        }

        /* LAYOUT */
        .container{
            padding:30px;
        }

        .grid{
            display:grid;
            grid-template-columns:1fr 2fr;
            gap:20px;
        }

        /* CARDS */
        .card{
            background:#111827;
            padding:25px;
            border-radius:15px;
            box-shadow:0 10px 25px rgba(0,0,0,0.4);
            border:1px solid #1f2937;
        }

        .card h3{
            margin-top:0;
            color:#00ff99;
        }

        .profile p{
            margin:10px 0;
            color:#cbd5e1;
        }

        .btn{
            display:inline-block;
            padding:10px 18px;
            margin-top:10px;
            border:none;
            border-radius:8px;
            cursor:pointer;
            font-weight:bold;
            text-decoration:none;
            transition:0.3s;
        }

        .btn-dark{
            background:#1f2937;
            color:#fff;
        }

        .btn-dark:hover{
            background:#374151;
        }

        @media(max-width:768px){
            .grid{
                grid-template-columns:1fr;
            }
        }
    </style>
</head>

<body>

<!-- HEADER -->
<div class="header">
    <div class="logo">GYM MS</div>

    <div class="menu">
        <a href="index.php">Home</a>
        <a href="about.php">About Gym</a>
        <a href="contact.php">Contact</a>
        <?php if (!empty($user)) { ?>
            <a href="booking-history.php">My Bookings</a>
            <a href="logout.php">Logout</a>
        <?php } else { ?>
            <a href="login.php">Login</a>
        <?php } ?>
    </div>
</div>

<!-- WELCOME -->
<div class="welcome">
    <?php if (!empty($user)) { ?>
        <h2>Welcome, <?php echo htmlentities($user->fname); ?></h2>
        <p>Track your fitness journey and bookings here</p>
    <?php } else { ?>
        <h2>About Our Gym</h2>
        <p>Learn more about our fitness programs and services</p>
    <?php } ?>
</div>

<!-- CONTENT -->
<div class="container">

    <div class="grid">

        <div class="card profile">
            <?php if (!empty($user)) { ?>
                <h3>My Profile</h3>
                <p><b>Name:</b> <?php echo htmlentities($user->fname . " " . $user->lname); ?></p>
                <p><b>Email:</b> <?php echo htmlentities($user->email); ?></p>
                <p><b>Status:</b>
                    <?php echo ($user->status==1) ? "Approved" : "Pending"; ?>
                </p>
                <a href="logout.php" class="btn btn-dark">Logout</a>
            <?php } else { ?>
                <h3>About Gym Management System</h3>
                <p>We help members manage workouts, classes, and bookings in one place.</p>
                <p>Sign in to view your profile, booking history, and account status.</p>
            <?php } ?>
        </div>

    </div>

</div>

</body>
</html>