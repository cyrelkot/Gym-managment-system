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

<style>

/* GLOBAL */
body{
    margin:0;
    font-family:Segoe UI;
    background:#000;
    color:#fff;
}

/* NAVBAR */
.navbar{
    position:relative;
    display:flex;
    align-items:center;
    padding:20px;
    background:#000;
    border-bottom:2px solid #ff6a00;
}

/* LOGO */
.logo{
    color:#ff6a00;
    font-size:22px;
    font-weight:bold;
}

/* CENTER MENU */
.nav-center{
    position:absolute;
    left:50%;
    transform:translateX(-50%);
}

.nav-center a{
    color:#fff;
    text-decoration:none;
    margin:0 15px;
    font-weight:500;
}

.nav-center a:hover{
    color:#ff6a00;
}

/* HERO */
.hero{
    height:300px;
    background:url('https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?auto=format&fit=crop&w=1600&q=80') center/cover no-repeat;
    display:flex;
    align-items:center;
    justify-content:center;
    text-align:center;
    position:relative;
}

.hero::before{
    content:"";
    position:absolute;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.7);
}

.hero-content{
    position:relative;
}

.hero h1{
    color:#ff6a00;
    font-size:40px;
}

/* CONTAINER */
.container{
    padding:30px;
}

/* CARD */
.card{
    background:#111;
    padding:20px;
    border-radius:12px;
    box-shadow:0 0 20px rgba(255,106,0,0.2);
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
}

th, td{
    padding:12px;
}

th{
    background:#1a1a1a;
    color:#ff6a00;
}

tr:nth-child(even){
    background:#0f0f0f;
}

/* BUTTON */
.btn-view{
    background:#ff6a00;
    border:none;
    padding:6px 12px;
    color:#000;
    border-radius:6px;
    cursor:pointer;
}

.btn-view:hover{
    background:#ff8c1a;
}

/* ALERT */
.alert{
    background:#222;
    padding:15px;
    border-left:4px solid #ff6a00;
    margin-bottom:20px;
}

</style>
</head>

<body>

<!-- NAVBAR -->
<div class="navbar">
    <div class="logo">GYM</div>

    <div class="nav-center">
        <a href="index.php">Home</a>
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
t2.PackageDuratiobn,
t2.Price,
t2.Description,
t4.category_name,
t5.PackageName
FROM tblbooking t1
LEFT JOIN tbladdpackage t2 ON t1.package_id = t2.id
LEFT JOIN tblcategory t4 ON t2.category = t4.id
LEFT JOIN tblpackage t5 ON t2.PackageType = t5.id
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
<th>Plan</th>
<th>Duration</th>
<th>Price</th>
<th>Category</th>
<th>Package</th>
<th>Action</th>
</tr>
</thead>

<tbody>

<?php foreach($results as $row){ ?>
<tr>
<td><?php echo $cnt++; ?></td>
<td><?php echo $row->booking_date; ?></td>
<td><?php echo $row->titlename; ?></td>
<td><?php echo $row->PackageDuratiobn; ?></td>
<td>₱<?php echo $row->Price; ?></td>
<td><?php echo $row->category_name; ?></td>
<td><?php echo $row->PackageName; ?></td>
<td>
<a href="booking-details.php?bookingid=<?php echo $row->bookingid; ?>">
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