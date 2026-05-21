<?php
error_reporting(E_ALL);
include 'include/config.php';

if (!isset($_SESSION['adminid']) || strlen($_SESSION['adminid']) == 0) {
    header('location:logout.php');
    exit();
}
require_permission('manage_admins');

$currentAdminId = (int)$_SESSION['adminid'];
$msg = '';
$errormsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_verify()) {
    die('Invalid request. Please go back and try again.');
}

// Add new admin
if (isset($_POST['add_admin'])) {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $mobile   = trim($_POST['mobile'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? 'staff';

    if (!in_array($role, ['super_admin', 'staff'], true)) {
        $errormsg = 'Invalid role selected.';
    } elseif ($name === '' || $email === '' || $password === '') {
        $errormsg = 'Name, email, and password are required.';
    } else {
        // Check email uniqueness
        $chk = $dbh->prepare("SELECT id FROM tbladmin WHERE email = :email LIMIT 1");
        $chk->bindParam(':email', $email, PDO::PARAM_STR);
        $chk->execute();
        if ($chk->fetch()) {
            $errormsg = 'An admin with that email already exists.';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $ins  = $dbh->prepare("INSERT INTO tbladmin (name, email, mobile, password, role) VALUES (:name, :email, :mobile, :password, :role)");
            $ins->bindParam(':name',     $name,   PDO::PARAM_STR);
            $ins->bindParam(':email',    $email,  PDO::PARAM_STR);
            $ins->bindParam(':mobile',   $mobile, PDO::PARAM_STR);
            $ins->bindParam(':password', $hash,   PDO::PARAM_STR);
            $ins->bindParam(':role',     $role,   PDO::PARAM_STR);
            if ($ins->execute()) {
                $msg = 'Admin account created successfully.';
            } else {
                $errormsg = 'Failed to create admin account.';
            }
        }
    }
}

// Change role
if (isset($_POST['change_role'])) {
    $targetId = (int)($_POST['target_id'] ?? 0);
    $newRole  = $_POST['new_role'] ?? '';

    if ($targetId === $currentAdminId) {
        $errormsg = 'You cannot change your own role.';
    } elseif (!in_array($newRole, ['super_admin', 'staff'], true)) {
        $errormsg = 'Invalid role selected.';
    } else {
        $upd = $dbh->prepare("UPDATE tbladmin SET role = :role WHERE id = :id");
        $upd->bindParam(':role', $newRole, PDO::PARAM_STR);
        $upd->bindParam(':id',   $targetId, PDO::PARAM_INT);
        if ($upd->execute()) {
            $msg = 'Role updated successfully.';
        } else {
            $errormsg = 'Failed to update role.';
        }
    }
}

// Delete admin
if (isset($_POST['delete_admin'])) {
    $targetId = (int)($_POST['target_id'] ?? 0);
    if ($targetId === $currentAdminId) {
        $errormsg = 'You cannot delete your own account.';
    } elseif ($targetId === 0) {
        $errormsg = 'Invalid admin selected.';
    } else {
        $del = $dbh->prepare("DELETE FROM tbladmin WHERE id = :id");
        $del->bindParam(':id', $targetId, PDO::PARAM_INT);
        if ($del->execute()) {
            $msg = 'Admin account deleted.';
        } else {
            $errormsg = 'Failed to delete admin account.';
        }
    }
}

// Fetch all admins
$admins = $dbh->query("SELECT id, name, email, mobile, role, create_date FROM tbladmin ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Admin | Manage Admins</title>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/main.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body class="app sidebar-mini rtl">

<?php include 'include/header.php'; ?>
<?php include 'include/sidebar.php'; ?>

<main class="app-content">

  <div class="app-title">
    <h1><i class="fa fa-user-secret"></i> Manage Admins</h1>
  </div>

  <?php if ($msg): ?>
    <div class="alert alert-success"><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></div>
  <?php endif; ?>
  <?php if ($errormsg): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($errormsg, ENT_QUOTES, 'UTF-8') ?></div>
  <?php endif; ?>

  <div class="row">

    <!-- Admin List -->
    <div class="col-md-8">
      <div class="tile">
        <div class="tile-title-w-btn">
          <h3 class="title">Admin Accounts</h3>
        </div>
        <div class="tile-body">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Role</th>
                <th>Created</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($admins as $adm): ?>
              <tr>
                <td><?= (int)$adm['id'] ?></td>
                <td><?= htmlspecialchars($adm['name'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($adm['email'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($adm['mobile'], ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                  <span class="badge <?= $adm['role'] === 'super_admin' ? 'badge-danger' : 'badge-info' ?>">
                    <?= $adm['role'] === 'super_admin' ? 'Super Admin' : 'Staff' ?>
                  </span>
                </td>
                <td><?= htmlspecialchars(date('M d, Y', strtotime($adm['create_date'])), ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                  <?php if ((int)$adm['id'] !== $currentAdminId): ?>
                    <!-- Change Role -->
                    <form method="post" style="display:inline-block; margin-bottom:4px;">
                      <?= csrf_field() ?>
                      <input type="hidden" name="target_id" value="<?= (int)$adm['id'] ?>">
                      <select name="new_role" class="form-control form-control-sm" style="display:inline-block;width:auto;vertical-align:middle;">
                        <option value="super_admin" <?= $adm['role'] === 'super_admin' ? 'selected' : '' ?>>Super Admin</option>
                        <option value="staff" <?= $adm['role'] === 'staff' ? 'selected' : '' ?>>Staff</option>
                      </select>
                      <button type="submit" name="change_role" class="btn btn-sm btn-warning" style="vertical-align:middle;">Set Role</button>
                    </form>
                    <!-- Delete -->
                    <form method="post" style="display:inline-block;" onsubmit="return confirm('Delete this admin account? This cannot be undone.');">
                      <?= csrf_field() ?>
                      <input type="hidden" name="target_id" value="<?= (int)$adm['id'] ?>">
                      <button type="submit" name="delete_admin" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                  <?php else: ?>
                    <span class="text-muted" style="font-size:12px;">You</span>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Add New Admin -->
    <div class="col-md-4">
      <div class="tile">
        <div class="tile-title-w-btn">
          <h3 class="title">Add New Admin</h3>
          <button type="button" id="toggleAddForm" class="btn btn-primary btn-sm">
            <i class="fa fa-plus"></i> Add New Admin
          </button>
        </div>
        <div class="tile-body collapse <?php echo ($errormsg || $msg) ? 'show' : ''; ?>" id="addAdminForm">
          <form method="post">
            <?= csrf_field() ?>
            <div class="form-group">
              <label>Name</label>
              <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
              <label>Email</label>
              <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
              <label>Mobile</label>
              <input type="text" name="mobile" class="form-control">
            </div>
            <div class="form-group">
              <label>Password</label>
              <div class="pass-wrapper">
                <input type="password" name="password" id="adminPassword" class="form-control" required>
                <span id="adminPasswordToggle" onclick="togglePass('adminPassword', this)"><i class="fa fa-eye"></i></span>
              </div>
            </div>
            <div class="form-group">
              <label>Role</label>
              <select name="role" class="form-control">
                <option value="staff">Staff</option>
                <option value="super_admin">Super Admin</option>
              </select>
            </div>
            <button type="submit" name="add_admin" class="btn btn-primary btn-block">Create Admin</button>
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
<script>
function togglePass(id, btn) {
  var x = document.getElementById(id);
  var showing = x.type === 'password';
  x.type = showing ? 'text' : 'password';
  btn.querySelector('i').className = showing ? 'fa fa-eye-slash' : 'fa fa-eye';
}

document.getElementById('toggleAddForm').addEventListener('click', function () {
  var panel = document.getElementById('addAdminForm');
  if (panel.classList.contains('show')) {
    panel.classList.remove('show');
  } else {
    panel.classList.add('show');
  }
});
</script>

</body>
</html>
