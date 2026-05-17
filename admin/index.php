<?php
session_start();
include 'include/config.php';

if (!isset($_SESSION['adminid']) || strlen($_SESSION['adminid']) == 0) {
  header('location:logout.php');
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Admin | Dashboard</title>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Main CSS -->
  <link rel="stylesheet" href="css/main.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

  <style>
    body, .app-content {
      background: linear-gradient(rgba(0,0,0,0.9), rgba(0,0,0,0.95)),
                  url('https://images.unsplash.com/photo-1554284126-aa88f22d8b74');
      background-size: cover;
      background-position: center;
      color: #fff;
    }

    .app-title {
      background: rgba(0,0,0,0.7);
      border: 1px solid rgba(255,102,0,0.5);
      border-radius: 12px;
      padding: 18px;
      margin-bottom: 25px;
    }

    .row {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
    }

    .row > div[class*="col-"] {
      display: flex;
    }

    .row > div[class*="col-"] > a {
      width: 100%;
      display: flex;
    }

    .widget-small {
      background: rgba(0,0,0,0.85);
      border: 1px solid rgba(255,102,0,0.5);
      border-radius: 14px;
      box-shadow: 0 0 20px rgba(0,0,0,0.4);
      transition: 0.3s;
      display: flex;
      align-items: center;
      width: 100%;
      height: 100%;
      padding: 15px;
    }

    .widget-small:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 30px rgba(0,0,0,0.6);
    }

    .widget-small .icon {
      background: #ff6600;
      color: #fff;
      padding: 20px;
      border-radius: 12px;
      margin-right: 15px;
    }

    .widget-small .info {
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .widget-small h4 {
      margin: 0;
      font-size: 18px;
      color: #fff;
    }

    .widget-small p {
      margin: 5px 0 0;
      font-size: 20px;
      font-weight: bold;
    }

    a {
      text-decoration: none !important;
    }
  </style>
</head>

<body class="app sidebar-mini rtl">

<?php include 'include/header.php'; ?>
<?php include 'include/sidebar.php'; ?>

<main class="app-content">

  <div class="app-title">
    <h1><i class="fa fa-dashboard"></i> Dashboard</h1>
  </div>

  <div class="row">

    <!-- Package Type -->
    <div class="col-md-6 col-lg-4">
      <?php
      $sql="SELECT count(id) as total FROM tblcategory;";
      $query= $dbh->prepare($sql);
      $query->execute();
      $result = $query->fetch(PDO::FETCH_OBJ);
      ?>
      <a href="add-package.php">
        <div class="widget-small">
          <i class="icon fa fa-cubes fa-2x"></i>
          <div class="info">
            <h4>Package Types</h4>
            <p><?php echo $result->total; ?></p>
          </div>
        </div>
      </a>
    </div>

    <!-- Packages -->
    <div class="col-md-6 col-lg-4">
      <?php
      $sql="SELECT count(id) as total FROM tbladdpackage;";
      $query= $dbh->prepare($sql);
      $query->execute();
      $result = $query->fetch(PDO::FETCH_OBJ);
      ?>
      <a href="manage-post.php">
        <div class="widget-small">
          <i class="icon fa fa-list fa-2x"></i>
          <div class="info">
            <h4>Packages</h4>
            <p><?php echo $result->total; ?></p>
          </div>
        </div>
      </a>
    </div>

    <!-- Total Bookings -->
    <div class="col-md-6 col-lg-4">
      <?php
      $sql="SELECT count(id) as total FROM tblbooking;";
      $query= $dbh->prepare($sql);
      $query->execute();
      $result = $query->fetch(PDO::FETCH_OBJ);
      ?>
      <a href="booking-history.php">
        <div class="widget-small">
          <i class="icon fa fa-users fa-2x"></i>
          <div class="info">
            <h4>Total Bookings</h4>
            <p><?php echo $result->total; ?></p>
          </div>
        </div>
      </a>
    </div>

    <!-- New Bookings -->
    <div class="col-md-6 col-lg-4">
      <?php
      $sql="SELECT count(id) as total FROM tblbooking WHERE paymentType IS NULL OR paymentType='';";
      $query= $dbh->prepare($sql);
      $query->execute();
      $result = $query->fetch(PDO::FETCH_OBJ);
      ?>
      <a href="new-bookings.php">
        <div class="widget-small">
          <i class="icon fa fa-user fa-2x"></i>
          <div class="info">
            <h4>New Bookings</h4>
            <p><?php echo $result->total; ?></p>
          </div>
        </div>
      </a>
    </div>

    <!-- Partial -->
    <div class="col-md-6 col-lg-4">
      <?php
      $sql="SELECT count(id) as total FROM tblbooking WHERE paymentType='Partial Payment';";
      $query= $dbh->prepare($sql);
      $query->execute();
      $result = $query->fetch(PDO::FETCH_OBJ);
      ?>
      <a href="partial-payment-bookings.php">
        <div class="widget-small">
          <i class="icon fa fa-credit-card fa-2x"></i>
          <div class="info">
            <h4>Partial Payments</h4>
            <p><?php echo $result->total; ?></p>
          </div>
        </div>
      </a>
    </div>

    <!-- Full -->
    <div class="col-md-6 col-lg-4">
      <?php
      $sql="SELECT count(id) as total FROM tblbooking WHERE paymentType='Full Payment';";
      $query= $dbh->prepare($sql);
      $query->execute();
      $result = $query->fetch(PDO::FETCH_OBJ);
      ?>
      <a href="full-payment-bookings.php">
        <div class="widget-small">
          <i class="icon fa fa-check fa-2x"></i>
          <div class="info">
            <h4>Full Payments</h4>
            <p><?php echo $result->total; ?></p>
          </div>
        </div>
      </a>
    </div>

  </div>

</main>

<script src="js/jquery-3.2.1.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/main.js"></script>

</body>
</html>