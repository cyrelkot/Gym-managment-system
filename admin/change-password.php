<?php
error_reporting(0);
include  'include/config.php';
if (!isset($_SESSION['adminid']) || strlen($_SESSION['adminid']) == 0) {
  header('location:logout.php');
  exit;
  } else{

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_verify()) {
    die('Invalid request. Please go back and try again.');
}

// Code for change password
if(isset($_POST['submit']))
  {
$currentPassword = $_POST['password'];
$newpassword     = $_POST['newpassword'];
$email           = $_SESSION['email'];

$sql = "SELECT password FROM tbladmin WHERE email = :email LIMIT 1";
$query = $dbh->prepare($sql);
$query->bindParam(':email', $email, PDO::PARAM_STR);
$query->execute();
$admin = $query->fetch(PDO::FETCH_ASSOC);

$passwordOk = $admin && (
    password_verify($currentPassword, $admin['password']) ||
    $admin['password'] === md5($currentPassword)
);

if ($passwordOk) {
    $newHash = password_hash($newpassword, PASSWORD_BCRYPT);
    $con = "UPDATE tbladmin SET password = :newpassword WHERE email = :email";
    $chngpwd1 = $dbh->prepare($con);
    $chngpwd1->bindParam(':email',       $email,   PDO::PARAM_STR);
    $chngpwd1->bindParam(':newpassword', $newHash, PDO::PARAM_STR);
    $chngpwd1->execute();
    $msg = "Your Password succesfully changed";
} else {
    $error = "Your current password is not valid.";
}
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta name="description" content="Vali is a">
   <title>Admin | Change Password</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <!-- Font-icon css-->
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" type="image/png" href="../icon-fonts/gym-logo.png">
  </head>
  <body class="app sidebar-mini rtl">
    <!-- Navbar-->
   <?php include 'include/header.php'; ?>
    <!-- Sidebar menu-->
    <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
    <?php include 'include/sidebar.php'; ?>
    <main class="app-content">
      
      <div class="row">
        
        <div class="col-md-6">
          <div class="tile">
            <h3 class="tile-title">Change Password</h3>
             <?php if($error){?><div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php } 
        else if($msg){?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php }?>
            
            <div class="tile-body">
              <form class="row" method="post">
                <?php echo csrf_field(); ?>
                <div class="form-group col-md-12">
                  <label class="control-label">Old Password</label>
                <input type="password" name="password" id="password" placeholder="Old Password" class="form-control" autocomplete="off">
                </div>
                <div class="form-group col-md-12">
                  <label class="control-label">New Password</label>
                <input type="password" name="newpassword" id="newpassword" class="form-control" placeholder="New Password" autocomplete="off">
                </div>
                 <div class="form-group col-md-12">
                  <label class="control-label">Confirm Password</label>
                  <input type="password" name="confirmpassword" id="confirmpassword" placeholder="Confirm Password" autocomplete="off" class="form-control">

                </div>
                 
                <div class="form-group col-md-4 align-self-end">
                  <input type="Submit" name="submit" id="submit" class="btn btn-primary" value="Submit">
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </main>
    <!-- Essential javascripts for application to work-->
    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
    <script src="js/plugins/pace.min.js"></script>
  <script type="text/javascript">
function valid()
{
if(document.chngpwd.newpassword.value!= document.chngpwd.confirmpassword.value)
{
alert("New Password and Confirm Password Field do not match  !!");
document.chngpwd.confirmpassword.focus();
return false;
}
return true;
}
</script>
  </body>
</html>
<?php } ?>