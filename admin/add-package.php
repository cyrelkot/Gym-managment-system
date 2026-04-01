

<?php 
session_start();
error_reporting(E_ALL);
include 'include/config.php'; 

if (strlen($_SESSION['adminid']==0)) {
  header('location:logout.php');
  exit();
} else {

$msg = '';
$errormsg = '';

// ================= ADD PACKAGE =================
if(isset($_POST['submit'])){
  $AddPackage = $_POST['addPackage'];
  $categoryName = trim($_POST['category']);

  if(empty($categoryName)){
    $errormsg = "Please enter a category.";
  } elseif(empty($AddPackage)){
    $errormsg = "Please enter package name.";
  } else {

    // Check if category exists
    $stmt = $dbh->prepare("SELECT id FROM tblcategory WHERE category_name = :catname");
    $stmt->bindParam(':catname', $categoryName, PDO::PARAM_STR);
    $stmt->execute();
    $catResult = $stmt->fetch(PDO::FETCH_ASSOC);

    if($catResult){
        $categoryId = $catResult['id'];
    } else {
        // Insert new category
        $insStmt = $dbh->prepare("INSERT INTO tblcategory (category_name) VALUES (:catname)");
        $insStmt->bindParam(':catname', $categoryName, PDO::PARAM_STR);
        $insStmt->execute();
        $categoryId = $dbh->lastInsertId();
    }

    // Insert package
    $sql="INSERT INTO tblpackage (PackageName,cate_id) VALUES(:Package,:category)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':Package',$AddPackage,PDO::PARAM_STR);
    $query->bindParam(':category',$categoryId,PDO::PARAM_INT);
    $query->execute();

    if($dbh->lastInsertId()>0){
      echo "<script>alert('Package Added Successfully');</script>";
      echo "<script>window.location.href='add-package.php';</script>";
      exit();
    } else {
      $errormsg= "Data not inserted successfully";
    }
  }
}

// ================= DELETE PACKAGE =================
if(isset($_GET['del']) && !empty($_GET['del'])){
    $uid = intval($_GET['del']);

    $sql = "DELETE FROM tblpackage WHERE id = :id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id',$uid,PDO::PARAM_INT);

    if($query->execute()){
        echo "<script>alert('Record deleted successfully');</script>";
        echo "<script>window.location.href='add-package.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error deleting record');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Admin | Add Package Type</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="stylesheet" href="css/main.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body class="app sidebar-mini rtl">

<?php include 'include/header.php'; ?>
<?php include 'include/sidebar.php'; ?>

<main class="app-content">

<h3>Package Types</h3>
<hr />

<div class="row">
  <div class="col-md-6">
    <div class="tile">

      <?php if($msg){ ?>
        <div class="alert alert-success"><?php echo htmlentities($msg);?></div>
      <?php } ?>

      <?php if($errormsg){ ?>
        <div class="alert alert-danger"><?php echo htmlentities($errormsg);?></div>
      <?php } ?>

      <form method="post">
        <div class="form-group">
          <label>Add Category</label>
          <input class="form-control" name="category" type="text" required>
        </div>

        <div class="form-group">
          <label>Add Package</label>
          <input class="form-control" name="addPackage" type="text" required>
        </div>

        <button type="submit" name="submit" class="btn btn-primary">Submit</button>
      </form>

    </div>
  </div>
</div>

<!-- TABLE -->
<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <table class="table table-bordered" id="sampleTable">
        <thead>
          <tr>
            <th>#</th>
            <th>Category</th>
            <th>Package</th>
            <th>Action</th>
          </tr>
        </thead>

        <tbody>
        <?php
        // ✅ FIXED QUERY (WITH ALIAS)
        $sql="SELECT tblpackage.id AS pid, tblpackage.PackageName, tblcategory.category_name 
              FROM tblpackage 
              JOIN tblcategory 
              ON tblpackage.cate_id = tblcategory.id";

        $query= $dbh->prepare($sql);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_OBJ);

        $cnt=1;
        foreach($results as $result){
        ?>
          <tr>
            <td><?php echo $cnt; ?></td>
            <td><?php echo htmlentities($result->category_name); ?></td>
            <td><?php echo htmlentities($result->PackageName); ?></td>
            <td>
              <!-- ✅ FIXED DELETE BUTTON -->
              <a href="add-package.php?del=<?php echo $result->pid; ?>" 
                 class="btn btn-danger"
                 onclick="return confirm('Are you sure you want to delete this record?');">
                 Delete
              </a>
            </td>
          </tr>
        <?php $cnt++; } ?>
        </tbody>

      </table>
    </div>
  </div>
</div>

</main>

<script src="js/jquery-3.2.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>

</body>
</html>

<?php } ?>