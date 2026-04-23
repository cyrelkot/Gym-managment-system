<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('include/config.php');

$error="";

if(isset($_POST['submit'])){

$fname=$_POST['fname'];
$lname=$_POST['lname'];
$email=$_POST['email'];
$mobile=$_POST['mobile'];
$city=$_POST['city'];
$password=$_POST['password'];
$repeat=$_POST['RepeatPassword'];

if($password != $repeat){
$error="Password does not match!";
}
else{

$status=0;
$hash=password_hash($password,PASSWORD_DEFAULT);

$sql="INSERT INTO tbluser
(fname,lname,email,mobile,city,password,status)
VALUES
(:fname,:lname,:email,:mobile,:city,:password,:status)";

$query=$dbh->prepare($sql);

$query->bindParam(':fname',$fname);
$query->bindParam(':lname',$lname);
$query->bindParam(':email',$email);
$query->bindParam(':mobile',$mobile);
$query->bindParam(':city',$city);
$query->bindParam(':password',$hash);
$query->bindParam(':status',$status);

$query->execute();

if($dbh->lastInsertId()){
echo "<script>alert('Registration Successful!');</script>";
echo "<script>window.location='login.php'</script>";
}

}
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Gym Registration</title>
<link rel="stylesheet" href="css/bootstrap.min.css">

<style>

body{
background:#0f172a;
color:white;
font-family:Segoe UI;
}

/* HEADER */
header{
background:#020617;
padding:15px;
}

.nav{
display:flex;
justify-content:space-between;
align-items:center;
width:90%;
margin:auto;
}

.menu a{
color:white;
margin-left:20px;
text-decoration:none;
}

.menu a:hover{
color:#22c55e;
}

/* FORM */
.form-box{
background:#1e293b;
padding:30px;
border-radius:10px;
margin-top:50px;
}

input{
width:100%;
padding:10px;
margin-bottom:15px;
border:none;
border-radius:5px;
}

/* PASSWORD WRAPPER */
.pass-wrapper{
position:relative;
}

.pass-wrapper span{
position:absolute;
right:10px;
top:10px;
cursor:pointer;
color:#ccc;
}

button{
background:#22c55e;
border:none;
padding:10px;
color:white;
width:100%;
}

</style>

</head>

<body>

<header>
<div class="nav">

<div><b>GYM</b></div>

<div class="menu">
<a href="index.php">Home</a>
<a href="about.php">About</a>
<a href="contact.php">Contact</a>
<a href="admin/">Admin</a>
</div>

</div>
</header>

<div class="container">

<div class="col-md-6 m-auto">

<div class="form-box">

<h3>Registration Form</h3>

<?php if($error){ ?>
<div style="background:red;padding:10px;"><?php echo $error; ?></div>
<?php } ?>

<form method="post">

<input type="text" name="fname" placeholder="First Name" required>
<input type="text" name="lname" placeholder="Last Name" required>
<input type="email" name="email" placeholder="Email" required>
<input type="text" name="mobile" placeholder="Mobile" required>
<input type="text" name="city" placeholder="City" required>

<!-- PASSWORD WITH EYE -->
<div class="pass-wrapper">
<input type="password" id="password" name="password" placeholder="Password" required>
<span onclick="togglePass('password')">👁</span>
</div>

<div class="pass-wrapper">
<input type="password" id="repeat" name="RepeatPassword" placeholder="Confirm Password" required>
<span onclick="togglePass('repeat')">👁</span>
</div>

<button type="submit" name="submit">Register Now</button>

</form>

</div>

</div>

</div>

<script>
function togglePass(id){
var x = document.getElementById(id);
if(x.type === "password"){
x.type = "text";
}else{
x.type = "password";
}
}
</script>

</body>
</html>