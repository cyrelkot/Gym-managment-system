<?php
session_start();
error_reporting(0);
include 'include/config.php';

// Require admin session
if (!isset($_SESSION['adminid']) || strlen($_SESSION['adminid']) == 0) {
    header('location:logout.php');
    exit;
}

// Handle deleting a booking
if (isset($_POST['delete_booking']) && isset($_POST['bookingid'])) {
    $bookingId = intval($_POST['bookingid']);

    // Remove payments first to avoid orphan records
    $delPayments = $dbh->prepare("DELETE FROM tblpayment WHERE bookingID = :bookingId");
    $delPayments->bindParam(':bookingId', $bookingId, PDO::PARAM_INT);
    $delPayments->execute();

    // Remove booking
    $delBooking = $dbh->prepare("DELETE FROM tblbooking WHERE id = :bookingId");
    $delBooking->bindParam(':bookingId', $bookingId, PDO::PARAM_INT);
    $delBooking->execute();

    header('Location: booking-history.php');
    exit;
}

// Handle marking a booking as partial or full payment
if (isset($_POST['set_payment']) && isset($_POST['bookingid'])) {
    $bookingId = intval($_POST['bookingid']);
    $paymentType = ($_POST['set_payment'] === 'Full Payment') ? 'Full Payment' : 'Partial Payment';

    $upd = $dbh->prepare("UPDATE tblbooking SET paymentType = :paymentType WHERE id = :bookingId");
    $upd->bindParam(':paymentType', $paymentType, PDO::PARAM_STR);
    $upd->bindParam(':bookingId', $bookingId, PDO::PARAM_INT);
    $upd->execute();

    // Refresh to reflect changes
    header('Location: booking-history.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta name="description" content="Vali is a">
   <title>admin | All Bookings</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <!-- Font-icon css-->
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  </head>
  <body class="app sidebar-mini rtl">
    <!-- Navbar-->
   <?php include 'include/header.php'; ?>
    <!-- Sidebar menu-->
    <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
    <?php include 'include/sidebar.php'; ?>
    <main class="app-content">
     
      
       <div class="row">
        <div class="col-md-12">
          <div class="tile">
            <div class="tile-body">
              <h3>All Bookings</h3>
              <hr />
              <table class="table table-hover table-bordered" id="sampleTable">
                <thead>
                  <tr>
                    <th>Sr.No</th>
        <th>bookingid</th>
        <th>Name</th>
        <th>Email</th>
        <th>bookingdate</th>
                <th>PackageName</th>
<th>Payment Status</th>
        <th>Action</th>
                  
                  </tr>
                </thead>
               <?php
                  $sql="SELECT t1.id as bookingid,t3.fname as Name, t3.email as email,t1.booking_date as bookingdate,t2.titlename as title,t2.PackageDuratiobn as PackageDuratiobn,
t2.Price as Price,t2.Description as Description,t4.category_name as category_name,COALESCE(t5.PackageName, t2.titlename) as PackageName,t1.paymentType as paymentType FROM tblbooking as t1
 LEFT JOIN tbladdpackage as t2
 ON t1.package_id = t2.id
 LEFT JOIN tbluser as t3
 ON t1.userid = t3.id
 LEFT JOIN tblcategory as t4
 ON t2.category = t4.id
 LEFT JOIN tblpackage as t5
 ON t2.PackageType = t5.id
WHERE t1.paymentType = 'Full Payment'";
                  $query= $dbh->prepare($sql);
                  $query-> execute();
                  $results = $query -> fetchAll(PDO::FETCH_OBJ);
                  $cnt=1;
                  if($query -> rowCount() > 0)
                  {
                  foreach($results as $result)
                  {
                  ?>

                <tbody>
                  <tr>
                    <td><?php echo($cnt);?></td>
                    <td ><?php echo htmlentities($result->bookingid);?></td>
                    <td><?php echo htmlentities($result->Name);?></td>
                    <td><?php echo htmlentities($result->email);?></td>
                    <td><?php echo htmlentities($result->bookingdate);?></td>
                      <td><?php echo htmlentities($result->PackageName);?></td>
                    <td><?php echo (!empty($result->paymentType) ? htmlentities($result->paymentType) : 'Not paid'); ?></td>
                    <td>
                        <form method="post" style="display:inline;" onsubmit="return confirm('Delete this booking?');">
                            <input type="hidden" name="bookingid" value="<?php echo htmlentities($result->bookingid); ?>">
                            <button type="submit" name="delete_booking" value="1" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                  </tr>
                    <?php  $cnt=$cnt+1; } } ?>
              
                </tbody>
              </table>
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
    <!-- The javascript plugin to display page loading on top-->
    <script src="js/plugins/pace.min.js"></script>
    <!-- Page specific javascripts-->
    <!-- Data table plugin-->
    <script type="text/javascript" src="js/plugins/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="js/plugins/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript">$('#sampleTable').DataTable();</script>
  
  </body>
</html>