<?php
session_start();
error_reporting(0);
include 'include/config.php';

if (!isset($_SESSION['adminid']) || strlen($_SESSION['adminid']) == 0) {
    header('location:logout.php');
    exit;
}

$bookingId = isset($_GET['bookingid']) ? intval($_GET['bookingid']) : 0;
$err = '';
$success = '';

if ($bookingId <= 0) {
    header('Location: partial-payment-bookings.php');
    exit;
}

// Handle update
if (isset($_POST['update_booking'])) {
    $newBookingDate = isset($_POST['bookingDate']) ? trim($_POST['bookingDate']) : '';
    $newUserId = isset($_POST['userId']) ? intval($_POST['userId']) : 0;
    $newPackageId = isset($_POST['packageId']) ? intval($_POST['packageId']) : 0;
    $newPaymentType = isset($_POST['paymentType']) ? trim($_POST['paymentType']) : '';
    $newPaymentAmount = isset($_POST['paymentAmount']) ? floatval($_POST['paymentAmount']) : 0;

    // Get package price for validation
    $priceStmt = $dbh->prepare("SELECT Price FROM tbladdpackage WHERE id = :packageId");
    $priceStmt->bindParam(':packageId', $newPackageId, PDO::PARAM_INT);
    $priceStmt->execute();
    $packageRow = $priceStmt->fetch(PDO::FETCH_OBJ);
    $packagePrice = $packageRow ? floatval($packageRow->Price) : 0;

    if ($newPaymentType === 'Full Payment') {
        $newPaymentAmount = $packagePrice;
    }

    if ($newPaymentAmount < 0) {
        $err = 'Payment amount cannot be negative.';
    } elseif ($newPaymentAmount > $packagePrice) {
        $err = 'Payment cannot exceed package amount.';
    } else {
        $update = $dbh->prepare("UPDATE tblbooking
            SET booking_date = :bookingDate,
                userid = :userId,
                package_id = :packageId,
                paymentType = :paymentType,
                payment = :payment
            WHERE id = :bookingId");
        $update->bindParam(':bookingDate', $newBookingDate, PDO::PARAM_STR);
        $update->bindParam(':userId', $newUserId, PDO::PARAM_INT);
        $update->bindParam(':packageId', $newPackageId, PDO::PARAM_INT);
        $update->bindParam(':paymentType', $newPaymentType, PDO::PARAM_STR);
        $update->bindParam(':payment', $newPaymentAmount, PDO::PARAM_STR);
        $update->bindParam(':bookingId', $bookingId, PDO::PARAM_INT);

        if ($update->execute()) {
            $success = 'Booking updated successfully.';
        } else {
            $err = 'Failed to update booking.';
        }
    }
}

// Load booking
$sql = "SELECT t1.id as bookingid, t1.booking_date as bookingdate, t1.paymentType as paymentType, t1.payment as paymentAmount,
        t1.userid as userId, t1.package_id as packageId,
        t3.fname as Name, t3.email as email,
        t2.titlename as title, t2.PackageDuratiobn as PackageDuratiobn, t2.Price as Price,
        t4.category_name as category_name, t5.PackageName as PackageName
        FROM tblbooking as t1
        LEFT JOIN tbladdpackage as t2 ON t1.package_id = t2.id
        LEFT JOIN tbluser as t3 ON t1.userid = t3.id
        LEFT JOIN tblcategory as t4 ON t2.category = t4.id
        LEFT JOIN tblpackage as t5 ON t2.PackageType = t5.id
        WHERE t1.id = :bookingId";

$query = $dbh->prepare($sql);
$query->bindParam(':bookingId', $bookingId, PDO::PARAM_INT);
$query->execute();
$booking = $query->fetch(PDO::FETCH_OBJ);

if (!$booking) {
    header('Location: partial-payment-bookings.php');
    exit;
}

// Load users for selection
$userStmt = $dbh->prepare("SELECT id, fname, email FROM tbluser ORDER BY fname");
$userStmt->execute();
$users = $userStmt->fetchAll(PDO::FETCH_OBJ);

// Load packages for selection
$packageStmt = $dbh->prepare("SELECT t2.id, t2.Price, t5.PackageName, t2.titlename FROM tbladdpackage t2
    LEFT JOIN tblpackage t5 ON t2.PackageType = t5.id
    ORDER BY t5.PackageName, t2.titlename");
$packageStmt->execute();
$packages = $packageStmt->fetchAll(PDO::FETCH_OBJ);

// Calculate remaining balance
$paymentSumStmt = $dbh->prepare("SELECT SUM(payment) as totalPaid FROM tblpayment WHERE bookingID = :bookingId");
$paymentSumStmt->bindParam(':bookingId', $bookingId, PDO::PARAM_INT);
$paymentSumStmt->execute();
$paidRow = $paymentSumStmt->fetch(PDO::FETCH_OBJ);
$totalPaidRecords = $paidRow && $paidRow->totalPaid ? floatval($paidRow->totalPaid) : 0;
$bookingPayment = $booking->paymentAmount ? floatval($booking->paymentAmount) : 0;

// Prefer payment history if present; if no history fallback to booking payment field.
$totalPaid = $totalPaidRecords > 0 ? $totalPaidRecords : $bookingPayment;
$remainingBalance = $booking->Price - $totalPaid;
if ($remainingBalance < 0) {
    $remainingBalance = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin | Edit Booking</title>
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
                    <h3>Edit Booking #<?php echo htmlentities($booking->bookingid); ?></h3>
                    <?php if ($err) { ?>
                        <div class="alert alert-danger"><?php echo htmlentities($err); ?></div>
                    <?php } ?>
                    <?php if ($success) { ?>
                        <div class="alert alert-success"><?php echo htmlentities($success); ?></div>
                    <?php } ?>
                    <form method="post">
                        <div class="form-group">
                            <label>Booking Date</label>
                            <input type="date" name="bookingDate" class="form-control" value="<?php echo htmlentities(date('Y-m-d', strtotime($booking->bookingdate))); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>User</label>
                            <select name="userId" class="form-control" required>
                                <option value="">-- Select User --</option>
                                <?php foreach ($users as $user) { ?>
                                    <option value="<?php echo $user->id; ?>" <?php echo $user->id == $booking->userId ? 'selected' : ''; ?>>
                                        <?php echo htmlentities($user->fname . ' (' . $user->email . ')'); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Package</label>
                            <select name="packageId" id="packageId" class="form-control" required>
                                <option value="" data-price="0">-- Select Package --</option>
                                <?php foreach ($packages as $package) { ?>
                                    <option value="<?php echo $package->id; ?>" data-price="<?php echo floatval($package->Price); ?>" <?php echo $package->id == $booking->packageId ? 'selected' : ''; ?>>
                                        <?php echo htmlentities($package->PackageName . ' / ' . $package->titlename); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Package Price</label>
                            <input type="text" id="packagePrice" class="form-control" value="<?php echo htmlentities($booking->Price); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Payment Type</label>
                            <select name="paymentType" class="form-control">
                                <option value="" <?php echo $booking->paymentType === '' ? 'selected' : ''; ?>>Not paid</option>
                                <option value="Partial Payment" <?php echo $booking->paymentType === 'Partial Payment' ? 'selected' : ''; ?>>Partial Payment</option>
                                <option value="Full Payment" <?php echo $booking->paymentType === 'Full Payment' ? 'selected' : ''; ?>>Full Payment</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Payment Amount</label>
                            <input type="text" name="paymentAmount" class="form-control" value="<?php echo htmlentities($booking->paymentAmount); ?>" placeholder="Enter amount">
                        </div>
                        <div class="form-group">
                            <label>Remaining Balance</label>
                            <input type="text" id="remainingBalance" class="form-control" value="<?php echo htmlentities($remainingBalance); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <button type="submit" name="update_booking" class="btn btn-primary">Update</button>
                            <a href="partial-payment-bookings.php" class="btn btn-secondary">Back</a>
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
<script>
    $(document).ready(function() {
        function updateRemaining() {
            var price = parseFloat($('#packageId option:selected').data('price')) || 0;
            var payment = parseFloat($('input[name="paymentAmount"]').val()) || 0;
            var remaining = price - payment;
            if (remaining < 0) {
                remaining = 0;
            }
            $('#packagePrice').val(price.toFixed(2));
            $('#remainingBalance').val(remaining.toFixed(2));
        }

        $('#packageId').on('change', updateRemaining);
        $('input[name="paymentAmount"]').on('input', updateRemaining);
        updateRemaining();
    });
</script>
</body>
</html>