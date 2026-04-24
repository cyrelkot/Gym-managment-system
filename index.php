<?php 
session_start();
error_reporting(0);
include 'include/config.php';

if(strlen($_SESSION['uid'])==0){
    header('location:login.php');
    exit;
}

$uid = $_SESSION['uid'];

/* CHECK APPROVAL */
$approved = false;
$check = $dbh->prepare("SELECT status FROM tbluser WHERE id=:uid");
$check->bindParam(':uid',$uid,PDO::PARAM_INT);
$check->execute();
$user = $check->fetch(PDO::FETCH_ASSOC);

if($user && intval($user['status']) === 1){
    $approved = true;
}

/* CHECK IF USER HAS BOOKING */
$hasBooking = false;
$checkBooking = $dbh->prepare("SELECT id FROM tblbooking WHERE userid = :uid LIMIT 1");
$checkBooking->bindParam(':uid', $uid, PDO::PARAM_INT);
$checkBooking->execute();

if($checkBooking->rowCount() > 0){
    $hasBooking = true;
}

/* BOOKING */
if(isset($_POST['submit'])){ 

    if(!$approved){
        echo "<script>alert('Your account is pending admin approval.');</script>";
        exit;
    }

    $pid=$_POST['pid'];

    $sql="INSERT INTO tblbooking (package_id,userid) VALUES (:pid,:uid)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':pid',$pid,PDO::PARAM_STR);
    $query->bindParam(':uid',$uid,PDO::PARAM_STR);
    $query->execute();

    echo "<script>alert('Package booked successfully');</script>";
    echo "<script>window.location.href='booking-history.php'</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Gym Fitness</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="css/bootstrap.min.css"/>

<style>

body{
    margin:0;
    font-family:Segoe UI;
    background:#000;
    color:#fff;
}

/* NAVBAR */
.navbar{
    display:flex;
    justify-content:center;
    gap:40px;
    padding:20px;
    background:#000;
    border-bottom:2px solid #ff6a00;
    position:sticky;
    top:0;
}

.navbar a{
    color:#fff;
    text-decoration:none;
    font-weight:600;
}

.navbar a:hover{
    color:#ff6a00;
}

/* HERO */
.hero{
    height:55vh;
    background:url('https://images.unsplash.com/photo-1554284126-aa88f22d8b74?auto=format&fit=crop&w=1600&q=80') center/cover no-repeat;
    display:flex;
    align-items:center;
    justify-content:center;
    position:relative;
}

.hero::before{
    content:"";
    position:absolute;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.7);
}

.hero-content{
    position:relative;
    text-align:center;
}

.hero h1{
    font-size:55px;
    color:#ff6a00;
}

/* SECTION */
.section{
    padding:60px 20px;
}

.title{
    text-align:center;
    margin-bottom:40px;
}

/* GRID FIX */
.row{
    display:flex;
    flex-wrap:wrap;
}

.col-lg-3{
    display:flex;
}

/* CARD */
.pricing-item{
    background:#111;
    border:1px solid #222;
    border-top:3px solid #ff6a00;
    padding:25px;
    border-radius:15px;
    width:100%;

    display:flex;
    flex-direction:column;
    justify-content:space-between;

    min-height:370px;
}

.pricing-item h4{
    color:#ff6a00;
}

.price{
    font-size:28px;
    font-weight:bold;
}

/* DESCRIPTION */
.desc{
    max-height:100px;
    overflow:hidden;
    font-size:14px;
    color:#ccc;
}

/* BUTTON */
.btn-container{
    margin-top:auto;
}

.btn-book{
    background:#ff6a00;
    color:#000;
    padding:10px 20px;
    border:none;
    border-radius:8px;
    text-decoration:none;
    font-weight:bold;
}

/* FOOTER */
.footer{
    text-align:center;
    padding:20px;
    color:#777;
}

</style>
</head>

<body>

<!-- NAVBAR -->
<div class="navbar">
    <a href="index.php">Home</a>
    <a href="about.php">About</a>
    <a href="contact.php">Contact</a>

    <?php if($hasBooking){ ?>
        <a href="booking-history.php">Booking History</a>
    <?php } ?>

    <a href="logout.php">Logout</a>
</div>

<!-- HERO -->
<div class="hero">
    <div class="hero-content">
        <h1>BUILD YOUR BODY</h1>
        <p>Train hard. Stay strong.</p>
    </div>
</div>

<!-- PRICING -->
<div class="section">

    <div class="title">
        <h2>Fitness Plans</h2>
    </div>

    <div class="container">
        <div class="row">

        <?php 
        $sql ="SELECT * FROM tbladdpackage";
        $query= $dbh->prepare($sql);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_OBJ);

        foreach($results as $result)
        { ?>

            <div class="col-lg-3 col-sm-6">

                <div class="pricing-item">

                    <div>
                        <h4><?php echo $result->titlename; ?></h4>

                        <div class="price">
                            ₱<?php echo $result->Price; ?>
                        </div>

                        <p><?php echo $result->PackageDuratiobn; ?></p>

                        <div class="desc">
                            <?php echo $result->Description; ?>
                        </div>
                    </div>

                    <div class="btn-container">
                        <form method="post">
                            <input type="hidden" name="pid" value="<?php echo $result->id; ?>">
                            <input type="submit" name="submit" class="btn-book" value="Book Now">
                        </form>
                    </div>

                </div>

            </div>

        <?php } ?>

        </div>
    </div>
</div>

<!-- FOOTER -->
<div class="footer">
    © 2026 Gym Management System
</div>

</body>
</html>