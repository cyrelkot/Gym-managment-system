<?php
session_start();
error_reporting(0);
require_once('include/config.php');

$msg = ""; 

if(isset($_POST['submit'])) {

  $email = trim($_POST['email']);
  $password = md5($_POST['password']);

  if($email != "" && $password != "") {
    try {
      $query = "SELECT id, name, email, password FROM tbladmin WHERE email=:email AND password=:password";
      $stmt = $dbh->prepare($query);
      $stmt->bindParam(':email', $email, PDO::PARAM_STR);
      $stmt->bindParam(':password', $password, PDO::PARAM_STR);
      $stmt->execute();

      if($stmt->rowCount() == 1){
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $_SESSION['adminid'] = $row['id'];
        $_SESSION['email']   = $row['email'];
        $_SESSION['name']    = $row['name'];

        header("location: index.php");
        exit();
      } else {
        $msg = "Invalid email or password!";
      }

    } catch (PDOException $e) {
      $msg = "Database error!";
    }
  } else {
    $msg = "All fields are required!";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gym Admin Login</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>

*{
    box-sizing:border-box;
}

body{
    margin:0;
    font-family:Segoe UI;
    background:#000;
    color:#fff;
    height:100vh;
    display:flex;
}

/* LEFT SIDE (IMAGE) */
.left{
    flex:1;
    background:url('https://images.unsplash.com/photo-1583454110551-21f2fa2afe61?auto=format&fit=crop&w=1400&q=80') center/cover no-repeat;
    position:relative;
}

.left::before{
    content:"";
    position:absolute;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.6);
}

/* RIGHT SIDE (FORM) */
.right{
    flex:1;
    display:flex;
    justify-content:center;
    align-items:center;
    background:#0d0d0d;
}

/* LOGIN BOX */
.login-box{
    width:360px;
    padding:40px;
    background:#111;
    border-radius:15px;
    box-shadow:0 0 25px rgba(255,106,0,0.3);
}

.login-box h2{
    text-align:center;
    color:#ff6a00;
    margin-bottom:25px;
}

/* ERROR MESSAGE */
.error{
    background:#330000;
    color:#ff4d4d;
    padding:10px;
    margin-bottom:15px;
    border-radius:6px;
    text-align:center;
}

/* INPUT GROUP */
.input-group{
    margin-bottom:15px;
}

.input-group label{
    font-size:14px;
    color:#ccc;
}

.input-group input{
    width:100%;
    padding:10px;
    margin-top:5px;
    border:none;
    border-radius:8px;
    background:#222;
    color:#fff;
}

/* BUTTON */
.btn{
    width:100%;
    padding:12px;
    background:#ff6a00;
    border:none;
    border-radius:8px;
    color:#000;
    font-weight:bold;
    cursor:pointer;
    transition:0.3s;
}

.btn:hover{
    background:#ff8c1a;
    transform:scale(1.03);
}

/* BACK LINK */
.back{
    display:block;
    text-align:center;
    margin-top:15px;
    color:#aaa;
    text-decoration:none;
}

.back:hover{
    color:#ff6a00;
}

/* RESPONSIVE */
@media(max-width:768px){
    .left{
        display:none;
    }
    .right{
        flex:1;
    }
}

</style>
</head>

<body>

<!-- LEFT IMAGE -->
<div class="left"></div>

<!-- RIGHT LOGIN -->
<div class="right">
    <div class="login-box">

        <h2><i class="fa fa-dumbbell"></i> Admin Login</h2>

        <?php if($msg){ ?>
            <div class="error"><?php echo htmlentities($msg); ?></div>
        <?php } ?>

        <form method="post">

            <div class="input-group">
                <label>Email</label>
                <input type="text" name="email" placeholder="Enter email">
            </div>

            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter password">
            </div>

            <button type="submit" name="submit" class="btn">
                <i class="fa fa-sign-in-alt"></i> LOGIN
            </button>

        </form>

        <a href="../index.php" class="back">← Back to Home</a>

    </div>
</div>

</body>
</html>