<?php
session_start();
error_reporting(E_ALL);
include 'include/config.php';

if (isset($_SESSION['adminid']) && strlen($_SESSION['adminid']) > 0) {
    header('location:index.php');
    exit();
}

$errmsg = '';

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $sql = "SELECT id, password FROM tbladmin WHERE email=:email LIMIT 1";
    $query = $dbh->prepare($sql);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->execute();
    $admin = $query->fetch(PDO::FETCH_ASSOC);

    if ($admin && $admin['password'] === md5($password)) {
        $_SESSION['adminid'] = $admin['id'];
        header('location:index.php');
        exit();
    } else {
        $errmsg = 'Invalid email or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Admin Login</title>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" href="css/main.css">
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
  <section class="material-half-bg">
    <div class="cover"></div>
  </section>
  <section class="login-content">
    <div class="logo">
      <h1>GYM MS</h1>
    </div>
    <div class="login-box">
      <form class="login-form" method="post">
        <h3 class="login-head"><i class="fa fa-lg fa-fw fa-user"></i>ADMIN SIGN IN</h3>

        <?php if ($errmsg) { ?>
          <div class="alert alert-danger"><?php echo htmlentities($errmsg); ?></div>
        <?php } ?>

        <div class="form-group">
          <label class="control-label">EMAIL</label>
          <input class="form-control" type="email" name="email" placeholder="Email" required autofocus>
        </div>
        <div class="form-group">
          <label class="control-label">PASSWORD</label>
          <input class="form-control" type="password" name="password" placeholder="Password" required>
        </div>
        <div class="form-group btn-container">
          <button class="btn btn-primary btn-block" type="submit" name="login">
            <i class="fa fa-sign-in fa-lg fa-fw"></i>SIGN IN
          </button>
        </div>
      </form>
    </div>
  </section>
  <script src="js/jquery-3.2.1.min.js"></script>
  <script src="js/popper.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/main.js"></script>
</body>
</html>
