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

/* DELETE */
if (isset($_POST['delete_booking']) && isset($_POST['bookingid'])) {
    $bookingId = intval($_POST['bookingid']);

    $dbh->prepare("DELETE FROM tblpayment WHERE bookingID = :id")
        ->execute([':id'=>$bookingId]);

    $dbh->prepare("DELETE FROM tblbooking WHERE id = :id")
        ->execute([':id'=>$bookingId]);

    header('Location: booking-history.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta name="description" content="">
<title>Admin | Booking History</title>
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

<h3>All Bookings</h3>

<hr />

<table class="table table-hover table-bordered" id="sampleTable">

<thead>
<tr>
<th>#</th>
<th>ID</th>
<th>Name</th>
<th>Email</th>
<th>Date</th>
<th>Plan</th>
<th>Status</th>
<th>Action</th>
</tr>
</thead>

<tbody>

<?php
$sql="SELECT t1.id as bookingid,
t3.fname as Name,
t3.email,
t1.booking_date,
t2.titlename as Plan,
t1.paymentType
FROM tblbooking t1
LEFT JOIN tbladdpackage t2 ON t1.package_id = t2.id
LEFT JOIN tbluser t3 ON t1.userid = t3.id";

$query= $dbh->prepare($sql);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);

$cnt=1;
foreach($results as $row){
?>

<tr>
<td><?php echo $cnt++; ?></td>
<td><?php echo (int)$row->bookingid; ?></td>
<td><?php echo htmlspecialchars($row->Name, ENT_QUOTES, 'UTF-8'); ?></td>
<td><?php echo htmlspecialchars($row->email, ENT_QUOTES, 'UTF-8'); ?></td>
<td><?php echo htmlspecialchars($row->booking_date, ENT_QUOTES, 'UTF-8'); ?></td>
<td><?php echo htmlspecialchars($row->Plan, ENT_QUOTES, 'UTF-8'); ?></td>

<td>
<?php
if($row->paymentType == "Full Payment"){
    echo "<span class='badge badge-full'>Full</span>";
}else if($row->paymentType == "Partial Payment"){
    echo "<span class='badge badge-partial'>Partial</span>";
}else{
    echo "<span class='badge badge-none'>Pending</span>";
}
?>
</td>

<td class="action-cell">
<a href="edit-booking.php?bookingid=<?php echo (int)$row->bookingid; ?>" class="btn btn-success btn-sm">Edit</a>

<form method="post" style="display:inline;">
<?php echo csrf_field(); ?>
<input type="hidden" name="bookingid" value="<?php echo (int)$row->bookingid;?>">
<button type="submit" name="delete_booking" class="btn btn-danger btn-sm"
onclick="return confirm('Are you sure you want to delete this booking?');">Delete</button>
</form>
</td>

</tr>

<?php } ?>

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
