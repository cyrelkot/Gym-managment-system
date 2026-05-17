<?php
session_start();
error_reporting(0);
require_once('include/config.php');

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_verify()) {
    die('Invalid request. Please go back and try again.');
}

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

                        session_regenerate_id(true);
                        $_SESSION['uid'] = $user['id'];
                        $_SESSION['fname'] = $user['fname'];
                        $_SESSION['email'] = $email;

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
<link rel="stylesheet" href="css/user.css"/>

</head>

<body class="login-page">

<div class="overlay">

<!-- NAVBAR -->
<div class="navbar">
<div class="logo">GYM</div>
<div class="nav-center">
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
<?php echo csrf_field(); ?>

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
