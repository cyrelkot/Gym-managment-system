<?php
session_start();
error_reporting(0);
require_once('include/config.php');
if(strlen($_SESSION["uid"])==0)
{
    header('location:login.php');
    exit;
}
else{

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_verify()) {
    die('Invalid request. Please go back and try again.');
}

if(isset($_POST['submit']))
{
    $uid=$_SESSION['uid'];
    $fname=$_POST['fname'];
    $lname=$_POST['lname'];
    $email=$_POST['email'];
    $mobile=$_POST['mobile'];
    $city=$_POST['city'];
    $state=$_POST['state'];
    $address=$_POST['address'];
    $sql="update tbluser set fname=:fname,lname=:lname,mobile=:mobile,city=:city,state=:state,address=:Address where id=:uid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':fname',$fname,PDO::PARAM_STR);
    $query->bindParam(':lname',$lname,PDO::PARAM_STR);
    $query->bindParam(':mobile',$mobile,PDO::PARAM_STR);
    $query->bindParam(':city',$city,PDO::PARAM_STR);
    $query->bindParam(':state',$state,PDO::PARAM_STR);
    $query->bindParam(':Address',$address,PDO::PARAM_STR);
    $query->bindParam(':uid',$uid,PDO::PARAM_INT);
    $query->execute();
    echo "<script>alert('Profile has been updated.');</script>";
    echo "<script>window.location.href = 'profile.php';</script>";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Gym Management System | My Profile</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/user.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"/>
</head>
<body class="profile-page">

<!-- NAVBAR -->
<div class="navbar">
    <div class="logo">GYM</div>
    <div class="nav-center">
        <a href="index.php">Home</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
        <a href="booking-history.php">Booking History</a>
    </div>
    <div class="nav-right">
        <div class="user-menu">
            <div class="user-trigger">
                <span class="user-avatar"><?php echo htmlspecialchars(strtoupper(substr($_SESSION['fname'], 0, 1)), ENT_QUOTES, 'UTF-8'); ?></span>
                <span class="user-name"><?php echo htmlspecialchars($_SESSION['fname'], ENT_QUOTES, 'UTF-8'); ?></span>
                <span class="user-caret">&#9660;</span>
            </div>
            <div class="user-dropdown">
                <a href="profile.php">Profile</a>
                <a href="changepassword.php">Change Password</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </div>
</div>

<!-- HERO -->
<div class="hero">
    <h1>My Profile</h1>
</div>

<!-- PROFILE SECTION -->
<div class="profile-section">
    <div class="profile-card">
        <h3>Account Details</h3>

        <?php
        $uid=$_SESSION['uid'];
        $sql ="SELECT id, fname, lname, email, mobile, address, state, city FROM tbluser WHERE id=:uid";
        $query= $dbh->prepare($sql);
        $query->bindParam(':uid',$uid, PDO::PARAM_STR);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_OBJ);
        if($query->rowCount() > 0)
        {
        foreach($results as $result)
        { ?>

        <form method="post">
            <?php echo csrf_field(); ?>
            <div class="form-grid">
                <div class="form-group">
                    <label for="fname">First Name</label>
                    <input type="text" name="fname" id="fname" autocomplete="off"
                        value="<?php echo htmlspecialchars($result->fname, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="form-group">
                    <label for="lname">Last Name</label>
                    <input type="text" name="lname" id="lname" autocomplete="off"
                        value="<?php echo htmlspecialchars($result->lname, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="text" name="email" id="email" autocomplete="off" readonly
                        value="<?php echo htmlspecialchars($result->email, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="form-group">
                    <label for="mobile">Mobile</label>
                    <input type="text" name="mobile" id="mobile" autocomplete="off"
                        value="<?php echo htmlspecialchars($result->mobile, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="form-group">
                    <label for="state">State</label>
                    <input type="text" name="state" id="state" autocomplete="off"
                        value="<?php echo htmlspecialchars($result->state, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" name="city" id="city" autocomplete="off"
                        value="<?php echo htmlspecialchars($result->city, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="form-group full-width">
                    <label for="address">Address</label>
                    <input type="text" name="address" id="address" autocomplete="off"
                        value="<?php echo htmlspecialchars($result->address, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="form-group full-width">
                    <button type="submit" name="submit" id="submit" class="btn-submit">Update Profile</button>
                </div>
            </div>
        </form>

        <?php }} ?>
    </div>
</div>

<!-- FOOTER -->
<footer class="footer">
    <div class="footer-brand">GYM</div>
    <div class="footer-tagline">Train harder. Live better.</div>
    <div class="footer-copy">© 2026 Gym Management System. All rights reserved.</div>
</footer>

<script>
(function() {
    var trigger = document.querySelector('.user-trigger');
    if (!trigger) return;
    var menu = trigger.closest('.user-menu');
    trigger.addEventListener('click', function(e) {
        e.stopPropagation();
        menu.classList.toggle('open');
    });
    document.addEventListener('click', function() {
        menu.classList.remove('open');
    });
})();
</script>

</body>
</html>
<?php } ?>
