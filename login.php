<?php
session_start();
error_reporting(0);
require_once('include/config.php');

$msg = "";

if (isset($_POST['submit'])) {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($email !== "" && !empty($password)) {

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
                        $msg = "⚠️ Your account is pending admin approval.";
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
            $msg = "Login failed. Please try again.";
        }

    } else {
        $msg = "Fill all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Gym Management System</title>
<meta charset="UTF-8">

<link rel="stylesheet" href="css/bootstrap.min.css"/>
<link rel="stylesheet" href="css/font-awesome.min.css"/>

<style>

body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background: linear-gradient(135deg,#0f172a,#1e293b);
    color:#fff;
}

/* 🔥 CENTER HEADER FIX */
.top-header{
    display:flex;
    justify-content:center;   /* CENTER EVERYTHING */
    align-items:center;
    padding:15px 50px;
    background:rgba(0,0,0,0.4);
    position:relative;
}

/* LOGO LEFT CENTER */
.logo{
    position:absolute;
    left:50px;
    color:#22c55e;
    font-weight:bold;
    font-size:20px;
}

/* MENU CENTER */
.menu{
    display:flex;
    gap:25px;
}

.menu a{
    color:#fff;
    text-decoration:none;
    font-weight:500;
}

.menu a:hover{
    color:#22c55e;
}

/* LOGIN */
.login-container{
    height:90vh;
    display:flex;
    justify-content:center;
    align-items:center;
}

.login-card{
    width:100%;
    max-width:400px;
    padding:40px;
    border-radius:15px;
    background:rgba(255,255,255,0.08);
    backdrop-filter:blur(15px);
    box-shadow:0 10px 30px rgba(0,0,0,0.4);
    text-align:center;
}

.input-group{
    position:relative;
    margin:15px 0;
}

.input-group i.fa-envelope,
.input-group i.fa-lock{
    position:absolute;
    top:50%;
    left:12px;
    transform:translateY(-50%);
    color:#94a3b8;
}

.login-card input{
    width:100%;
    padding:12px 45px 12px 40px;
    border:none;
    border-radius:8px;
    background:rgba(255,255,255,0.1);
    color:#fff;
}

.login-card input::placeholder{
    color:#cbd5f5;
}

/* EYE */
.toggle-password{
    position:absolute;
    top:50%;
    right:12px;
    transform:translateY(-50%);
    cursor:pointer;
    color:#94a3b8;
}

.btn-main{
    width:100%;
    padding:12px;
    background:#22c55e;
    border:none;
    color:#fff;
    border-radius:8px;
    font-weight:bold;
}

.btn-main:hover{
    background:#16a34a;
}

.btn-secondary{
    display:block;
    margin-top:12px;
    color:#22c55e;
    text-decoration:none;
}

.error{
    background:#f59e0b;
    color:#000;
    padding:10px;
    border-radius:6px;
    margin-bottom:10px;
}

</style>
</head>

<body>

<!-- HEADER -->
<div class="top-header">

    <div class="logo">GYM</div>

    <div class="menu">
        <a href="index.php">Home</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
        <a href="admin/">Admin</a>
    </div>

</div>

<!-- LOGIN -->
<div class="login-container">
    <div class="login-card">

        <h3>User Login</h3>

        <?php if (!empty($msg)) { ?>
        <div class="error"><?php echo htmlentities($msg); ?></div>
        <?php } ?>

        <form method="post">

            <div class="input-group">
                <i class="fa fa-envelope"></i>
                <input type="text" name="email" placeholder="Enter Email" required>
            </div>

            <div class="input-group">
                <i class="fa fa-lock"></i>
                <input type="password" id="password" name="password" placeholder="Enter Password" required>

                <span class="toggle-password" onclick="togglePassword()">
                    <i class="fa fa-eye" id="eyeIcon"></i>
                </span>
            </div>

            <button type="submit" name="submit" class="btn-main">Login</button>
            <a href="registration.php" class="btn-secondary">Create Account</a>

        </form>

    </div>
</div>

<script>
function togglePassword(){
    var pass = document.getElementById("password");
    var icon = document.getElementById("eyeIcon");

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