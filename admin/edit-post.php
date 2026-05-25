<?php
error_reporting(0);
include 'include/config.php';
if (!isset($_SESSION['adminid']) || strlen($_SESSION['adminid']) == 0) {
    header('location:logout.php');
    exit;
}
require_permission('manage_packages');

$pid = intval($_GET['pid'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_verify()) {
    die('Invalid request. Please go back and try again.');
}

if (isset($_POST['Submit'])) {
    $category    = $_POST['category'];
    $titlename   = $_POST['titlename'];
    $duration    = $_POST['duration'];
    $Price       = $_POST['Price'];
    $description = $_POST['description'];

    // Compare against current DB values
    $chkStmt = $dbh->prepare("SELECT category, titlename, PackageDuration, Price, Description FROM tbladdpackage WHERE id = :pid");
    $chkStmt->bindParam(':pid', $pid, PDO::PARAM_INT);
    $chkStmt->execute();
    $current = $chkStmt->fetch(PDO::FETCH_OBJ);

    $unchanged = $current &&
        (string)$current->category        === (string)$category &&
        trim($current->titlename)         === trim($titlename) &&
        (string)$current->PackageDuration === (string)$duration &&
        (string)$current->Price           === (string)$Price &&
        trim($current->Description)       === trim($description);

    if ($unchanged) {
        echo "<script>alert('No changes were made.');</script>";
        echo "<script>window.location.href='manage-post.php';</script>";
    } else {
        $sql = "UPDATE tbladdpackage
                SET category=:category, titlename=:titlename, PackageDuration=:duration,
                    Price=:Price, Description=:description
                WHERE id=:pid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':category',    $category,    PDO::PARAM_STR);
        $query->bindParam(':titlename',   $titlename,   PDO::PARAM_STR);
        $query->bindParam(':duration',    $duration,    PDO::PARAM_STR);
        $query->bindParam(':Price',       $Price,       PDO::PARAM_STR);
        $query->bindParam(':description', $description, PDO::PARAM_STR);
        $query->bindParam(':pid',         $pid,         PDO::PARAM_INT);
        $query->execute();

        echo "<script>alert('Package updated successfully.');</script>";
        echo "<script>window.location.href='manage-post.php';</script>";
    }
}

// Fetch existing record
$sql = "SELECT t1.*, t2.category_name
        FROM tbladdpackage t1
        LEFT JOIN tblcategory t2 ON t1.category = t2.id
        WHERE t1.id = :pid";
$query = $dbh->prepare($sql);
$query->bindParam(':pid', $pid, PDO::PARAM_INT);
$query->execute();
$result = $query->fetch(PDO::FETCH_OBJ);

if (!$result) {
    echo "<script>alert('Package not found.');</script>";
    echo "<script>window.location.href='manage-post.php';</script>";
    exit;
}

$durations = ['1 Month', '3 Months', '6 Months', '12 Months'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin | Edit Package</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" type="image/png" href="../icon-fonts/gym-logo.png">

</head>
<body class="app sidebar-mini rtl">
    <?php include 'include/header.php'; ?>
    <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
    <?php include 'include/sidebar.php'; ?>
    <main class="app-content">
        <h3>Edit Package</h3>
        <hr/>
        <div class="row">
            <div class="col-md-12">
                <div class="tile">
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
                                        $sel = ($cat['id'] == $result->category) ? 'selected' : '';
                                        echo "<option value='" . (int)$cat['id'] . "' $sel>" . htmlspecialchars($cat['category_name']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label class="control-label">Title Name</label>
                                <input class="form-control" name="titlename" type="text"
                                       value="<?php echo htmlspecialchars($result->titlename, ENT_QUOTES); ?>" required>
                            </div>

                            <div class="form-group col-md-6">
                                <label class="control-label">Package Duration</label>
                                <select name="duration" class="form-control" required>
                                    <option value="">-- Select Duration --</option>
                                    <?php foreach ($durations as $d) {
                                        $sel = ($result->PackageDuration === $d) ? 'selected' : '';
                                        echo "<option value='$d' $sel>$d</option>";
                                    } ?>
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label class="control-label">Price</label>
                                <input class="form-control" type="number" name="Price" min="0" step="0.01"
                                       value="<?php echo htmlspecialchars($result->Price, ENT_QUOTES); ?>" required>
                            </div>

                            <div class="form-group col-md-12">
                                <label class="control-label">Description</label>
                                <textarea name="description" id="description" class="form-control" rows="6"><?php
                                    echo htmlspecialchars($result->Description, ENT_QUOTES);
                                ?></textarea>
                            </div>

                            <div class="form-group col-md-4 align-self-end">
                                <input type="submit" name="Submit" class="btn btn-primary" value="Update">
                                <a href="manage-post.php" class="btn btn-default" style="margin-left:8px;">Cancel</a>
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
