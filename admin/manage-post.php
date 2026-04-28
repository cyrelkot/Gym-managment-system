<?php session_start();
error_reporting(0);
include  'include/config.php'; 
if (!isset($_SESSION['adminid']) || strlen($_SESSION['adminid']) == 0) {
  header('location:logout.php');
  } else{

if (isset($_POST['delete_package']) && isset($_POST['packageid'])) {
    $packageId = intval($_POST['packageid']);

    // Delete related payments and bookings for this package before deleting the package record.
    $delPayments = $dbh->prepare("DELETE FROM tblpayment WHERE bookingID IN (SELECT id FROM tblbooking WHERE package_id = :packageId)");
    $delPayments->bindParam(':packageId', $packageId, PDO::PARAM_INT);
    $delPayments->execute();

    $delBookings = $dbh->prepare("DELETE FROM tblbooking WHERE package_id = :packageId");
    $delBookings->bindParam(':packageId', $packageId, PDO::PARAM_INT);
    $delBookings->execute();

    $delPackage = $dbh->prepare("DELETE FROM tbladdpackage WHERE id = :id");
    $delPackage->bindParam(':id', $packageId, PDO::PARAM_INT);
    $delPackage->execute();

    header('Location: manage-post.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta name="description" content="Vali is a responsive">
  
    <title>Admin | Manage Package </title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <!-- Font-icon css-->
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
      body,
      .app-content {
        background: linear-gradient(rgba(0,0,0,0.88), rgba(0,0,0,0.96)),
                    url('https://images.unsplash.com/photo-1554284126-aa88f22d8b74');
        background-size: cover;
        background-position: center;
        color: #fff;
      }

      .tile {
        background: rgba(0, 0, 0, 0.80);
        border: 1px solid rgba(255, 102, 0, 0.45);
        border-radius: 14px;
        box-shadow: 0 0 22px rgba(0, 0, 0, 0.35);
        color: #fff;
      }

      h3,
      .table,
      .table th,
      .table td {
        color: #fff !important;
      }

      hr {
        border-top: 1px solid rgba(255, 102, 0, 0.35);
      }

      .table-bordered,
      .table-bordered th,
      .table-bordered td {
        border: 1px solid rgba(255,255,255,0.12) !important;
      }

      .table thead {
        background: rgba(255, 102, 0, 0.12);
      }

      .table-hover tbody tr:hover {
        background: rgba(255, 102, 0, 0.08);
      }

      .btn-success {
        background: #ff6600;
        border-color: #ff6600;
      }

      .btn-success:hover,
      .btn-success:focus {
        background: #e65c00;
        border-color: #e65c00;
      }
    </style>
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
               <h3>Manage Packages</h3>
              <hr />
              <table class="table table-hover table-bordered" id="sampleTable">
                <thead>
                  <tr>
                    <th>Sr.No</th>
                    <th>Category</th>
                    <th>Package Type</th>
                    <th>Title</th>
                    <th>Package Duration</th>
                    <th>Price</th>
                    <th>Action</th>
                  </tr>
                </thead>
              
                   <?php
                   include  'include/config.php';
                  $sql="SELECT t1.id as packageid, t1.titlename, t1.PackageDuratiobn as PackageDuration, t1.Price, t2.category_name, t3.PackageName
                    FROM tbladdpackage as t1
                    LEFT JOIN tblcategory as t2 ON t1.category = t2.id
                    LEFT JOIN tblpackage as t3 ON t1.PackageType = t3.id
                    ORDER BY t1.id DESC";
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
                    <td><?php echo htmlentities($result->category_name ?: 'Unassigned');?></td>
                  <td><?php echo htmlentities($result->PackageName ?: 'Unassigned');?></td>
                  <td><?php echo htmlentities($result->titlename);?></td>
                  <td><?php echo htmlentities($result->PackageDuration);?></td>
                  <td><?php echo htmlentities($result->Price);?></td>
                  <td>
                    <a href="edit-post.php?pid=<?php echo htmlentities($result->packageid);?>" class="btn btn-success btn-sm">Edit</a>
                    <form method="post" style="display:inline; margin-left: 5px;">
                      <input type="hidden" name="packageid" value="<?php echo htmlentities($result->packageid); ?>">
                      <button type="submit" name="delete_package" class="btn btn-danger btn-sm" onclick="return confirm('Delete this package?');">Delete</button>
                    </form>
                  </td>
                  </tr>
                   
                 
                </tbody>

     <!--    // end modal popup code........ -->
                 <?php  $cnt=$cnt+1; } } ?>
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
<?php } ?>