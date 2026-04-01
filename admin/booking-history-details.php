<?php
session_start();
error_reporting(0);
include('include/config.php');

if(strlen($_SESSION['adminid'])==0){
header('location:logout.php');
}else{

$bookindid=$_GET['bookingid'];

// Ensure the booking has a package set (for new/partial bookings)
$bookingPackageCheck = $dbh->prepare("SELECT package_id FROM tblbooking WHERE id = :bid");
$bookingPackageCheck->bindParam(':bid', $bookindid, PDO::PARAM_INT);
$bookingPackageCheck->execute();
$bookingPackageRow = $bookingPackageCheck->fetch(PDO::FETCH_OBJ);
if ($bookingPackageRow && empty($bookingPackageRow->package_id)) {
    $packageFallback = $dbh->prepare("SELECT id FROM tbladdpackage ORDER BY id LIMIT 1");
    $packageFallback->execute();
    $firstPackage = $packageFallback->fetch(PDO::FETCH_OBJ);
    if ($firstPackage) {
        $updatePackage = $dbh->prepare("UPDATE tblbooking SET package_id = :pid WHERE id = :bid");
        $updatePackage->bindParam(':pid', $firstPackage->id, PDO::PARAM_INT);
        $updatePackage->bindParam(':bid', $bookindid, PDO::PARAM_INT);
        $updatePackage->execute();
    }
}

if(isset($_POST['submit']))
{

$bookingiid=$_POST['bookingiid'];
$Paymenttype=$_POST['Paymenttype'];
$paymentAmount=$_POST['ParcialPayment'];

$paymentAmount=floatval($paymentAmount);

/* GET PACKAGE PRICE */

$sql=$dbh->prepare("SELECT t2.Price FROM tblbooking t1 
JOIN tbladdpackage t2 ON t1.package_id=t2.id 
WHERE t1.id=:bookingid");

$sql->bindParam(':bookingid',$bookingiid,PDO::PARAM_INT);
$sql->execute();
$row=$sql->fetch(PDO::FETCH_OBJ);

$price=$row->Price;

/* GET TOTAL PAID */

$sql2=$dbh->prepare("SELECT SUM(payment) as total FROM tblpayment WHERE bookingID=:bookingid");
$sql2->bindParam(':bookingid',$bookingiid,PDO::PARAM_INT);
$sql2->execute();
$row2=$sql2->fetch(PDO::FETCH_OBJ);

$totalpaid=$row2->total ? $row2->total : 0;

$remaining=$price-$totalpaid;

/* FULL PAYMENT */

if($Paymenttype=="Full Payment"){
$paymentAmount=$remaining;
}

/* PREVENT OVERPAY */

if($paymentAmount>$remaining){

echo "<script>alert('Payment exceeds remaining balance');</script>";

}else{

/* INSERT PAYMENT */

$sql3=$dbh->prepare("INSERT INTO tblpayment
(bookingID,paymentType,payment,payment_date)
VALUES
(:bookingID,:paymentType,:payment,NOW())");

$sql3->bindParam(':bookingID',$bookingiid,PDO::PARAM_INT);
$sql3->bindParam(':paymentType',$Paymenttype,PDO::PARAM_STR);
$sql3->bindParam(':payment',$paymentAmount);

$sql3->execute();

/* UPDATE BOOKING */

$newTotal=$totalpaid+$paymentAmount;

$sql4=$dbh->prepare("UPDATE tblbooking
SET paymentType=:ptype,payment=:payment
WHERE id=:bookingid");

$sql4->bindParam(':ptype',$Paymenttype,PDO::PARAM_STR);
$sql4->bindParam(':payment',$newTotal);
$sql4->bindParam(':bookingid',$bookingiid,PDO::PARAM_INT);

$sql4->execute();

echo "<script>alert('Payment Recorded');</script>";
echo "<script>window.location='booking-history-details.php?bookingid=".$bookingiid."'</script>";

}

}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="utf-8">
<title>Edit Booking</title>

<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" type="text/css" href="css/main.css">

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

</head>

<body class="app sidebar-mini rtl">

<?php include('include/header.php'); ?>
<?php include('include/sidebar.php'); ?>

<main class="app-content">

<div class="app-title">
<div>
<h1>Edit Booking</h1>
</div>
</div>

<div class="row">
<div class="col-md-12">

<div class="tile">

<div class="tile-body">

<?php

$sql="SELECT t1.id as bookingid,
        t1.userid as userId,
        t1.package_id as packageId,
        t1.booking_date,
        t3.fname,
        t3.email,
        t2.Price,
        COALESCE(t5.PackageName, t2.titlename) as PackageName,
        t1.paymentType

        FROM tblbooking t1

        LEFT JOIN tbladdpackage t2 ON t1.package_id=t2.id
        LEFT JOIN tbluser t3 ON t1.userid=t3.id
        LEFT JOIN tblpackage t5 ON t2.PackageType=t5.id

        WHERE t1.id=:bookindid";

$query=$dbh->prepare($sql);
$query->bindParam(':bookindid',$bookindid,PDO::PARAM_INT);
$query->execute();
$result=$query->fetch(PDO::FETCH_OBJ);

$ptype=$result->paymentType;

// Ensure booking date is set (default to today for new bookings)
$bookingDate = $result->booking_date;
if (empty($bookingDate) || $bookingDate === '0000-00-00') {
    $bookingDate = date('Y-m-d');
}

// Ensure user is set for new bookings (auto-assign first user if none)
$userId = $result->userId;
if (empty($userId)) {
    $userFallback = $dbh->prepare("SELECT id FROM tbluser ORDER BY id LIMIT 1");
    $userFallback->execute();
    $firstUser = $userFallback->fetch(PDO::FETCH_OBJ);
    if ($firstUser) {
        $userId = $firstUser->id;
        $updateUser = $dbh->prepare("UPDATE tblbooking SET userid=:uid WHERE id=:bid");
        $updateUser->bindParam(':uid', $userId, PDO::PARAM_INT);
        $updateUser->bindParam(':bid', $bookindid, PDO::PARAM_INT);
        $updateUser->execute();
    }
}

// If user info is still missing, load it by userId
if (empty($result->fname) && !empty($userId)) {
    $userStmt = $dbh->prepare("SELECT fname, email FROM tbluser WHERE id = :uid");
    $userStmt->bindParam(':uid', $userId, PDO::PARAM_INT);
    $userStmt->execute();
    $userInfo = $userStmt->fetch(PDO::FETCH_OBJ);
    if ($userInfo) {
        $result->fname = $userInfo->fname;
        $result->email = $userInfo->email;
    }
}
// Ensure package is set for new bookings (auto-assign first package if none)
$packageId = $result->packageId;
if (empty($packageId)) {
    $packageFallback = $dbh->prepare("SELECT id FROM tbladdpackage ORDER BY id LIMIT 1");
    $packageFallback->execute();
    $firstPackage = $packageFallback->fetch(PDO::FETCH_OBJ);
    if ($firstPackage) {
        $packageId = $firstPackage->id;
        $updatePackage = $dbh->prepare("UPDATE tblbooking SET package_id=:pid WHERE id=:bid");
        $updatePackage->bindParam(':pid', $packageId, PDO::PARAM_INT);
        $updatePackage->bindParam(':bid', $bookindid, PDO::PARAM_INT);
        $updatePackage->execute();
    }
}

// If package info is missing, load it by packageId
if (empty($result->PackageName) && !empty($packageId)) {
    $packageStmt = $dbh->prepare("SELECT t5.PackageName FROM tbladdpackage t2
        LEFT JOIN tblpackage t5 ON t2.PackageType=t5.id
        WHERE t2.id = :pid");
    $packageStmt->bindParam(':pid', $packageId, PDO::PARAM_INT);
    $packageStmt->execute();
    $packageInfo = $packageStmt->fetch(PDO::FETCH_OBJ);
    if ($packageInfo) {
        $result->PackageName = $packageInfo->PackageName;
    }
}
/* TOTAL PAYMENT */

$sql2=$dbh->prepare("SELECT SUM(payment) as total FROM tblpayment WHERE bookingID=:bookindid");
$sql2->bindParam(':bookindid',$bookindid,PDO::PARAM_INT);
$sql2->execute();
$row2=$sql2->fetch(PDO::FETCH_OBJ);

$gpayment=$row2->total ? $row2->total : 0;

/* LAST PAYMENT */

$sql3=$dbh->prepare("SELECT payment FROM tblpayment 
WHERE bookingID=:bookindid 
ORDER BY payment_date DESC LIMIT 1");

$sql3->bindParam(':bookindid',$bookindid,PDO::PARAM_INT);
$sql3->execute();

$row3=$sql3->fetch(PDO::FETCH_OBJ);

$lastpayment=$row3 ? $row3->payment : '';

$remaining=$result->Price-$gpayment;

?>

<form method="post">

<input type="hidden" name="bookingiid" value="<?php echo $bookindid;?>">

<table class="table table-bordered">

<tr>
<th>Booking Date</th>
<td><input class="form-control" value="<?php echo $bookingDate; ?>" readonly></td>
</tr>

<tr>
<th>User</th>
<td><input class="form-control" value="<?php echo $result->fname;?> (<?php echo $result->email;?>)" readonly></td>
</tr>

<tr>
<th>Package</th>
<td><input class="form-control" value="<?php echo $result->PackageName;?>" readonly></td>
</tr>

<tr>

<th>Payment Type</th>

<td>

<select name="Paymenttype" id="Payment" class="form-control">

<option value="">--Select--</option>

<option value="Partial Payment" <?php if($ptype=="Partial Payment") echo "selected"; ?>>Partial Payment</option>

<option value="Full Payment" <?php if($ptype=="Full Payment") echo "selected"; ?>>Full Payment</option>

</select>

</td>

</tr>

<tr>

<th>Payment Amount</th>

<td>

<input type="number"
name="ParcialPayment"
id="ParcialPayment"
class="form-control"
value="<?php echo $lastpayment;?>">

</td>

</tr>

<tr>
<th>Remaining Balance</th>
<td><input class="form-control" value="<?php echo $remaining;?>" readonly></td>
</tr>

<tr>

<td colspan="2">

<button type="submit" name="submit" class="btn btn-success">
Submit Payment
</button>

<a href="booking-history.php" class="btn btn-secondary">
Back
</a>

</td>

</tr>

</table>

</form>

<?php

/* PAYMENT HISTORY */

$sql="SELECT * FROM tblpayment WHERE bookingID=:bookindid";

$query=$dbh->prepare($sql);
$query->bindParam(':bookindid',$bookindid,PDO::PARAM_INT);
$query->execute();

$results=$query->fetchAll(PDO::FETCH_OBJ);

if($query->rowCount()>0){

?>

<table class="table table-bordered">

<tr>
<th colspan="3" style="text-align:center;font-size:18px;">
Payment History
</th>
</tr>

<tr>
<th>Payment Type</th>
<th>Amount</th>
<th>Date</th>
</tr>

<?php foreach($results as $row){ ?>

<tr>
<td><?php echo $row->paymentType;?></td>
<td><?php echo $row->payment;?></td>
<td><?php echo $row->payment_date;?></td>
</tr>

<?php } ?>

</table>

<?php } ?>

</div>
</div>
</div>
</div>

</main>

<script src="js/jquery-3.2.1.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/main.js"></script>

<script>

$(document).ready(function(){

var remaining=<?php echo $remaining;?>;

function checkPayment(){

var type=$("#Payment").val();

if(type=="Full Payment"){

$("#ParcialPayment").val(remaining);
$("#ParcialPayment").prop("readonly",true);

}else{

$("#ParcialPayment").prop("readonly",false);

}

}

$("#Payment").change(checkPayment);

checkPayment();

});

</script>

</body>
</html>

<?php } ?>