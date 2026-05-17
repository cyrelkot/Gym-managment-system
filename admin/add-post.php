<?php session_start();
error_reporting(0);
include 'include/config.php';
if (!isset($_SESSION['adminid']) || strlen($_SESSION['adminid']) == 0) {
    header('location:logout.php');
    exit;
}

$msg = '';
$errormsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_verify()) {
    die('Invalid request. Please go back and try again.');
}

if (isset($_POST['Submit'])) {
    $category    = $_POST['category'];
    $titlename   = $_POST['titlename'];
    $duration    = $_POST['duration'];
    $Price       = $_POST['Price'];
    $description = $_POST['description'];

    $sql = "INSERT INTO tbladdpackage (category, titlename, PackageDuration, Price, Description)
            VALUES (:category, :titlename, :duration, :Price, :description)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':category',    $category,    PDO::PARAM_STR);
    $query->bindParam(':titlename',   $titlename,   PDO::PARAM_STR);
    $query->bindParam(':duration',    $duration,    PDO::PARAM_STR);
    $query->bindParam(':Price',       $Price,       PDO::PARAM_STR);
    $query->bindParam(':description', $description, PDO::PARAM_STR);
    $query->execute();

    if ($dbh->lastInsertId() > 0) {
        echo "<script>alert('Package added successfully.');</script>";
        echo "<script>window.location.href='manage-post.php';</script>";
    } else {
        $errormsg = "Could not insert package. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin | Add Package</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body, .app-content {
            background: linear-gradient(rgba(0,0,0,0.88), rgba(0,0,0,0.96)),
                        url('https://images.unsplash.com/photo-1554284126-aa88f22d8b74');
            background-size: cover;
            background-position: center;
            color: #fff;
        }
        .tile {
            background: rgba(0,0,0,0.80);
            border: 1px solid rgba(255,102,0,0.45);
            border-radius: 14px;
            box-shadow: 0 0 22px rgba(0,0,0,0.35);
            color: #fff;
        }
        h3, label, .tile-body, .tile-body * { color: #fff; }
        hr { border-top: 1px solid rgba(255,102,0,0.35); }
        .form-control {
            background: #1a1a1a;
            border: 1px solid #333;
            color: #fff;
        }
        .form-control:focus {
            background: #1a1a1a;
            border-color: #ff6600;
            box-shadow: 0 0 5px rgba(255,102,0,0.35);
            color: #fff;
        }
        select.form-control option { background: #1a1a1a; color: #fff; }
        .btn-primary { background: #ff6600; border-color: #ff6600; }
        .btn-primary:hover, .btn-primary:focus { background: #e65c00; border-color: #e65c00; }
    </style>
</head>
<body class="app sidebar-mini rtl">
    <?php include 'include/header.php'; ?>
    <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
    <?php include 'include/sidebar.php'; ?>
    <main class="app-content">
        <h3>Add Package</h3>
        <hr/>
        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <?php if ($msg) { ?>
                    <div class="alert alert-success"><strong>Done!</strong> <?php echo htmlentities($msg); ?></div>
                    <?php } ?>
                    <?php if ($errormsg) { ?>
                    <div class="alert alert-danger"><strong>Error:</strong> <?php echo htmlentities($errormsg); ?></div>
                    <?php } ?>
                    <div class="tile-body">
                        <form class="row" method="post">
                            <?php echo csrf_field(); ?>

                            <div class="form-group col-md-6">
                                <label class="control-label">Category</label>
                                <select name="category" class="form-control" required>
                                    <option value="">-- Select Category --</option>
                                    <?php
                                    $stmt = $dbh->prepare("SELECT * FROM tblcategory ORDER BY category_name");
                                    $stmt->execute();
                                    foreach ($stmt->fetchAll() as $cat) {
                                        echo "<option value='" . (int)$cat['id'] . "'>" . htmlspecialchars($cat['category_name']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label class="control-label">Title Name</label>
                                <input class="form-control" name="titlename" type="text" placeholder="Enter package title" required>
                            </div>

                            <div class="form-group col-md-6">
                                <label class="control-label">Package Duration</label>
                                <select name="duration" class="form-control" required>
                                    <option value="">-- Select Duration --</option>
                                    <option value="1 Month">1 Month</option>
                                    <option value="3 Months">3 Months</option>
                                    <option value="6 Months">6 Months</option>
                                    <option value="12 Months">12 Months</option>
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label class="control-label">Price</label>
                                <input class="form-control" type="number" name="Price" placeholder="Enter price" min="0" step="0.01" required>
                            </div>

                            <div class="form-group col-md-12">
                                <label class="control-label">Description</label>
                                <textarea name="description" id="description" class="form-control" rows="6"></textarea>
                            </div>

                            <div class="form-group col-md-4 align-self-end">
                                <input type="submit" name="Submit" class="btn btn-primary" value="Submit">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
    <script src="js/plugins/pace.min.js"></script>
    <script src="//js.nicedit.com/nicEdit-latest.js" type="text/javascript"></script>
    <script type="text/javascript">bkLib.onDomLoaded(nicEditors.allTextAreas);</script>
</body>
</html>
