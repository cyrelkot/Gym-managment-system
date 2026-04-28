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
  <meta name="viewport" content="width=device-width, initial-scale=1">

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
      background: rgba(0,0,0,0.80);
      padding: 40px;
      border-radius: 12px;
      width: 350px;
      box-shadow: 0 0 25px rgba(0,0,0,0.7);
      text-align: center;
      border: 1px solid #ff6600;
    }

    .logo {
      font-size: 28px;
      font-weight: bold;
      margin-bottom: 10px;
      color: #ff6600;
      letter-spacing: 2px;
    }

    .tagline {
      font-size: 13px;
      color: #aaa;
      margin-bottom: 25px;
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

    .form-control {
      width: 100%;
      padding: 10px;
      border: none;
      border-radius: 6px;
      outline: none;
      background: #1a1a1a;
      color: #fff;
      font-size: 14px;
      border: 1px solid #333;
    }

    .form-control:focus {
      border: 1px solid #ff6600;
      box-shadow: 0 0 5px #ff6600;
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
</head>

<body>

<div class="login-container">
  <div class="login-box">

    <div class="logo">
      ADMIN
    </div>
    <?php if ($errmsg) { ?>
      <div class="alert"><?php echo htmlentities($errmsg); ?></div>
    <?php } ?>

    <form method="post">

      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" class="form-control" placeholder="Enter email" required>
      </div>

      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" class="form-control" placeholder="Enter password" required>
      </div>

      <button type="submit" name="login" class="btn-login">LOGIN</button>

    </form>

    <a href="../index.php" class="back-home">Back to Home</a>

  </div>
</div>

</body>
</html>