<?php 
session_start();
error_reporting(0);
require_once('include/config.php');

if(strlen($_SESSION["uid"])==0){   
    header('location:login.php');
    exit;
}

$uid=$_SESSION['uid'];

if (isset($_POST['update_payment_type']) && isset($_POST['paymentType'])) {
    $newType = trim($_POST['paymentType']);
    if (in_array($newType, ['Partial Payment', 'Full Payment'], true)) {
        $upd = $dbh->prepare("UPDATE tblbooking SET paymentType = :paymentType WHERE id = :bookingid");
        $upd->bindParam(':paymentType', $newType, PDO::PARAM_STR);
        $upd->bindParam(':bookingid', $_POST['bookingid'], PDO::PARAM_INT);
        $upd->execute();
    }
    header('Location: booking-details.php?bookingid=' . urlencode($_POST['bookingid']));
    exit;
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
$bookindid=$_GET['bookingid'];

$sql="SELECT t1.*, t2.titlename,t2.PackageDuratiobn,t2.Price,t2.Description,
t4.category_name,t5.PackageName,t3.fname,t3.email
FROM tblbooking t1
LEFT JOIN tbladdpackage t2 ON t1.package_id=t2.id
LEFT JOIN tbluser t3 ON t1.userid=t3.id
LEFT JOIN tblcategory t4 ON t2.category=t4.id
LEFT JOIN tblpackage t5 ON t2.PackageType=t5.id
WHERE t1.id=:id";

$query=$dbh->prepare($sql);
$query->bindParam(':id',$bookindid);
$query->execute();
$row=$query->fetch(PDO::FETCH_OBJ);
?>

<!-- BOOKING CARD -->
<div class="card">
<h3>Booking Info</h3>

<div class="grid">
<div><span class="label">Name</span><br><span class="value"><?php echo $row->fname;?></span></div>
<div><span class="label">Email</span><br><span class="value"><?php echo $row->email;?></span></div>
<div><span class="label">Date</span><br><span class="value"><?php echo $row->booking_date;?></span></div>
<div><span class="label">Category</span><br><span class="value"><?php echo $row->category_name;?></span></div>
<div><span class="label">Plan</span><br><span class="value"><?php echo $row->titlename;?></span></div>
<div><span class="label">Package</span><br><span class="value"><?php echo $row->PackageName;?></span></div>
<div><span class="label">Duration</span><br><span class="value"><?php echo $row->PackageDuratiobn;?></span></div>
<div><span class="label">Price</span><br><span class="value">₱<?php echo $row->Price;?></span></div>
</div>

<br>

<div>
<span class="label">Description</span><br>
<?php echo $row->Description;?>
</div>

<br>

<!-- PAYMENT UPDATE -->
<form method="post">
<input type="hidden" name="bookingid" value="<?php echo $bookindid;?>">

<select name="paymentType">
<option value="Partial Payment">Partial Payment</option>
<option value="Full Payment">Full Payment</option>
</select>

<button class="btn" name="update_payment_type">Update</button>
</form>

</div>

<!-- PAYMENT HISTORY -->
<div class="card">
<h3>Payment History</h3>

<table>
<tr>
<th>Type</th>
<th>Amount</th>
<th>Date</th>
</tr>

<?php
$sql="SELECT * FROM tblpayment WHERE bookingID=:id";
$query=$dbh->prepare($sql);
$query->bindParam(':id',$bookindid);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);

$total=0;

foreach($results as $pay){
$total += $pay->payment;
?>

<tr>
<td><?php echo $pay->paymentType;?></td>
<td><?php echo $pay->payment;?></td>
<td><?php echo $pay->payment_date;?></td>
</tr>

<?php } ?>

<tr>
<th>Total</th>
<th><?php echo $total;?></th>
<th></th>
</tr>

</table>

</div>

</div>

</body>
</html>