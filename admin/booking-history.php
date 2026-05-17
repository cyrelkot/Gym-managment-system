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

<style>
body,
.app-content {
    background: linear-gradient(rgba(0,0,0,0.88), rgba(0,0,0,0.96)),
    url('https://images.unsplash.com/photo-1554284126-aa88f22d8b74');
    background-size: cover;
    background-position: center;
    color: #fff;
}

.tile {
    background: rgba(0, 0, 0, 0.80);
    border: 1px solid rgba(255, 102, 0, 0.45);
    border-radius: 14px;
    box-shadow: 0 0 22px rgba(0, 0, 0, 0.35);
    color: #fff;
}

h3,
.table,
.table th,
.table td {
    color: #fff !important;
}

hr {
    border-top: 1px solid rgba(255, 102, 0, 0.35);
}

.table-bordered,
.table-bordered th,
.table-bordered td {
    border: 1px solid rgba(255,255,255,0.12) !important;
}

.table thead {
    background: rgba(255, 102, 0, 0.12);
}

.table-hover tbody tr:hover {
    background: rgba(255, 102, 0, 0.08);
}

.btn-primary {
    background: #ff6600;
    border-color: #ff6600;
}

.btn-primary:hover,
.btn-primary:focus {
    background: #e65c00;
    border-color: #e65c00;
}

.btn-danger {
    background: #c82333;
    border-color: #bd2130;
    color: #fff;
}

.btn-danger:hover,
.btn-danger:focus {
    background: #a71d2a;
    border-color: #a71d2a;
    color: #fff;
}

.action-cell form {
    display: inline-block;
}

.action-cell .btn {
    margin-right: 4px;
}

/* Badge colours */
.badge-full    { background: #00cc66; }
.badge-partial { background: #ffaa00; }
.badge-none    { background: #555; }

/* Modal */
.modal-overlay {
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.8);
    z-index: 9999;
}

.modal-box {
    background: #111;
    padding: 20px;
    width: 350px;
    margin: 10% auto;
    border-radius: 10px;
    color: #fff;
}

.modal-close {
    float: right;
    cursor: pointer;
    color: #ff6a00;
    font-size: 22px;
}
</style>
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
<button class="btn btn-primary btn-sm"
onclick="openModal(
<?php echo json_encode((string)$row->bookingid); ?>,
<?php echo json_encode($row->Name); ?>,
<?php echo json_encode($row->email); ?>,
<?php echo json_encode($row->booking_date); ?>,
<?php echo json_encode($row->Plan); ?>,
<?php echo json_encode($row->paymentType); ?>
)">View</button>

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

<!-- MODAL -->
<div id="modal" class="modal-overlay">
<div class="modal-box">
<span class="modal-close" onclick="closeModal()">&times;</span>

<h3>Booking Details</h3>

<p><b>ID:</b> <span id="m_id"></span></p>
<p><b>Name:</b> <span id="m_name"></span></p>
<p><b>Email:</b> <span id="m_email"></span></p>
<p><b>Date:</b> <span id="m_date"></span></p>
<p><b>Plan:</b> <span id="m_package"></span></p>
<p><b>Payment:</b> <span id="m_payment"></span></p>

</div>
</div>

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

function openModal(id,name,email,date,pack,payment){
    document.getElementById("m_id").innerText=id;
    document.getElementById("m_name").innerText=name;
    document.getElementById("m_email").innerText=email;
    document.getElementById("m_date").innerText=date;
    document.getElementById("m_package").innerText=pack;
    document.getElementById("m_payment").innerText=payment;

    document.getElementById("modal").style.display="block";
}

function closeModal(){
    document.getElementById("modal").style.display="none";
}

window.onclick=function(e){
    if(e.target==document.getElementById("modal")){
        closeModal();
    }
}
</script>

</body>
</html>
