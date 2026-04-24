<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('include/config.php');

$error = "";

if(isset($_POST['submit'])){

$fname = $_POST['fname'];
$lname = $_POST['lname'];
$email = $_POST['email'];
$mobile = $_POST['mobile'];
$city = $_POST['city'];
$password = $_POST['password'];
$repeat = $_POST['RepeatPassword'];

$pattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8}$/";

/* VALIDATION */
if($password != $repeat){
    $error = "Password does not match!";
}
else if(strlen($password) != 8){
    $error = "Password must be EXACTLY 8 characters!";
}
else if(!preg_match($pattern, $password)){
    $error = "Must include uppercase, lowercase, number & symbol!";
}
else if(!preg_match('/^[0-9]{1,12}$/', $mobile)){
    $error = "Mobile number must be 1 to 12 digits only!";
}
else{

$status = 0;
$hash = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO tbluser
(fname,lname,email,mobile,city,password,status)
VALUES
(:fname,:lname,:email,:mobile,:city,:password,:status)";

$query = $dbh->prepare($sql);

$query->bindParam(':fname',$fname);
$query->bindParam(':lname',$lname);
$query->bindParam(':email',$email);
$query->bindParam(':mobile',$mobile);
$query->bindParam(':city',$city);
$query->bindParam(':password',$hash);
$query->bindParam(':status',$status);

$query->execute();

if($dbh->lastInsertId()){
echo "<script>alert('Welcome to GYM!');</script>";
echo "<script>window.location='login.php'</script>";
exit();
}

}
}
?>

<!DOCTYPE html>
<html>
<head>
<title>GYM Registration</title>

<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;800&display=swap" rel="stylesheet">

<style>

/* BACKGROUND */
body{
margin:0;
font-family:'Montserrat', sans-serif;
background:url('https://images.unsplash.com/photo-1554284126-aa88f22d8b74?auto=format&fit=crop&w=1600&q=80')
no-repeat center center/cover;
height:100vh;
overflow:hidden;
color:white;
}

/* OVERLAY */
body::before{
content:"";
position:fixed;
top:0;
left:0;
width:100%;
height:100%;
background:rgba(0,0,0,0.45);
z-index:0;
}

/* ORANGE GLOW */
body::after{
content:"";
position:fixed;
top:-200px;
left:-200px;
width:600px;
height:600px;
background:rgba(255,102,0,0.12);
filter:blur(150px);
z-index:0;
}

/* HEADER */
header{
position:relative;
z-index:2;
padding:20px 0;
border-bottom:1px solid rgba(255,102,0,0.3);
}

.nav{
width:85%;
margin:auto;
display:flex;
align-items:center;
justify-content:space-between;
position:relative;
}

.logo{
font-size:28px;
font-weight:800;
color:#ff6600;
letter-spacing:3px;
text-transform:uppercase;
}

/* CENTER MENU */
.menu{
position:absolute;
left:50%;
transform:translateX(-50%);
display:flex;
gap:25px;
}

.menu a{
color:white;
text-decoration:none;
font-weight:500;
}

.menu a:hover{
color:#ff6600;
}

.right-space{
width:120px;
}

/* FORM */
.container{
position:relative;
z-index:2;
height:90vh;
display:flex;
justify-content:center;
align-items:center;
}

.form-box{
width:420px;
background:rgba(20,20,20,0.85);
backdrop-filter: blur(12px);
border:1px solid rgba(255,102,0,0.3);
padding:35px;
border-radius:16px;
box-shadow:0 0 30px rgba(255,102,0,0.2);
}

h3{
text-align:center;
color:#ff6600;
margin-bottom:20px;
font-weight:800;
}

/* INPUT */
input{
width:100%;
padding:12px;
margin-bottom:12px;
border-radius:8px;
border:1px solid rgba(255,102,0,0.2);
background:#111;
color:white;
outline:none;
}

input:focus{
border:1px solid #ff6600;
box-shadow:0 0 10px rgba(255,102,0,0.3);
}

/* PASSWORD */
.pass-wrapper{
position:relative;
}

.pass-wrapper span{
position:absolute;
right:12px;
top:12px;
cursor:pointer;
color:#ff6600;
}

/* BUTTON */
button{
width:100%;
padding:12px;
border:none;
border-radius:8px;
background:#ff6600;
color:black;
font-weight:800;
cursor:pointer;
transition:0.3s;
}

button:hover{
background:#ff7a1a;
transform:scale(1.03);
}

/* ERROR */
.error{
background:rgba(255,0,0,0.8);
padding:10px;
border-radius:8px;
margin-bottom:10px;
text-align:center;
}

/* TEXT */
small{
display:block;
text-align:center;
margin-bottom:10px;
color:#ffb366;
}

</style>
</head>

<body>

<header>
<div class="nav">

<div class="logo">GYM</div>

<div class="menu">
<a href="index.php">Home</a>
<a href="about.php">About</a>
<a href="contact.php">Contact</a>
<a href="admin/">Admin</a>
</div>

<div class="right-space"></div>

</div>
</header>

<div class="container">

<div class="form-box">

<h3>Create Account</h3>

<?php if(!empty($error)){ ?>
<div class="error"><?php echo $error; ?></div>
<?php } ?>

<form method="post">

<input type="text" name="fname" placeholder="First Name" required>
<input type="text" name="lname" placeholder="Last Name" required>
<input type="email" name="email" placeholder="Email Address" required>

<!-- FIXED MOBILE INPUT -->
<input type="text" name="mobile" placeholder="Mobile Number" maxlength="12" required>

<input type="text" name="city" placeholder="City" required>

<small>8 chars: Uppercase + lowercase + number + symbol</small>

<div class="pass-wrapper">
<input type="password" id="password" name="password" placeholder="Password" maxlength="8" required>
<span onclick="togglePass('password')">👁</span>
</div>

<div class="pass-wrapper">
<input type="password" id="repeat" name="RepeatPassword" placeholder="Confirm Password" maxlength="8" required>
<span onclick="togglePass('repeat')">👁</span>
</div>

<button type="submit" name="submit">Register Now</button>

</form>

</div>

</div>

<script>
function togglePass(id){
var x = document.getElementById(id);
x.type = (x.type === "password") ? "text" : "password";
}

/* 🔥 STRICT MOBILE LIMIT (BLOCK COPY-PASTE OVERRIDE) */
document.querySelector('input[name="mobile"]').addEventListener('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
    if (this.value.length > 12) {
        this.value = this.value.slice(0, 12);
    }
});
</script>

</body>
</html>
