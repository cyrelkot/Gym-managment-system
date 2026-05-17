<?php 
session_start();
error_reporting(0);
require_once('include/config.php');

if(strlen($_SESSION["uid"])==0){   
    header('location:login.php');
    exit;
}

$uid=$_SESSION['uid'];

/* CHECK APPROVAL */
$approved = false;
$check = $dbh->prepare("SELECT status FROM tbluser WHERE id=:uid");
$check->bindParam(':uid',$uid,PDO::PARAM_INT);
$check->execute();
$user = $check->fetch(PDO::FETCH_ASSOC);

if($user && intval($user['status']) === 1){
    $approved = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Booking History</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="css/user.css"/>
</head>

<body class="booking-history-page">

<!-- NAVBAR -->
<div class="navbar">
    <div class="logo">GYM</div>

    <div class="nav-center">
        <a href="index.php">Home</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
        <a href="booking-history.php">Booking History</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<!-- HERO -->
<div class="hero">
    <div class="hero-content">
        <h1>Booking History</h1>
        <p>Track your fitness journey</p>
    </div>
</div>

<div class="container">
<div class="card">

<?php if(!$approved){ ?>
    <div class="alert">
        Your account is pending admin approval.
    </div>
<?php } else { ?>

<?php
$sql="SELECT t1.id as bookingid,
t1.booking_date,
t2.titlename,
t2.PackageDuration,
t2.Price,
t2.Description,
t4.category_name
FROM tblbooking t1
LEFT JOIN tbladdpackage t2 ON t1.package_id = t2.id
LEFT JOIN tblcategory t4 ON t2.category = t4.id
WHERE t1.userid=:uid";

$query= $dbh->prepare($sql);
$query->bindParam(':uid',$uid,PDO::PARAM_INT);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
?>

<?php if(empty($results)){ ?>
    <div class="alert">
        No bookings yet.
    </div>
<?php } else { ?>

<table>
<thead>
<tr>
<th>#</th>
<th>Date</th>
<th>Title</th>
<th>Duration</th>
<th>Price</th>
<th>Category</th>
<th>Action</th>
</tr>
</thead>

<tbody>

<?php foreach($results as $row){ ?>
<tr>
<td><?php echo $cnt++; ?></td>
<td><?php echo htmlspecialchars($row->booking_date, ENT_QUOTES, 'UTF-8'); ?></td>
<td><?php echo htmlspecialchars($row->titlename, ENT_QUOTES, 'UTF-8'); ?></td>
<td><?php echo htmlspecialchars($row->PackageDuration, ENT_QUOTES, 'UTF-8'); ?></td>
<td>₱<?php echo htmlspecialchars($row->Price, ENT_QUOTES, 'UTF-8'); ?></td>
<td><?php echo htmlspecialchars($row->category_name, ENT_QUOTES, 'UTF-8'); ?></td>
<td>
<a href="booking-details.php?bookingid=<?php echo (int)$row->bookingid; ?>">
<button class="btn-view">View</button>
</a>
</td>
</tr>
<?php } ?>

</tbody>
</table>

<?php } } ?>

</div>
</div>

</body>
</html>