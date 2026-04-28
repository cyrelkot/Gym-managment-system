<?php 
session_start();
error_reporting(E_ALL);
include 'include/config.php'; 

// SESSION CHECK
if (!isset($_SESSION['adminid']) || strlen($_SESSION['adminid']) == 0) {
    header('location:logout.php');
    exit();
}

$msg='';
$errormsg='';

// ADD PACKAGE
if(isset($_POST['submit'])){

$AddPackage=$_POST['addPackage'];
$categoryName=$_POST['category'];

// check category
$stmt = $dbh->prepare("SELECT id FROM tblcategory WHERE category_name=:catname");
$stmt->bindParam(':catname',$categoryName,PDO::PARAM_STR);
$stmt->execute();
$catResult = $stmt->fetch(PDO::FETCH_ASSOC);

if($catResult){
$categoryId = $catResult['id'];
}
else{
$ins = $dbh->prepare("INSERT INTO tblcategory(category_name) VALUES(:catname)");
$ins->bindParam(':catname',$categoryName,PDO::PARAM_STR);
$ins->execute();
$categoryId = $dbh->lastInsertId();
}

// insert package
$sql="INSERT INTO tblpackage(PackageName,cate_id) VALUES(:Package,:category)";
$query=$dbh->prepare($sql);

$query->bindParam(':Package',$AddPackage,PDO::PARAM_STR);
$query->bindParam(':category',$categoryId,PDO::PARAM_INT);

$query->execute();

if($dbh->lastInsertId()){
echo "<script>alert('Package Added Successfully');</script>";
echo "<script>window.location.href='add-package.php'</script>";
}
else{
$errormsg="Something went wrong";
}
}

// DELETE
if(isset($_GET['del'])){
$uid=intval($_GET['del']);

$sql="DELETE FROM tblpackage WHERE id=:id";
$query=$dbh->prepare($sql);
$query->bindParam(':id',$uid,PDO::PARAM_INT);
$query->execute();

echo "<script>alert('Record deleted');</script>";
echo "<script>window.location.href='add-package.php'</script>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<title>Admin | Add Package</title>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="css/main.css">

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

<style>

.app-sidebar{
position:fixed;
z-index:1000;
}

.app-content,
body {
background: linear-gradient(rgba(0,0,0,0.88), rgba(0,0,0,0.96)),
            url('https://images.unsplash.com/photo-1554284126-aa88f22d8b74');
background-size: cover;
background-position: center;
color:#fff;
}

.app-content{
margin-left:230px;
}

.tile{
position:relative;
z-index:1;
background: rgba(0, 0, 0, 0.80);
border: 1px solid rgba(255, 102, 0, 0.45);
border-radius: 14px;
box-shadow: 0 0 22px rgba(0, 0, 0, 0.35);
color:#fff;
}

h3,
label,
.tile-title,
.table,
.table th,
.table td{
color:#fff !important;
}

hr{
border-top:1px solid rgba(255, 102, 0, 0.35);
}

.form-control{
background:#1a1a1a;
border:1px solid #333;
color:#fff;
}

.form-control:focus{
background:#1a1a1a;
border-color:#ff6600;
box-shadow:0 0 5px rgba(255,102,0,0.35);
color:#fff;
}

.btn-primary{
background:#ff6600;
border-color:#ff6600;
}

.btn-primary:hover,
.btn-primary:focus{
background:#e65c00;
border-color:#e65c00;
}

.btn-danger{
background:#c0392b;
border-color:#c0392b;
}

.table-bordered,
.table-bordered th,
.table-bordered td{
border:1px solid rgba(255,255,255,0.12) !important;
}

thead{
background:rgba(255, 102, 0, 0.12);
}

</style>

</head>

<body class="app sidebar-mini rtl">

<?php include 'include/header.php'; ?>
<?php include 'include/sidebar.php'; ?>

<main class="app-content">

<h3>Add Package</h3>
<hr>

<div class="row">

<div class="col-md-6">

<div class="tile">

<?php if($msg){ ?>
<div class="alert alert-success"><?php echo $msg; ?></div>
<?php } ?>

<?php if($errormsg){ ?>
<div class="alert alert-danger"><?php echo $errormsg; ?></div>
<?php } ?>

<form method="post">

<div class="form-group">
<label>Add Category</label>
<input class="form-control" type="text" name="category" required>
</div>

<div class="form-group">
<label>Add Package</label>
<input class="form-control" type="text" name="addPackage" required>
</div>

<button class="btn btn-primary" type="submit" name="submit">
Submit
</button>

</form>

</div>

</div>

</div>


<!-- TABLE -->

<div class="row">

<div class="col-md-12">

<div class="tile">

<table class="table table-bordered">

<thead>

<tr>
<th>#</th>
<th>Category</th>
<th>Package</th>
<th>Action</th>
</tr>

</thead>

<tbody>

<?php

$sql="SELECT tblpackage.id as pid,tblpackage.PackageName,tblcategory.category_name 
FROM tblpackage
JOIN tblcategory ON tblcategory.id=tblpackage.cate_id";

$query=$dbh->prepare($sql);
$query->execute();

$results=$query->fetchAll(PDO::FETCH_OBJ);

$cnt=1;

if($query->rowCount()>0){

foreach($results as $result){

?>

<tr>

<td><?php echo $cnt;?></td>

<td><?php echo htmlentities($result->category_name);?></td>

<td><?php echo htmlentities($result->PackageName);?></td>

<td>

<a href="add-package.php?del=<?php echo $result->pid;?>"
class="btn btn-danger"
onclick="return confirm('Delete this record?')">
Delete
</a>

</td>

</tr>

<?php 
$cnt++;
}} ?>

</tbody>

</table>

</div>

</div>

</div>

</main>


<script src="js/jquery-3.2.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/main.js"></script>

</body>

</html>