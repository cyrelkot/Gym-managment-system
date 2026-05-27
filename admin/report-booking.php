<?php
error_reporting(0);
include  'include/config.php';
if (!isset($_SESSION['adminid']) || strlen($_SESSION['adminid']) == 0) {
  header('location:logout.php');
  exit;
  } else{
require_permission('view_reports');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_verify()) {
    die('Invalid request. Please go back and try again.');
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta name="description" content="Vali is a">
   <title>Admin | Booking Report</title>
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
        
        <div class="col-md-12">
          <div class="tile">
             <!---Success Message--->  
          
          <!---Error Message--->
                      <h3 class="tile-title">Booking Report</h3>
            <div class="tile-body">
              <form class="row" method="post">
               <?php echo csrf_field(); ?>
               <div class="form-group col-md-6">
                  <label class="control-label">From Date</label>
                  <input class="form-control" type="date" name="fdate" id="fdate" placeholder="Enter From Date">
                </div>

                 <div class="form-group col-md-6">
                  <label class="control-label">To Date</label>
                  <input class="form-control" type="date" name="todate" id="todate" placeholder="Enter To Date">
                </div>
                <div class="form-group col-md-4 align-self-end">
                  <input type="Submit" name="Submit" id="Submit" class="btn btn-primary" value="Submit">
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      </div>
      <?php
      $isFiltered = isset($_POST['Submit']);
      if ($isFiltered) {
          $fdate = $_POST['fdate'];
          $tdate = $_POST['todate'];
          $sql = "SELECT t1.id as bookingid, t3.fname as Name, t3.email as email,
                  t1.booking_date as bookingdate, t2.titlename as title,
                  t2.PackageDuration as PackageDuration, t2.Price as Price,
                  t2.Description as Description, t4.category_name as category_name,
                  t2.titlename as Plan, t1.status, t1.paymentType
                  FROM tblbooking as t1
                  LEFT JOIN tbladdpackage as t2 ON t1.package_id = t2.id
                  LEFT JOIN tbluser as t3 ON t1.userid = t3.id
                  LEFT JOIN tblcategory as t4 ON t2.category = t4.id
                  WHERE date(t1.booking_date) BETWEEN :fdate AND :tdate
                  ORDER BY t1.booking_date DESC";
          $query = $dbh->prepare($sql);
          $query->bindParam(':fdate', $fdate, PDO::PARAM_STR);
          $query->bindParam(':tdate', $tdate, PDO::PARAM_STR);
      } else {
          $sql = "SELECT t1.id as bookingid, t3.fname as Name, t3.email as email,
                  t1.booking_date as bookingdate, t2.titlename as title,
                  t2.PackageDuration as PackageDuration, t2.Price as Price,
                  t2.Description as Description, t4.category_name as category_name,
                  t2.titlename as Plan, t1.status, t1.paymentType
                  FROM tblbooking as t1
                  LEFT JOIN tbladdpackage as t2 ON t1.package_id = t2.id
                  LEFT JOIN tbluser as t3 ON t1.userid = t3.id
                  LEFT JOIN tblcategory as t4 ON t2.category = t4.id
                  ORDER BY t1.booking_date DESC";
          $query = $dbh->prepare($sql);
      }
      $query->execute();
      $results = $query->fetchAll(PDO::FETCH_OBJ);

      // Summary query for total paid amount
      if ($isFiltered) {
          $sqlPaid = "SELECT COALESCE(SUM(tp.payment),0) as total_paid
                      FROM tblpayment tp
                      INNER JOIN tblbooking t1 ON tp.bookingID = t1.id
                      WHERE date(t1.booking_date) BETWEEN :fdate AND :tdate";
          $qPaid = $dbh->prepare($sqlPaid);
          $qPaid->bindParam(':fdate', $fdate, PDO::PARAM_STR);
          $qPaid->bindParam(':tdate', $tdate, PDO::PARAM_STR);
      } else {
          $sqlPaid = "SELECT COALESCE(SUM(tp.payment),0) as total_paid FROM tblpayment tp";
          $qPaid = $dbh->prepare($sqlPaid);
      }
      $qPaid->execute();
      $paidRow = $qPaid->fetch(PDO::FETCH_OBJ);

      // Compute summary stats
      $totalBookings  = count($results);
      $totalRevenue   = 0;
      $activeCount    = 0; $expiredCount  = 0;
      $fullyPaidCount = 0; $partialCount  = 0; $notPaidCount = 0;
      foreach ($results as $r) {
          $totalRevenue += (float)$r->Price;
          if ($r->status === 'active') $activeCount++; else $expiredCount++;
          if ($r->paymentType === 'Full Payment')        $fullyPaidCount++;
          elseif ($r->paymentType === 'Partial Payment') $partialCount++;
          else                                           $notPaidCount++;
      }
      $totalPaid      = (float)$paidRow->total_paid;
      $totalRemaining = max(0, $totalRevenue - $totalPaid);
      $cnt = 1;
      ?>

      <!-- Summary Cards -->
      <div class="row">
        <div class="col-md-3">
          <div class="tile" style="border-left:4px solid #3490dc">
            <div class="tile-body text-center">
              <div style="font-size:2rem;font-weight:700;color:#3490dc"><?php echo $totalBookings; ?></div>
              <div style="font-size:.85rem;color:#888;text-transform:uppercase;letter-spacing:.05em">Total Bookings</div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="tile" style="border-left:4px solid #38c172">
            <div class="tile-body text-center">
              <div style="font-size:2rem;font-weight:700;color:#38c172">&#8369;<?php echo number_format($totalRevenue,2); ?></div>
              <div style="font-size:.85rem;color:#888;text-transform:uppercase;letter-spacing:.05em">Total Revenue</div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="tile" style="border-left:4px solid #20c997">
            <div class="tile-body text-center">
              <div style="font-size:2rem;font-weight:700;color:#20c997">&#8369;<?php echo number_format($totalPaid,2); ?></div>
              <div style="font-size:.85rem;color:#888;text-transform:uppercase;letter-spacing:.05em">Total Paid</div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="tile" style="border-left:4px solid #ff6600">
            <div class="tile-body text-center">
              <div style="font-size:2rem;font-weight:700;color:#ff6600">&#8369;<?php echo number_format($totalRemaining,2); ?></div>
              <div style="font-size:.85rem;color:#888;text-transform:uppercase;letter-spacing:.05em">Remaining Balance</div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="tile" style="border-left:4px solid #6c757d">
            <div class="tile-body">
              <div style="font-size:.85rem;color:#888;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.5rem">Booking Status</div>
              <span style="color:#38c172;font-weight:600">Active: <?php echo $activeCount; ?></span>
              &nbsp;&middot;&nbsp;
              <span style="color:#dc3545;font-weight:600">Expired: <?php echo $expiredCount; ?></span>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="tile" style="border-left:4px solid #6c757d">
            <div class="tile-body">
              <div style="font-size:.85rem;color:#888;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.5rem">Payment Status</div>
              <span style="color:#20c997;font-weight:600">Fully Paid: <?php echo $fullyPaidCount; ?></span>
              &nbsp;&middot;&nbsp;
              <span style="color:#fd7e14;font-weight:600">Partial: <?php echo $partialCount; ?></span>
              &nbsp;&middot;&nbsp;
              <span style="color:#dc3545;font-weight:600">Not Paid: <?php echo $notPaidCount; ?></span>
            </div>
          </div>
        </div>
      </div>
      <?php if ($isFiltered): ?>
      <div class="row">
        <div class="col-md-12">
          <p style="color:#888;font-size:.85rem;margin-bottom:.5rem">
            Filtered by: <strong><?php echo htmlspecialchars($fdate,ENT_QUOTES,'UTF-8'); ?></strong> &ndash; <strong><?php echo htmlspecialchars($tdate,ENT_QUOTES,'UTF-8'); ?></strong>
          </p>
        </div>
      </div>
      <?php endif; ?>
      <div class="row">
        <div class="col-md-12">
          <div class="tile">
            <div class="tile-body">
              <table class="table table-hover table-bordered" id="sampleTable">
                <thead>
                  <tr>
                    <th>Sr.No</th>
                    <th hidden>bookingid</th>
                    <th>Name</th>
                    <th>email</th>
                    <th>bookingdate</th>
                    <th hidden>title</th>
                    <th>Duration</th>
                    <th>price</th>
                    <th hidden>Description</th>
                    <th>category_name</th>
                    <th>Plan</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($results as $result) { ?>
                  <tr>
                    <td><?php echo $cnt++; ?></td>
                    <td hidden><?php echo htmlentities($result->bookingid); ?></td>
                    <td><?php echo htmlspecialchars($result->Name, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($result->email, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($result->bookingdate, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td hidden><?php echo htmlspecialchars($result->title, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($result->PackageDuration, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($result->Price, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td hidden><?php echo htmlspecialchars($result->Description, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($result->category_name, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($result->Plan, ENT_QUOTES, 'UTF-8'); ?></td>
                  </tr>
                  <?php } ?>
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
    <script src="js/plugins/pace.min.js"></script>
    <script type="text/javascript" src="js/plugins/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="js/plugins/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript">$('#sampleTable').DataTable();</script>
  </body>
</html>
<?php } ?>