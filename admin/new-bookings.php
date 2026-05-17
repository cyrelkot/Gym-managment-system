<?php
session_start();
error_reporting(0);
include 'include/config.php';

if (!isset($_SESSION['adminid']) || strlen($_SESSION['adminid']) == 0) {
    header('location:logout.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_verify()) {
    die('Invalid request. Please go back and try again.');
}

// DELETE BOOKING
if (isset($_POST['delete_booking']) && isset($_POST['bookingid'])) {

    $bookingId = intval($_POST['bookingid']);

    if ($bookingId > 0) {

        // Delete payment first
        $sql1 = "DELETE FROM tblpayment WHERE bookingID=:id";
        $query1 = $dbh->prepare($sql1);
        $query1->bindParam(':id', $bookingId, PDO::PARAM_INT);
        $query1->execute();

        // Delete booking
        $sql2 = "DELETE FROM tblbooking WHERE id=:id";
        $query2 = $dbh->prepare($sql2);
        $query2->bindParam(':id', $bookingId, PDO::PARAM_INT);
        $query2->execute();

        echo "<script>alert('Booking deleted successfully');</script>";
        echo "<script>window.location.href='new-bookings.php'</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta name="description" content="Vali is a">
<title>Admin | New Bookings</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" type="text/css" href="css/main.css">

<link rel="stylesheet" type="text/css"
href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

</head>

<body class="app sidebar-mini rtl">

<?php include 'include/header.php'; ?>

<div class="app-sidebar__overlay" data-toggle="sidebar"></div>

<?php include 'include/sidebar.php'; ?>

<main class="app-content">

<div class="row">
<div class="col-md-12">

<div class="tile">

<div class="tile-body">

<h3>New Bookings</h3>

<hr />

<table class="table table-hover table-bordered" id="sampleTable">

<thead>
<tr>
<th>Sr.No</th>
<th>Booking ID</th>
<th>Name</th>
<th>Email</th>
<th>Booking Date</th>
<th>Plan</th>
<th>Payment Type</th>
<th>Action</th>
</tr>
</thead>

<tbody>

<?php

$sql = "SELECT 
t1.id as bookingid,
t3.fname as Name,
t3.email as email,
t1.booking_date as bookingdate,
t1.paymentType as status,
t2.titlename as title,
t2.PackageDuration as PackageDuration,
t2.Price as Price,
t2.Description as Description,
t4.category_name as category_name,
t2.titlename as Plan

FROM tblbooking as t1

LEFT JOIN tbladdpackage as t2
ON t1.package_id = t2.id

LEFT JOIN tbluser as t3
ON t1.userid=t3.id

LEFT JOIN tblcategory as t4
ON t2.category=t4.id

WHERE t1.paymentType IS NULL OR t1.paymentType=''";

$query = $dbh->prepare($sql);
$query->execute();

$results = $query->fetchAll(PDO::FETCH_OBJ);

$cnt = 1;

if ($query->rowCount() > 0) {

foreach ($results as $result) {

?>

<tr>

<td><?php echo htmlentities($cnt); ?></td>

<td><?php echo htmlentities($result->bookingid); ?></td>

<td><?php echo htmlentities($result->Name); ?></td>

<td><?php echo htmlentities($result->email); ?></td>

<td><?php echo htmlentities($result->bookingdate); ?></td>

<td><?php echo htmlentities($result->Plan); ?></td>

<td>
<?php echo htmlentities($result->status ? $result->status : 'Not Paid'); ?>
</td>

<td class="action-cell">

<a href="booking-history-details.php?bookingid=<?php echo htmlentities($result->bookingid); ?>">

<button class="btn btn-primary btn-sm" type="button">
Edit
</button>

</a>

<form method="POST">
<?php echo csrf_field(); ?>
<input type="hidden"
name="bookingid"
value="<?php echo htmlentities($result->bookingid); ?>">

<button type="submit"
name="delete_booking"
class="btn btn-danger btn-sm"
onclick="return confirm('Are you sure you want to delete this booking?');">

Delete

</button>

</form>

</td>

</tr>

<?php
$cnt++;
}
}
?>

</tbody>
</table>

</div>
</div>
</div>
</div>
</main>

<script src="js/jquery-3.2.1.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/main.js"></script>

<script src="js/plugins/pace.min.js"></script>

<script type="text/javascript"
src="js/plugins/jquery.dataTables.min.js"></script>

<script type="text/javascript"
src="js/plugins/dataTables.bootstrap.min.js"></script>

<script type="text/javascript">
$('#sampleTable').DataTable();
</script>

</body>
</html>