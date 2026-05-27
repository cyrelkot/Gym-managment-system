<?php
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

<link rel="stylesheet" href="css/user.css"/>
<link rel="icon" type="image/png" href="icon-fonts/gym-logo.png">
</head>

<body class="booking-details-page">

<!-- NAVBAR -->
<div class="navbar">
    <div class="logo">GYM</div>
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

<?php
$sql="SELECT * FROM tblpayment WHERE bookingID=:id ORDER BY payment_date ASC, id ASC";
$query=$dbh->prepare($sql);
$query->bindParam(':id',$bookingid);
$query->execute();
$payments=$query->fetchAll(PDO::FETCH_OBJ);

$packageTotal = isset($row->Price) ? (float) $row->Price : 0;
$totalPaid = 0;
foreach($payments as $pay){ $totalPaid += (float) $pay->payment; }
$remaining = max(0, $packageTotal - $totalPaid);

if($totalPaid >= $packageTotal && $packageTotal > 0){
    $payStatus = '<span style="background:#28a745;color:#fff;padding:3px 10px;border-radius:12px;font-size:0.82em;font-weight:600;">Fully Paid</span>';
} elseif($totalPaid > 0){
    $payStatus = '<span style="background:#ff6600;color:#fff;padding:3px 10px;border-radius:12px;font-size:0.82em;font-weight:600;">Partially Paid</span>';
} else {
    $payStatus = '<span style="background:#6c757d;color:#fff;padding:3px 10px;border-radius:12px;font-size:0.82em;font-weight:600;">Not Paid</span>';
}
?>

<!-- PAYMENT SUMMARY -->
<div class="card">
<h3>Payment Summary</h3>
<div class="grid">
<div><span class="label">Package Price</span><br><span class="value">₱<?php echo number_format($packageTotal, 2, '.', ''); ?></span></div>
<div><span class="label">Total Paid</span><br><span class="value">₱<?php echo number_format($totalPaid, 2, '.', ''); ?></span></div>
<div><span class="label">Remaining Balance</span><br><span class="value">₱<?php echo number_format($remaining, 2, '.', ''); ?></span></div>
<div><span class="label">Payment Status</span><br><span class="value"><?php echo $payStatus; ?></span></div>
</div>
</div>

<!-- PAYMENT HISTORY -->
<div class="card">
<h3>Payment History</h3>

<table>
<tr>
<th>Type</th>
<th>Amount</th>
<th>Package Price</th>
<th>Remaining Balance</th>
<th>Updated Date</th>
</tr>

<?php
$cumulativePaid = 0;
foreach($payments as $pay){
$cumulativePaid += (float) $pay->payment;
$remainingAfter = max(0, $packageTotal - $cumulativePaid);
?>

<tr>
<td><?php echo htmlentities($pay->paymentType);?></td>
<td>₱<?php echo number_format((float) $pay->payment, 2, '.', '');?></td>
<td>₱<?php echo number_format($packageTotal, 2, '.', '');?></td>
<td>₱<?php echo number_format($remainingAfter, 2, '.', '');?></td>
<td><?php echo htmlentities($pay->payment_date);?></td>
</tr>

<?php } ?>

<tr>
<th>Total Paid</th>
<th colspan="4">₱<?php echo number_format($totalPaid, 2, '.', '');?></th>
</tr>

</table>

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