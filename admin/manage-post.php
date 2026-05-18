<?php session_start();
error_reporting(0);
include 'include/config.php';
if (!isset($_SESSION['adminid']) || strlen($_SESSION['adminid']) == 0) {
    header('location:logout.php');
    exit;
}
require_permission('manage_packages');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_verify()) {
    die('Invalid request. Please go back and try again.');
}

if (isset($_POST['delete_package']) && isset($_POST['packageid'])) {
    $packageId = intval($_POST['packageid']);

    // Block deletion if any bookings reference this package
    $checkStmt = $dbh->prepare("SELECT COUNT(*) FROM tblbooking WHERE package_id = :packageId");
    $checkStmt->bindParam(':packageId', $packageId, PDO::PARAM_INT);
    $checkStmt->execute();
    $bookingCount = (int)$checkStmt->fetchColumn();

    if ($bookingCount > 0) {
        $_SESSION['pkg_error'] = "Cannot delete: {$bookingCount} user(s) have booked this package. Remove all their bookings first.";
        header('Location: manage-post.php');
        exit;
    }

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
    <title>Admin | Manage Packages</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

</head>
<body class="app sidebar-mini rtl">
    <?php include 'include/header.php'; ?>
    <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
    <?php include 'include/sidebar.php'; ?>
    <main class="app-content">
        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <div class="tile-body">
                        <h3>Manage Packages</h3>
                        <hr/>
                        <?php if (!empty($_SESSION['pkg_error'])): ?>
                            <div class="alert alert-danger alert-dismissible" role="alert">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <?php echo htmlspecialchars($_SESSION['pkg_error'], ENT_QUOTES, 'UTF-8');
                                      unset($_SESSION['pkg_error']); ?>
                            </div>
                        <?php endif; ?>
                        <table class="table table-hover table-bordered" id="sampleTable">
                            <thead>
                                <tr>
                                    <th>Sr.No</th>
                                    <th>Category</th>
                                    <th>Title</th>
                                    <th>Duration</th>
                                    <th>Price</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $sql = "SELECT t1.id AS packageid, t1.titlename, t1.PackageDuration, t1.Price, t2.category_name
                                    FROM tbladdpackage t1
                                    LEFT JOIN tblcategory t2 ON t1.category = t2.id
                                    ORDER BY t1.id DESC";
                            $query = $dbh->prepare($sql);
                            $query->execute();
                            $results = $query->fetchAll(PDO::FETCH_OBJ);
                            $cnt = 1;
                            foreach ($results as $result) {
                            ?>
                                <tr>
                                    <td><?php echo $cnt; ?></td>
                                    <td><?php echo htmlentities($result->category_name ?: 'Unassigned'); ?></td>
                                    <td><?php echo htmlentities($result->titlename); ?></td>
                                    <td><?php echo htmlentities($result->PackageDuration); ?></td>
                                    <td><?php echo htmlentities($result->Price); ?></td>
                                    <td>
                                        <a href="edit-post.php?pid=<?php echo (int)$result->packageid; ?>" class="btn btn-success btn-sm">Edit</a>
                                        <form method="post" style="display:inline; margin-left:5px;">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="packageid" value="<?php echo (int)$result->packageid; ?>">
                                            <button type="submit" name="delete_package" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Delete this package?');">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php $cnt++; } ?>
                            </tbody>
                        </table>
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
    <script type="text/javascript" src="js/plugins/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="js/plugins/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript">$('#sampleTable').DataTable();</script>
</body>
</html>
