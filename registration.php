<?php
require_once('include/config.php');

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_verify()) {
    die('Invalid request. Please go back and try again.');
}

if(isset($_POST['submit'])){

$fname = $_POST['fname'];
$lname = $_POST['lname'];
$email = $_POST['email'];
$mobile = $_POST['mobile'];
$city = $_POST['city'];
$password = $_POST['password'];
$repeat = $_POST['RepeatPassword'];

$pattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/";

/* VALIDATION */
if($password != $repeat){
    $error = "Password does not match!";
}
else if(strlen($password) < 8){
    $error = "Password must be at least 8 characters!";
}
else if(!preg_match($pattern, $password)){
    $error = "Must include uppercase, lowercase, number & symbol!";
}
else if(!preg_match('/^[0-9]{1,12}$/', $mobile)){
    $error = "Mobile number must be 1 to 12 digits only!";
}
else{

$checkSql = "SELECT COUNT(*) FROM tbluser WHERE email = :email";
$checkQuery = $dbh->prepare($checkSql);
$checkQuery->bindParam(':email', $email, PDO::PARAM_STR);
$checkQuery->execute();
if ($checkQuery->fetchColumn() > 0) {
    $error = "An account with that email already exists.";
}
else {

$status = 0;
$hash = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO tbluser
(fname,lname,email,mobile,city,password,status)
VALUES
(:fname,:lname,:email,:mobile,:city,:password,:status)";

$query = $dbh->prepare($sql);

$query->bindParam(':fname',$fname);
$query->bindParam(':lname',$lname);
$query->bindParam(':email',$email);
$query->bindParam(':mobile',$mobile);
$query->bindParam(':city',$city);
$query->bindParam(':password',$hash);
$query->bindParam(':status',$status);

$query->execute();

if($dbh->lastInsertId()){
echo "<script>alert('Welcome to GYM!');</script>";
echo "<script>window.location='login.php'</script>";
exit();
}

} // end duplicate-email else
} // end validation else
}
?>

<!DOCTYPE html>
<html>
<head>
<title>GYM Registration</title>

<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/user.css"/>
</head>

<body class="reg-page">

<div class="navbar">
<div class="logo">GYM</div>
<div class="nav-center">
<a href="index.php">Home</a>
<a href="about.php">About</a>
<a href="contact.php">Contact</a>
</div>
</div>

<div class="container">

<div class="form-box">

<h3>Create Account</h3>

<?php if(!empty($error)){ ?>
<div class="error"><?php echo $error; ?></div>
<?php } ?>

<form method="post">
<?php echo csrf_field(); ?>
<input type="text" name="fname" placeholder="First Name" value="<?php echo htmlspecialchars($_POST['fname'] ?? ''); ?>" required>
<input type="text" name="lname" placeholder="Last Name" value="<?php echo htmlspecialchars($_POST['lname'] ?? ''); ?>" required>
<input type="email" name="email" placeholder="Email Address" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>

<!-- FIXED MOBILE INPUT -->
<input type="text" name="mobile" placeholder="Mobile Number" maxlength="12" value="<?php echo htmlspecialchars($_POST['mobile'] ?? ''); ?>" required>

<input type="text" name="city" placeholder="City" value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>" required>

<small>Min 8 chars: Uppercase + lowercase + number + symbol</small>

<div class="pass-wrapper">
<input type="password" id="password" name="password" placeholder="Password" value="<?php echo htmlspecialchars($_POST['password'] ?? ''); ?>" required>
<span onclick="togglePass('password')">👁</span>
</div>

<div class="pass-wrapper">
<input type="password" id="repeat" name="RepeatPassword" placeholder="Confirm Password" value="<?php echo htmlspecialchars($_POST['RepeatPassword'] ?? ''); ?>" required>
<span onclick="togglePass('repeat')">👁</span>
</div>

<button type="submit" name="submit">Register Now</button>

</form>

</div>

</div>

<script>
function togglePass(id){
var x = document.getElementById(id);
x.type = (x.type === "password") ? "text" : "password";
}

/* 🔥 STRICT MOBILE LIMIT (BLOCK COPY-PASTE OVERRIDE) */
document.querySelector('input[name="mobile"]').addEventListener('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
    if (this.value.length > 12) {
        this.value = this.value.slice(0, 12);
    }
});
</script>

</body>
</html>
