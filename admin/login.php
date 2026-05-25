<?php
error_reporting(E_ALL);
include 'include/config.php';

if (isset($_SESSION['adminid']) && strlen($_SESSION['adminid']) > 0) {
    header('location:index.php');
    exit();
}

$errmsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_verify()) {
    die('Invalid request. Please go back and try again.');
}

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $sql = "SELECT id, password, role FROM tbladmin WHERE email=:email LIMIT 1";
    $query = $dbh->prepare($sql);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->execute();
    $admin = $query->fetch(PDO::FETCH_ASSOC);

    $passwordOk = false;
    if ($admin) {
        if (password_verify($password, $admin['password'])) {
            $passwordOk = true;
        } elseif ($admin['password'] === md5($password)) {
            // Legacy MD5 account — rehash to bcrypt on login
            $newHash = password_hash($password, PASSWORD_BCRYPT);
            $upd = $dbh->prepare("UPDATE tbladmin SET password = :hash WHERE id = :id");
            $upd->bindParam(':hash', $newHash, PDO::PARAM_STR);
            $upd->bindParam(':id',   $admin['id'], PDO::PARAM_INT);
            $upd->execute();
            $passwordOk = true;
        }
    }

    if ($passwordOk) {
        session_regenerate_id(true);
        $_SESSION['adminid'] = $admin['id'];
        $_SESSION['email'] = $email;
        $_SESSION['adminrole'] = $admin['role'];
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
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(rgba(0,0,0,0.85), rgba(0,0,0,0.95)),
                  url('https://images.unsplash.com/photo-1554284126-aa88f22d8b74');
      background-size: cover;
      background-position: center;
      color: #fff;
    }

    .login-container {
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .login-box {
      background: rgba(0,0,0,0.82);
      padding: 40px;
      border-radius: 14px;
      width: 360px;
      box-shadow: 0 0 40px rgba(255,102,0,0.18), 0 0 80px rgba(0,0,0,0.7);
      text-align: center;
      border: 1px solid rgba(255,102,0,0.55);
      animation: glowPulse 3s ease-in-out infinite alternate;
    }

    @keyframes glowPulse {
      from { box-shadow: 0 0 30px rgba(255,102,0,0.12), 0 0 70px rgba(0,0,0,0.6); }
      to   { box-shadow: 0 0 45px rgba(255,102,0,0.28), 0 0 90px rgba(0,0,0,0.7); }
    }

    .logo {
      font-size: 30px;
      font-weight: bold;
      margin-bottom: 4px;
      color: #ff6600;
      letter-spacing: 3px;
    }

    .tagline {
      font-size: 11px;
      color: #888;
      letter-spacing: 2px;
      text-transform: uppercase;
      margin-bottom: 28px;
    }

    .form-group {
      margin-bottom: 20px;
      text-align: left;
    }

    .form-group label {
      font-size: 12px;
      color: #ccc;
      margin-bottom: 5px;
      display: block;
    }

    .input-icon-wrap {
      position: relative;
    }

    .input-icon-wrap .fa {
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      color: #666;
      font-size: 14px;
      pointer-events: none;
    }

    .form-control {
      width: 100%;
      padding: 10px 10px 10px 36px;
      border: none;
      border-radius: 6px;
      outline: none;
      background: #1a1a1a;
      color: #fff;
      font-size: 14px;
      border: 1px solid #333;
      box-sizing: border-box;
    }

    .form-control:focus {
      border: 1px solid #ff6600;
      box-shadow: 0 0 0 3px rgba(255,102,0,0.22);
    }

    .btn-login {
      width: 100%;
      padding: 12px;
      background: #ff6600;
      border: none;
      border-radius: 6px;
      font-weight: bold;
      color: #fff;
      cursor: pointer;
      transition: 0.3s;
    }

    .btn-login:hover {
      background: #e65c00;
      transform: scale(1.03);
    }

    .back-home {
      margin-top: 15px;
      display: block;
      color: #ccc;
      text-decoration: none;
      font-size: 13px;
    }

    .back-home:hover {
      color: #ff6600;
    }

    .alert {
      background: #ff3333;
      padding: 10px;
      border-radius: 6px;
      font-size: 13px;
      margin-bottom: 15px;
    }

  </style>
<link rel="icon" type="image/png" href="../icon-fonts/gym-logo.png">
</head>

<body>

<div class="login-container">
  <div class="login-box">

    <div class="logo">GYM MS</div>
    <div class="tagline">Admin Panel</div>

    <?php if ($errmsg) { ?>
      <div class="alert"><?php echo htmlentities($errmsg); ?></div>
    <?php } ?>

    <form method="post">
      <?php echo csrf_field(); ?>
      <div class="form-group">
        <label>Email</label>
        <div class="input-icon-wrap">
          <i class="fa fa-envelope"></i>
          <input type="email" name="email" class="form-control" placeholder="Enter email" required>
        </div>
      </div>

      <div class="form-group">
        <label>Password</label>
        <div class="input-icon-wrap">
          <i class="fa fa-lock"></i>
          <input type="password" name="password" class="form-control" placeholder="Enter password" required>
        </div>
      </div>

      <button type="submit" name="login" class="btn-login">LOGIN</button>

    </form>

    <a href="../index.php" class="back-home">Back to Home</a>

  </div>
</div>

</body>
</html>