<?php
session_start();
error_reporting(E_ALL);
include 'include/config.php';

if (!isset($_SESSION['adminid']) || strlen($_SESSION['adminid']) == 0) {
    header('location:logout.php');
    exit();
}

$msg = '';
$errormsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_verify()) {
    die('Invalid request. Please go back and try again.');
}

// ADD CATEGORY
if (isset($_POST['submit'])) {
    $categoryName = trim($_POST['category']);

    $check = $dbh->prepare("SELECT id FROM tblcategory WHERE category_name = :catname");
    $check->bindParam(':catname', $categoryName, PDO::PARAM_STR);
    $check->execute();

    if ($check->rowCount() > 0) {
        $errormsg = "Category already exists.";
    } else {
        $ins = $dbh->prepare("INSERT INTO tblcategory (category_name) VALUES (:catname)");
        $ins->bindParam(':catname', $categoryName, PDO::PARAM_STR);
        $ins->execute();

        if ($dbh->lastInsertId()) {
            $msg = "Category added successfully.";
        } else {
            $errormsg = "Something went wrong. Please try again.";
        }
    }
}

// DELETE CATEGORY
if (isset($_GET['del'])) {
    $uid = intval($_GET['del']);
    $sql = "DELETE FROM tblcategory WHERE id = :id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $uid, PDO::PARAM_INT);
    $query->execute();

    echo "<script>alert('Category deleted.');</script>";
    echo "<script>window.location.href='add-package.php'</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin | Manage Categories</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        .app-sidebar { position: fixed; z-index: 1000; }
        .app-content, body {
            background: linear-gradient(rgba(0,0,0,0.88), rgba(0,0,0,0.96)),
                        url('https://images.unsplash.com/photo-1554284126-aa88f22d8b74');
            background-size: cover;
            background-position: center;
            color: #fff;
        }
        .app-content { margin-left: 230px; padding: 20px; }
        .tile {
            position: relative; z-index: 1;
            background: rgba(0,0,0,0.80);
            border: 1px solid rgba(255,102,0,0.45);
            border-radius: 14px;
            box-shadow: 0 0 22px rgba(0,0,0,0.35);
            color: #fff;
            padding: 20px;
            margin-bottom: 20px;
        }
        h3, h4, label, .table, .table th, .table td { color: #fff !important; }
        hr { border-top: 1px solid rgba(255,102,0,0.35); }
        .form-control { background: #1a1a1a; border: 1px solid #333; color: #fff; }
        .form-control:focus {
            background: #1a1a1a; border-color: #ff6600;
            box-shadow: 0 0 5px rgba(255,102,0,0.35); color: #fff;
        }
        .btn-primary { background: #ff6600; border-color: #ff6600; }
        .btn-primary:hover { background: #e65c00; border-color: #e65c00; }
        .btn-danger { background: #c0392b; border-color: #c0392b; }
        .table-bordered, .table-bordered th, .table-bordered td {
            border: 1px solid rgba(255,255,255,0.12) !important;
        }
        thead { background: rgba(255,102,0,0.12); }
    </style>
</head>
<body class="app sidebar-mini rtl">
    <?php include 'include/header.php'; ?>
    <?php include 'include/sidebar.php'; ?>

    <main class="app-content">
        <h3><i class="fa fa-tags"></i> Manage Categories</h3>
        <hr>

        <div class="row">
            <div class="col-md-6">
                <div class="tile">
                    <?php if ($msg) { ?>
                    <div class="alert alert-success"><?php echo htmlentities($msg); ?></div>
                    <?php } ?>
                    <?php if ($errormsg) { ?>
                    <div class="alert alert-danger"><?php echo htmlentities($errormsg); ?></div>
                    <?php } ?>

                    <form method="post">
                        <?php echo csrf_field(); ?>
                        <div class="form-group">
                            <label>Category Name</label>
                            <input class="form-control" type="text" name="category" placeholder="Enter category name" required>
                        </div>
                        <button class="btn btn-primary" type="submit" name="submit">
                            <i class="fa fa-plus"></i> Add Category
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="tile">
                    <h4>All Categories</h4>
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Category Name</th>
                                <th width="120">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $query = $dbh->prepare("SELECT * FROM tblcategory ORDER BY category_name");
                        $query->execute();
                        $cats = $query->fetchAll(PDO::FETCH_OBJ);
                        $cnt = 1;
                        foreach ($cats as $cat) {
                        ?>
                            <tr>
                                <td><?php echo $cnt; ?></td>
                                <td><?php echo htmlentities($cat->category_name); ?></td>
                                <td>
                                    <a href="add-package.php?del=<?php echo (int)$cat->id; ?>"
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Delete this category?')">
                                        <i class="fa fa-trash"></i> Delete
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
    <script src="js/main.js"></script>
</body>
</html>
