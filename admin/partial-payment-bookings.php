<?php session_start();
error_reporting(0);
include  'include/config.php'; 
if (strlen($_SESSION['adminid']==0)) {
  header('location:logout.php');
  exit;
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta name="description" content="Vali is a">
   <title>Admin | Partial Payment Bookings</title>
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
              <h3>Partial Payment Bookings</h3>
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
 WHERE LOWER(IFNULL(t1.paymentType, '')) LIKE '%partial%'";
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
                    <td><?php echo htmlentities($result->paymentType);?></td>
                     <td>
                       <a href="edit-booking.php?bookingid=<?php echo htmlentities($result->bookingid);?>" class="btn btn-info btn-sm">Edit</a>
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