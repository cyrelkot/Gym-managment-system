<?php
session_start();
error_reporting(0);
require_once('include/config.php');

$msg = "";

if (isset($_POST['submit'])) {

$email = trim($_POST['email']);
$password = $_POST['password'];

if (!empty($email) && !empty($password)) {

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = "Invalid email format.";
    } else {

        try {

            $sql = "SELECT id, fname, password, status 
                    FROM tbluser 
                    WHERE email = :email 
                    LIMIT 1";

            $query = $dbh->prepare($sql);
            $query->bindParam(':email', $email, PDO::PARAM_STR);
            $query->execute();
            $user = $query->fetch(PDO::FETCH_ASSOC);

            if ($user) {

                if (password_verify($password, $user['password'])) {

                    if ((int)$user['status'] === 0) {
                        $msg = "Your account is pending admin approval.";
                    } else {

                        $_SESSION['uid'] = $user['id'];
                        $_SESSION['fname'] = $user['fname'];

                        header("location:index.php");
                        exit;
                    }

                } else {
                    $msg = "Invalid email or password.";
                }

            } else {
                $msg = "Invalid email or password.";
            }

        } catch (PDOException $e) {
            $msg = "Login failed.";
        }
    }

} else {
    $msg = "Fill all fields.";
}
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Gym Login</title>

<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="css/font-awesome.min.css">

<style>

/* GLOBAL */
*{
box-sizing:border-box;
margin:0;
padding:0;
}

body{
font-family:'Segoe UI',sans-serif;
background:url("https://images.unsplash.com/photo-1540497077202-7c8a3999166f") center/cover no-repeat;
height:100vh;
color:white;
}

/* OVERLAY */
.overlay{
background:rgba(0,0,0,0.75);
height:100vh;
}

/* 🔥 HEADER (FIXED & CENTERED MENU) */
.header{
position:relative;
display:flex;
align-items:center;
padding:20px 60px;
}

/* LOGO LEFT */
.logo{
position:absolute;
left:60px;
font-size:24px;
font-weight:bold;
color:#ff6600;
}

/* MENU CENTER */
.menu{
margin:0 auto;
display:flex;
gap:30px;
}

.menu a{
color:white;
text-decoration:none;
font-weight:500;
}

.menu a:hover{
color:#ff6600;
}

/* MAIN */
.main{
display:flex;
align-items:center;
justify-content:space-between;
height:85vh;
padding:0 80px;
}

/* LEFT TEXT */
.hero-text{
max-width:500px;
}

.hero-text h1{
font-size:48px;
}

.hero-text span{
color:#ff6600;
}

.hero-text p{
color:#ccc;
margin-top:10px;
}

/* LOGIN CARD */
.login-card{
width:350px;
background:rgba(0,0,0,0.85);
padding:35px;
border-radius:10px;
}

.login-card h2{
text-align:center;
margin-bottom:20px;
color:#ff6600;
}

/* INPUT GROUP */
.input-group{
position:relative;
margin-bottom:15px;
}

/* LEFT ICON */
.left-icon{
position:absolute;
left:12px;
top:50%;
transform:translateY(-50%);
color:#aaa;
pointer-events:none;
}

/* INPUT */
.login-card input{
width:100%;
padding:12px 45px;
border:none;
border-radius:6px;
background:#1f1f1f;
color:white;
outline:none;
}

/* EYE BUTTON */
.toggle-password{
position:absolute;
right:12px;
top:50%;
transform:translateY(-50%);
cursor:pointer;
color:#aaa;
z-index:999;
}

.toggle-password i{
pointer-events:none;
}

/* BUTTON */
.btn-login{
width:100%;
padding:12px;
border:none;
background:#ff6600;
color:white;
border-radius:6px;
font-weight:bold;
cursor:pointer;
}

.btn-login:hover{
background:#e65c00;
}

/* REGISTER */
.register{
text-align:center;
margin-top:10px;
}

.register a{
color:#ff6600;
text-decoration:none;
}

/* ERROR */
.error{
background:#ff4d4d;
padding:10px;
margin-bottom:10px;
border-radius:6px;
text-align:center;
}

</style>

</head>

<body>

<div class="overlay">

<!-- HEADER -->
<div class="header">
<div class="logo">GYM</div>

<div class="menu">
<a href="index.php">Home</a>
<a href="about.php">About</a>
<a href="contact.php">Contact</a>
<a href="admin/">Admin</a>
</div>
</div>

<!-- MAIN -->
<div class="main">

<div class="hero-text">
<h1>Train <span>Hard</span><br>Stay <span>Strong</span></h1>
<p>Join our gym and achieve your fitness goals.</p>
</div>

<div class="login-card">

<h2>Member Login</h2>

<?php if(!empty($msg)){ ?>
<div class="error"><?php echo htmlentities($msg); ?></div>
<?php } ?>

<form method="post">

<div class="input-group">
<i class="fa fa-envelope left-icon"></i>
<input type="email" name="email" placeholder="Email Address" required>
</div>

<div class="input-group">
<i class="fa fa-lock left-icon"></i>

<input type="password" id="password" name="password" placeholder="Password" required>

<span class="toggle-password" onclick="togglePassword()">
<i class="fa fa-eye"></i>
</span>
</div>

<button type="submit" name="submit" class="btn-login">
Login
</button>

<div class="register">
<a href="registration.php">Create Account</a>
</div>

</form>

</div>

</div>

</div>

<script>
function togglePassword(){
var pass = document.getElementById("password");
var icon = document.querySelector(".toggle-password i");

if(pass.type === "password"){
pass.type = "text";
icon.classList.replace("fa-eye","fa-eye-slash");
}else{
pass.type = "password";
icon.classList.replace("fa-eye-slash","fa-eye");
}
}
</script>

</body>
</html>
