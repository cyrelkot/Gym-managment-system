<?php
error_reporting(0);
require_once('include/config.php');

if(strlen($_SESSION["uid"])==0){   
    header('location:login.php');
    exit;
}

$uid=$_SESSION['uid'];

/* CHECK APPROVAL */
$approved = false;
$check = $dbh->prepare("SELECT status FROM tbluser WHERE id=:uid");
$check->bindParam(':uid',$uid,PDO::PARAM_INT);
$check->execute();
$user = $check->fetch(PDO::FETCH_ASSOC);

if($user && intval($user['status']) === 1){
    $approved = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Booking History</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="css/user.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"/>
<link rel="icon" type="image/png" href="icon-fonts/gym-logo.png">
</head>

<body class="booking-history-page">

<!-- NAVBAR -->
<div class="navbar">
    <div class="logo">GYM</div>

    <div class="nav-center">
        <a href="index.php">Home</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
        <a href="booking-history.php">Booking History</a>
    </div>
    <div class="nav-right">
        <div class="user-menu">
            <div class="user-trigger">
                <span class="user-avatar"><?php echo htmlspecialchars(strtoupper(substr($_SESSION['fname'], 0, 1)), ENT_QUOTES, 'UTF-8'); ?></span>
                <span class="user-name"><?php echo htmlspecialchars($_SESSION['fname'], ENT_QUOTES, 'UTF-8'); ?></span>
                <span class="user-caret">&#9660;</span>
            </div>
            <div class="user-dropdown">
                <a href="profile.php">Profile</a>
                <a href="changepassword.php">Change Password</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </div>
</div>

<!-- HERO -->
<div class="hero">
    <div class="hero-content">
        <h1>Booking History</h1>
        <p>Track your fitness journey</p>
    </div>
</div>

<div class="container">
<div class="card">

<?php if(!$approved){ ?>
    <div class="alert">
        Your account is pending admin approval.
    </div>
<?php } else { ?>

<?php
$sql="SELECT t1.id as bookingid,
t1.booking_date,
t2.titlename,
t2.PackageDuration,
t2.Price,
t2.Description,
t4.category_name,
t1.status
FROM tblbooking t1
LEFT JOIN tbladdpackage t2 ON t1.package_id = t2.id
LEFT JOIN tblcategory t4 ON t2.category = t4.id
WHERE t1.userid=:uid";

$query= $dbh->prepare($sql);
$query->bindParam(':uid',$uid,PDO::PARAM_INT);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
?>

<?php if(empty($results)){ ?>
    <div class="alert">
        No bookings yet.
    </div>
<?php } else { ?>

<table>
<thead>
<tr>
<th>#</th>
<th>Date</th>
<th>Title</th>
<th>Duration</th>
<th>Price</th>
<th>Category</th>
<th>Status</th>
<th>Action</th>
</tr>
</thead>

<tbody>

<?php foreach($results as $row){ ?>
<tr>
<td><?php echo $cnt++; ?></td>
<td><?php echo htmlspecialchars($row->booking_date, ENT_QUOTES, 'UTF-8'); ?></td>
<td><?php echo htmlspecialchars($row->titlename, ENT_QUOTES, 'UTF-8'); ?></td>
<td><?php echo htmlspecialchars($row->PackageDuration, ENT_QUOTES, 'UTF-8'); ?></td>
<td>₱<?php echo htmlspecialchars($row->Price, ENT_QUOTES, 'UTF-8'); ?></td>
<td><?php echo htmlspecialchars($row->category_name, ENT_QUOTES, 'UTF-8'); ?></td>
<td>
<?php if($row->status === 'active'): ?>
    <span style="color:#28a745;font-weight:600;">Active</span>
<?php else: ?>
    <span style="color:#6c757d;font-weight:600;">Expired</span>
<?php endif; ?>
</td>
<td>
<a href="booking-details.php?bookingid=<?php echo (int)$row->bookingid; ?>">
<button class="btn-view">View</button>
</a>
</td>
</tr>
<?php } ?>

</tbody>
</table>

<?php } } ?>

</div>
</div>

<!-- FOOTER -->
<footer class="footer">
    <div class="footer-brand">GYM</div>
    <div class="footer-tagline">Train harder. Live better.</div>
    <div class="footer-copy">© 2026 Gym Management System. All rights reserved.</div>
</footer>

<script>
(function() {
    var trigger = document.querySelector('.user-trigger');
    if (!trigger) return;
    var menu = trigger.closest('.user-menu');
    trigger.addEventListener('click', function(e) {
        e.stopPropagation();
        menu.classList.toggle('open');
    });
    document.addEventListener('click', function() {
        menu.classList.remove('open');
    });
})();
</script>

</body>
</html>