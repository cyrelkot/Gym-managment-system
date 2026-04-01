<?php session_start();
error_reporting(0);
include  'include/config.php'; 

$successmsg = "";
$errormsg = "";

// Ensure `status` column exists so approvals can work.
try {
    $colStmt = $dbh->prepare("SHOW COLUMNS FROM tbluser LIKE 'status'");
    $colStmt->execute();
    if ($colStmt->rowCount() === 0) {
        $dbh->exec("ALTER TABLE tbluser ADD COLUMN status TINYINT(1) NOT NULL DEFAULT 0 AFTER password");
        // Mark existing users as approved (optional) or pending
        $dbh->exec("UPDATE tbluser SET status=1 WHERE status IS NULL");
    }
} catch (Exception $e) {
    // If the database cannot be altered, we'll still show users but approval won't work.
    $errormsg = "Warning: unable to ensure approval column exists. Approvals may not work. (" . htmlspecialchars($e->getMessage()) . ")";
}

if (isset($_POST['approve']) && isset($_POST['userid'])) {
    $uid = intval($_POST['userid']);
    $sql = "UPDATE tbluser SET status=1 WHERE id=:uid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':uid', $uid, PDO::PARAM_INT);
    if ($query->execute()) {
        $successmsg = "User approved successfully.";
    } else {
        $errormsg = "Failed to approve user.";
    }
}

if (strlen($_SESSION['adminid']==0)) {
  header('location:logout.php');
  } else{
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta name="description" content="Vali is a">
   <title>Admin | user Registration Report</title>
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
             <!---Success Message--->  
          <?php if($successmsg){?><div class="alert alert-success"><?php echo htmlentities($successmsg);?></div><?php } ?>
          <?php if($errormsg){?><div class="alert alert-danger"><?php echo htmlentities($errormsg);?></div><?php } ?>
          <!---Error Message--->
                      <h3 class="tile-title">Registration Report</h3>
            <div class="tile-body">
              <form class="row" method="post">
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
      // Show all users by default (newly registered users are included). A date range filter can still be applied.
      $statusColumnAvailable = true;
      if(isset($_POST['Submit'])){
        $fdate=$_POST['fdate'];
        $tdate=$_POST['todate'];
        $sql="SELECT id, fname, lname, email, mobile, password, state, city, address, create_date, status from tbluser
where date(create_date) between :fdate and :tdate
ORDER BY create_date DESC";
      } else {
        $sql = "SELECT id, fname, lname, email, mobile, password, state, city, address, create_date, status from tbluser ORDER BY create_date DESC";
      }

      try {
          $query= $dbh->prepare($sql);
          if(isset($_POST['Submit'])) {
            $query->bindParam(':fdate',$fdate, PDO::PARAM_STR);
            $query->bindParam(':tdate',$tdate, PDO::PARAM_STR);
          }
          $query-> execute();
          $results = $query -> fetchAll(PDO::FETCH_OBJ);
      } catch (PDOException $e) {
          // If the `status` column isn't available yet (schema without approval workflow), fall back to listing users without status
          $statusColumnAvailable = false;
          $errormsg = "Showing registrations, but status is unavailable. (" . htmlspecialchars($e->getMessage()) . ")";

          if(isset($_POST['Submit'])){
            $sql="SELECT id, fname, lname, email, mobile, password, state, city, address, create_date from tbluser
where date(create_date) between :fdate and :tdate
ORDER BY create_date DESC";
          } else {
            $sql = "SELECT id, fname, lname, email, mobile, password, state, city, address, create_date from tbluser ORDER BY create_date DESC";
          }
          $query= $dbh->prepare($sql);
          if(isset($_POST['Submit'])) {
            $query->bindParam(':fdate',$fdate, PDO::PARAM_STR);
            $query->bindParam(':tdate',$tdate, PDO::PARAM_STR);
          }
          $query-> execute();
          $results = $query -> fetchAll(PDO::FETCH_OBJ);
      }
      $cnt=1;
      ?>
       <div class="row">
        <div class="col-md-12">
          <div class="tile">
            <div class="tile-body">
              <table class="table table-hover table-bordered" id="sampleTable">
                <thead>
                  <tr>
              <th>Sr.No</th>
              <th>Name</th>
              <th>email</th>
              <th>mobile</th>
              <th>state</th>
              <th>city</th>
              <th>Registered</th>
              <th>Status</th>
              <th>Action</th>
                    
                  </tr>
                </thead>
                <tbody>
                  <?php
                  if(!empty($results))
                  {
                  foreach($results as $result)
                  {
                  ?>
                  <tr>
                    <td><?php echo($cnt);?></td>
                    <td><?php echo htmlentities($result->fname);?> <?php echo htmlentities($result->lname);?></td>
                    <td><?php echo htmlentities($result->email);?></td>
                    <td><?php echo htmlentities($result->mobile);?></td>
                    <td><?php echo htmlentities($result->state);?></td>
                    <td><?php echo htmlentities($result->city);?></td>
                    <td><?php echo htmlentities(date('Y-m-d H:i', strtotime($result->create_date)));?></td>
                    <td><?php if($statusColumnAvailable && isset($result->status)) { echo (intval($result->status)===1) ? 'Approved' : 'Pending'; } else { echo 'N/A'; } ?></td>
                    <td>
                      <?php if($statusColumnAvailable && isset($result->status) && intval($result->status)===0){ ?>
                        <form method="post" style="display:inline;">
                          <input type="hidden" name="userid" value="<?php echo $result->id;?>">
                          <button type="submit" name="approve" class="btn btn-sm btn-success">Approve</button>
                        </form>
                      <?php } ?>
                    </td>
                  </tr>
                    <?php  $cnt=$cnt+1; } } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <?php ?>
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