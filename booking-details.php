<?php 
session_start();
error_reporting(0);
require_once('include/config.php');

if(strlen($_SESSION["uid"])==0){   
    header('location:login.php');
    exit;
}

$uid=$_SESSION['uid'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_verify()) {
    die('Invalid request. Please go back and try again.');
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Booking Details</title>
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

.nav-center a:hover{
    color:#ff6a00;
}

/* HERO IMAGE */
.hero{
    height:280px;
    background:url('https://images.unsplash.com/photo-1554284126-aa88f22d8b74?auto=format&fit=crop&w=1600&q=80') center/cover no-repeat;
    display:flex;
    align-items:center;
    justify-content:center;
    position:relative;
}

.hero::before{
    content:"";
    position:absolute;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.7);
}

.hero h1{
    position:relative;
    color:#ff6a00;
}

/* CONTAINER */
.container{
    padding:30px;
}

/* CARD */
.card{
    background:#111;
    padding:25px;
    border-radius:12px;
    box-shadow:0 0 20px rgba(255,106,0,0.2);
    margin-bottom:20px;
}

/* GRID */
.grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:15px;
}

.label{
    color:#aaa;
    font-size:13px;
}

.value{
    font-weight:bold;
}

/* BUTTON */
.btn{
    background:#ff6a00;
    border:none;
    padding:8px 15px;
    border-radius:6px;
    color:#000;
    cursor:pointer;
}

.btn:hover{
    background:#ff8c1a;
}

/* SELECT */
select{
    padding:6px;
    border-radius:5px;
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
}

th,td{
    padding:10px;
}

th{
    background:#1a1a1a;
    color:#ff6a00;
}

tr:nth-child(even){
    background:#0f0f0f;
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
        <a href="booking-history.php">Booking History</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<!-- HERO -->
<div class="hero">
    <h1>Booking Details</h1>
</div>

<div class="container">

<?php
$bookingid=$_GET['bookingid'];

$sql="SELECT t1.*, t2.titlename,t2.PackageDuration,t2.Price,t2.Description,
t4.category_name,t3.fname,t3.email
FROM tblbooking t1
LEFT JOIN tbladdpackage t2 ON t1.package_id=t2.id
LEFT JOIN tbluser t3 ON t1.userid=t3.id
LEFT JOIN tblcategory t4 ON t2.category=t4.id
WHERE t1.id=:id AND t1.userid=:uid";

$query=$dbh->prepare($sql);
$query->bindParam(':id',$bookingid);
$query->bindParam(':uid',$uid,PDO::PARAM_INT);
$query->execute();
$row=$query->fetch(PDO::FETCH_OBJ);

if (!$row) {
    header('location:booking-history.php');
    exit;
}
?>

<!-- BOOKING CARD -->
<div class="card">
<h3>Booking Info</h3>

<div class="grid">
<div><span class="label">Name</span><br><span class="value"><?php echo htmlspecialchars($row->fname, ENT_QUOTES, 'UTF-8');?></span></div>
<div><span class="label">Email</span><br><span class="value"><?php echo htmlspecialchars($row->email, ENT_QUOTES, 'UTF-8');?></span></div>
<div><span class="label">Date</span><br><span class="value"><?php echo htmlspecialchars($row->booking_date, ENT_QUOTES, 'UTF-8');?></span></div>
<div><span class="label">Category</span><br><span class="value"><?php echo htmlspecialchars($row->category_name, ENT_QUOTES, 'UTF-8');?></span></div>
<div><span class="label">Title</span><br><span class="value"><?php echo htmlspecialchars($row->titlename, ENT_QUOTES, 'UTF-8');?></span></div>
<div><span class="label">Duration</span><br><span class="value"><?php echo htmlspecialchars($row->PackageDuration, ENT_QUOTES, 'UTF-8');?></span></div>
<div><span class="label">Price</span><br><span class="value">₱<?php echo htmlspecialchars($row->Price, ENT_QUOTES, 'UTF-8');?></span></div>
</div>

<br>

<div>
<span class="label">Description</span><br>
<?php echo htmlspecialchars($row->Description, ENT_QUOTES, 'UTF-8');?>
</div>

<br>


</div>

<!-- PAYMENT HISTORY -->
<div class="card">
<h3>Payment History</h3>

<table>
<tr>
<th>Type</th>
<th>Amount</th>
<th>Total Amount</th>
<th>Remaining Balance</th>
<th>Updated Date</th>
</tr>

<?php
$sql="SELECT * FROM tblpayment WHERE bookingID=:id ORDER BY payment_date ASC, id ASC";
$query=$dbh->prepare($sql);
$query->bindParam(':id',$bookingid);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);

$packageTotal = isset($row->Price) ? (float) $row->Price : 0;
$cumulativePaid = 0;

foreach($results as $pay){
$cumulativePaid += (float) $pay->payment;
$remainingAfter = $packageTotal - $cumulativePaid;
if ($remainingAfter < 0) {
    $remainingAfter = 0;
}
?>

<tr>
<td><?php echo htmlentities($pay->paymentType);?></td>
<td><?php echo number_format((float) $pay->payment, 2, '.', '');?></td>
<td><?php echo number_format($packageTotal, 2, '.', '');?></td>
<td><?php echo number_format($remainingAfter, 2, '.', '');?></td>
<td><?php echo htmlentities($pay->payment_date);?></td>
</tr>

<?php } ?>

<tr>
<th>Total Paid</th>
<th colspan="4"><?php echo number_format($cumulativePaid, 2, '.', '');?></th>
</tr>

</table>

</div>

</div>

</body>
</html>