<?php
session_start();
error_reporting(0);
require_once('include/config.php');

if (!isset($_SESSION['uid'])) {
    header('location:login.php');
    exit;
}

$msg = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_verify()) {
    die('Invalid request. Please go back and try again.');
}

if (isset($_POST['submit'])) {
    $currentPassword = $_POST['password'];
    $newpassword     = $_POST['newpassword'];
    $email           = $_SESSION['email'];

    $sql   = "SELECT password FROM tbluser WHERE email = :email LIMIT 1";
    $query = $dbh->prepare($sql);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->execute();
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($currentPassword, $user['password'])) {
        $hashedNew = password_hash($newpassword, PASSWORD_BCRYPT);
        $con = "UPDATE tbluser SET password = :newpassword WHERE email = :email";
        $chngpwd1 = $dbh->prepare($con);
        $chngpwd1->bindParam(':email',       $email,     PDO::PARAM_STR);
        $chngpwd1->bindParam(':newpassword', $hashedNew, PDO::PARAM_STR);
        $chngpwd1->execute();
        $msg = "Your password has been changed successfully.";
    } else {
        $error = "Your current password is not valid.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Gym Management System | Change Password</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/user.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"/>
    <script>
    function valid() {
        if (document.chngpwd.newpassword.value != document.chngpwd.confirmpassword.value) {
            alert("New Password and Confirm Password do not match!");
            document.chngpwd.confirmpassword.focus();
            return false;
        }
        return true;
    }
    function togglePwd(inputId, iconEl) {
        var input = document.getElementById(inputId);
        if (input.type === 'password') {
            input.type = 'text';
            iconEl.classList.remove('fa-eye');
            iconEl.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            iconEl.classList.remove('fa-eye-slash');
            iconEl.classList.add('fa-eye');
        }
    }
    </script>
</head>
<body class="changepwd-page">

<!-- NAVBAR -->
<div class="navbar">
    <div class="logo">GYM</div>
    <div class="nav-center">
        <a href="index.php">Home</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
        <a href="booking-history.php">Booking History</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<!-- HERO -->
<div class="hero">
    <h1>Change Password</h1>
</div>

<!-- CHANGE PASSWORD SECTION -->
<div class="changepwd-section">
    <div class="changepwd-card">
        <h3><i class="fa fa-lock"></i> Update Your Password</h3>

        <?php if ($error) { ?>
        <div class="errorWrap"><strong>ERROR:</strong> <?php echo htmlentities($error); ?></div>
        <?php } elseif ($msg) { ?>
        <div class="succWrap"><strong>SUCCESS:</strong> <?php echo htmlentities($msg); ?></div>
        <?php } ?>

        <form name="chngpwd" method="post" onsubmit="return valid();">
            <?php echo csrf_field(); ?>

            <div class="form-group">
                <label for="password">Current Password</label>
                <div class="pwd-wrap">
                    <input type="password" name="password" id="password" autocomplete="off">
                    <span class="toggle-pwd" onclick="togglePwd('password', this.querySelector('i'))">
                        <i class="fa fa-eye"></i>
                    </span>
                </div>
            </div>

            <div class="form-group">
                <label for="newpassword">New Password</label>
                <div class="pwd-wrap">
                    <input type="password" name="newpassword" id="newpassword" autocomplete="off">
                    <span class="toggle-pwd" onclick="togglePwd('newpassword', this.querySelector('i'))">
                        <i class="fa fa-eye"></i>
                    </span>
                </div>
            </div>

            <div class="form-group">
                <label for="confirmpassword">Confirm New Password</label>
                <div class="pwd-wrap">
                    <input type="password" name="confirmpassword" id="confirmpassword" autocomplete="off">
                    <span class="toggle-pwd" onclick="togglePwd('confirmpassword', this.querySelector('i'))">
                        <i class="fa fa-eye"></i>
                    </span>
                </div>
            </div>

            <button type="submit" name="submit" class="btn-submit">Update Password</button>
        </form>
    </div>
</div>

<!-- FOOTER -->
<footer class="footer">
    <div class="footer-brand">GYM</div>
    <div class="footer-tagline">Train harder. Live better.</div>
    <div class="footer-copy">© 2026 Gym Management System. All rights reserved.</div>
</footer>

</body>
</html>
