<?php
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


</head>

<body class="app sidebar-mini rtl">

<?php include 'include/header.php'; ?>
<?php include 'include/sidebar.php'; ?>

<main class="app-content">

  <div class="app-title">
    <h1><i class="fa fa-dashboard"></i> Dashboard</h1>
  </div>

  <?php if (!empty($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['flash_error'], ENT_QUOTES, 'UTF-8') ?></div>
    <?php unset($_SESSION['flash_error']); ?>
  <?php endif; ?>

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