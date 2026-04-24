<?php
session_start();
error_reporting(0);
include 'include/config.php';

if (!isset($_SESSION['adminid']) || strlen($_SESSION['adminid']) == 0) {
    header('location:logout.php');
    exit;
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
<meta charset="UTF-8">
<title>Gym Admin | Bookings</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>

/* GLOBAL */
body{
    margin:0;
    font-family:Segoe UI;
    background:#000;
    color:#fff;
}

/* HEADER */
.header{
    display:flex;
    justify-content:space-between;
    padding:15px 30px;
    background:#0d0d0d;
    border-bottom:2px solid #ff6a00;
}

.header h2{ color:#ff6a00; }
.header a{
    color:#fff;
    margin-left:20px;
    text-decoration:none;
}

/* CONTAINER */
.container{ padding:30px; }

/* CARD */
.card{
    background:#111;
    padding:20px;
    border-radius:10px;
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

/* BUTTONS */
.btn-view{
    background:#00bfff;
    border:none;
    padding:6px 10px;
    color:#fff;
    border-radius:5px;
    cursor:pointer;
}

.btn-delete{
    background:#ff4d4d;
    border:none;
    padding:6px 10px;
    color:#fff;
    border-radius:5px;
    cursor:pointer;
}

/* BADGE */
.badge{
    padding:5px 10px;
    border-radius:5px;
    font-size:12px;
}
.full{ background:#00cc66; }
.partial{ background:#ffaa00; }
.none{ background:#555; }

/* MODAL */
.modal{
    display:none;
    position:fixed;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.8);
}

.modal-content{
    background:#111;
    padding:20px;
    width:350px;
    margin:10% auto;
    border-radius:10px;
}

.close{
    float:right;
    cursor:pointer;
    color:#ff6a00;
    font-size:22px;
}

</style>
</head>

<body>

<div class="header">
    <h2>💪 Gym Admin</h2>
    <div>
        <a href="index.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">
<div class="card">

<h3>All Bookings</h3>

<table>
<thead>
<tr>
<th>#</th>
<th>ID</th>
<th>Name</th>
<th>Email</th>
<th>Date</th>
<th>Package</th>
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
COALESCE(t5.PackageName, t2.titlename) as PackageName,
t1.paymentType
FROM tblbooking t1
LEFT JOIN tbladdpackage t2 ON t1.package_id = t2.id
LEFT JOIN tbluser t3 ON t1.userid = t3.id
LEFT JOIN tblpackage t5 ON t2.PackageType = t5.id";

$query= $dbh->prepare($sql);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);

$cnt=1;
foreach($results as $row){
?>

<tr>
<td><?php echo $cnt++; ?></td>
<td><?php echo $row->bookingid; ?></td>
<td><?php echo $row->Name; ?></td>
<td><?php echo $row->email; ?></td>
<td><?php echo $row->booking_date; ?></td>
<td><?php echo $row->PackageName; ?></td>

<td>
<?php 
if($row->paymentType == "Full Payment"){
    echo "<span class='badge full'>Full</span>";
}else if($row->paymentType == "Partial Payment"){
    echo "<span class='badge partial'>Partial</span>";
}else{
    echo "<span class='badge none'>Pending</span>";
}
?>
</td>

<td>
<button class="btn-view"
onclick="openModal(
'<?php echo $row->bookingid;?>',
'<?php echo $row->Name;?>',
'<?php echo $row->email;?>',
'<?php echo $row->booking_date;?>',
'<?php echo $row->PackageName;?>',
'<?php echo $row->paymentType;?>'
)">View</button>

<form method="post" style="display:inline;">
<input type="hidden" name="bookingid" value="<?php echo $row->bookingid;?>">
<button type="submit" name="delete_booking" class="btn-delete">Delete</button>
</form>
</td>

</tr>

<?php } ?>

</tbody>
</table>

</div>
</div>

<!-- MODAL -->
<div id="modal" class="modal">
<div class="modal-content">
<span class="close" onclick="closeModal()">&times;</span>

<h3>Booking Details</h3>

<p><b>ID:</b> <span id="m_id"></span></p>
<p><b>Name:</b> <span id="m_name"></span></p>
<p><b>Email:</b> <span id="m_email"></span></p>
<p><b>Date:</b> <span id="m_date"></span></p>
<p><b>Package:</b> <span id="m_package"></span></p>
<p><b>Payment:</b> <span id="m_payment"></span></p>

</div>
</div>

<script>
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