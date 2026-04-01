<?php 
session_start();
error_reporting(0);
include 'include/config.php';

// Require login
if(strlen($_SESSION['uid'])==0){
    header('location:login.php');
    exit;
}

$uid = $_SESSION['uid'];

// Ensure user is approved before allowing booking
$check = $dbh->prepare("SELECT status FROM tbluser WHERE id=:uid");
$check->bindParam(':uid',$uid,PDO::PARAM_INT);
$check->execute();
$user = $check->fetch(PDO::FETCH_ASSOC);
$approved = ($user && isset($user['status']) && intval($user['status']) === 1);

if(isset($_POST['submit']))
{ 
    if(!$approved){
        echo "<script>alert('Your account is pending admin approval. You cannot book packages yet.');</script>";
        echo "<script>window.location.href='booking-history.php'</script>";
        exit;
    }

    $pid=$_POST['pid'];

    $sql="INSERT INTO tblbooking (package_id,userid) Values(:pid,:uid)";

    $query = $dbh -> prepare($sql);
    $query->bindParam(':pid',$pid,PDO::PARAM_STR);
    $query->bindParam(':uid',$uid,PDO::PARAM_STR);
    $query -> execute();
    echo "<script>alert('Package has been booked.');</script>";
    echo "<script>window.location.href='booking-history.php'</script>";

}

?>
<!DOCTYPE html>
<html lang="zxx">
<head>
	<title>Gym Management System</title>
	<meta charset="UTF-8">
	<meta name="description" content="Ahana Yoga HTML Template">
	<meta name="keywords" content="yoga, html">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- Stylesheets -->
	<link rel="stylesheet" href="css/bootstrap.min.css"/>
	<link rel="stylesheet" href="css/font-awesome.min.css"/>
	<link rel="stylesheet" href="css/owl.carousel.min.css"/>
	<link rel="stylesheet" href="css/nice-select.css"/>
	<link rel="stylesheet" href="css/magnific-popup.css"/>
	<link rel="stylesheet" href="css/slicknav.min.css"/>
	<link rel="stylesheet" href="css/animate.css"/>

	<!-- Main Stylesheets -->
	<link rel="stylesheet" href="css/style.css"/>

</head>
<body>
	<!-- Page Preloder -->
	

	<!-- Header Section -->
	<?php include 'include/header.php';?>
	<!-- Header Section end -->

	

	                                                                              
	<!-- Page top Section -->
	<section class="page-top-section set-bg" data-setbg="img/page-top-bg.jpg">
		<div class="container">
			<div class="row">
				<div class="col-lg-7 m-auto text-white">
					<h2>About GYM Management System</h2>
				</div>
			</div>
		</div>
	</section>



	<!-- Pricing Section -->
	<section class="pricing-section spad">
		<div class="container">
			<div class="section-title text-center">
				<img src="img/icons/logo-icon.png" alt="">
				<h2>About Us</h2>
			</div>
			<div class="row">

				<div class="col-lg-12 col-sm-6">
			<p>Our gym is dedicated to promoting a healthy and active lifestyle by providing a safe, clean, and motivating environment for all members. We aim to help individuals of all fitness levels achieve their personal goals—whether it’s building strength, losing weight, or maintaining overall wellness. We are equipped with modern fitness machines, organized workout spaces, and programs designed to suit different needs, allowing everyone from beginners to experienced athletes to train and improve at their own pace. Our Gym Management System is designed to make gym operations simple and efficient, enabling members to easily view available packages, book services, and track their activity, while administrators can manage memberships, monitor bookings, and generate reports effectively. We believe that fitness is not just about physical strength but also about discipline, confidence, and a healthier lifestyle, and our goal is to support every member in their fitness journey and help them become the best version of themselves.</p>
				</div>
			</div>
		</div>
	</section>
	

	<!-- Footer Section -->
	<?php include 'include/footer.php'; ?>
	<!-- Footer Section end -->

	<div class="back-to-top"><img src="img/icons/up-arrow.png" alt=""></div>

	<!-- Search model end -->

	<!--====== Javascripts & Jquery ======-->
	<script src="js/vendor/jquery-3.2.1.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery.slicknav.min.js"></script>
	<script src="js/owl.carousel.min.js"></script>
	<script src="js/jquery.nice-select.min.js"></script>
	<script src="js/jquery-ui.min.js"></script>
	<script src="js/jquery.magnific-popup.min.js"></script>
	<script src="js/main.js"></script>

	</body>
</html>
