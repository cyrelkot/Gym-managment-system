<?php
error_reporting(0);
include 'include/config.php';

if(strlen($_SESSION['uid'])==0){
    header('location:login.php');
    exit;
}

$uid = $_SESSION['uid'];

/* CHECK APPROVAL */
$approved = false;
$check = $dbh->prepare("SELECT status FROM tbluser WHERE id=:uid");
$check->bindParam(':uid',$uid,PDO::PARAM_INT);
$check->execute();
$user = $check->fetch(PDO::FETCH_ASSOC);

if($user && intval($user['status']) === 1){
    $approved = true;
}

/* GET PACKAGES ALREADY BOOKED BY THIS USER */
$bookedQuery = $dbh->prepare("SELECT package_id FROM tblbooking WHERE userid = :uid AND status = 'active'");
$bookedQuery->bindParam(':uid', $uid, PDO::PARAM_INT);
$bookedQuery->execute();
$bookedPackageIds = array_map('intval', $bookedQuery->fetchAll(PDO::FETCH_COLUMN));

/* BOOKING */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_verify()) {
    die('Invalid request. Please go back and try again.');
}

if(isset($_POST['submit'])){

    if(!$approved){
        echo "<script>alert('Your account is pending admin approval.');</script>";
        exit;
    }

    $pid = intval($_POST['pid']);

    $dupCheck = $dbh->prepare("SELECT id FROM tblbooking WHERE userid = :uid AND package_id = :pid AND status = 'active' LIMIT 1");
    $dupCheck->bindParam(':uid', $uid, PDO::PARAM_INT);
    $dupCheck->bindParam(':pid', $pid, PDO::PARAM_INT);
    $dupCheck->execute();

    if($dupCheck->rowCount() > 0){
        echo "<script>alert('You have already booked this package.');</script>";
    } else {
        $sql="INSERT INTO tblbooking (package_id,userid) VALUES (:pid,:uid)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':pid',$pid,PDO::PARAM_INT);
        $query->bindParam(':uid',$uid,PDO::PARAM_INT);
        $query->execute();

        echo "<script>alert('Package booked successfully');</script>";
        echo "<script>window.location.href='booking-history.php'</script>";
    }
}

/* FETCH PACKAGES WITH ENRICHED DATA */
$sql = "SELECT t1.id, t1.titlename, t1.PackageDuration, t1.Price, t1.Description,
    COALESCE(t2.category_name, 'General') AS category_name,
    (SELECT COUNT(*) FROM tblbooking WHERE package_id = t1.id) AS booking_count
FROM tbladdpackage t1
LEFT JOIN tblcategory t2 ON t1.category = t2.id
ORDER BY booking_count DESC, CAST(t1.Price AS DECIMAL) ASC";
$query = $dbh->prepare($sql);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);

/* BADGE LOGIC */
$maxBookings = 0;
$minPrice    = null;
$maxPrice    = null;

if(!empty($results)){
    $maxBookings = (int)$results[0]->booking_count; // sorted DESC
    $prices = array_map(fn($r) => (float)$r->Price, $results);
    $minPrice = min($prices);
    $maxPrice = max($prices);
}

function getBadge($result, $maxBookings, $minPrice, $maxPrice){
    $price      = (float)$result->Price;
    $bookings   = (int)$result->booking_count;
    $priceRange = $maxPrice - $minPrice;

    if($bookings === $maxBookings && $maxBookings > 0){
        return ['label'=>'Most Popular','class'=>'badge-popular'];
    }
    if($price == $minPrice){
        return ['label'=>'Best Value','class'=>'badge-value'];
    }
    if($price == $maxPrice && $priceRange > 0){
        return ['label'=>'Premium','class'=>'badge-premium'];
    }
    return ['label'=>'Recommended','class'=>'badge-rec'];
}

/* DISTINCT CATEGORIES FOR FILTER TABS */
$categories = [];
foreach($results as $r){
    $cat = $r->category_name;
    if(!in_array($cat, $categories)) $categories[] = $cat;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Gym Fitness</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/bootstrap.min.css"/>
<link rel="stylesheet" href="css/user.css"/>
</head>
<body class="index-page">

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
        <h1>BUILD YOUR BODY</h1>
        <p>Train hard. Stay strong.</p>
    </div>
</div>

<p style="color: red; background-color: blue;">CI/CD PIPELINE TEST!</p>


<!-- PRICING -->
<div class="section">

    <div class="title">
        <h2>Fitness Plans</h2>
        <p style="color:#888;margin-top:5px;">Choose the plan that fits your goals</p>
        <div class="section-title-bar"></div>
    </div>

    <div class="container">

        <?php if(!$approved){ ?>
        <div class="approval-banner">
            <svg width="20" height="20" fill="none" stroke="#ff6a00" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            Your account is pending admin approval. You can browse plans but cannot book yet.
        </div>
        <?php } ?>

        <?php if(empty($results)){ ?>
        <div class="empty-state">
            <svg width="60" height="60" fill="none" stroke="#fff" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h4>No fitness plans available</h4>
            <p>Check back later — new plans are coming soon.</p>
        </div>
        <?php } else { ?>

        <!-- FILTER TABS -->
        <div class="filter-tabs">
            <button class="tab active" data-cat="all">All Plans</button>
            <?php foreach($categories as $cat){ ?>
                <button class="tab" data-cat="<?php echo htmlspecialchars($cat); ?>">
                    <?php echo htmlspecialchars($cat); ?>
                </button>
            <?php } ?>
        </div>

        <!-- CARDS -->
        <div id="plans-grid">

        <?php
        $firstPopular = true;
        foreach($results as $result){
            $badge = getBadge($result, $maxBookings, $minPrice, $maxPrice);
            $isFeatured = ($badge['class'] === 'badge-popular');
            $featuredClass = $isFeatured ? ' featured' : '';
            $descId = 'desc-' . $result->id;
        ?>
            <div class="plan-card" data-cat="<?php echo htmlspecialchars($result->category_name); ?>">
                <div class="pricing-item<?php echo $featuredClass; ?>">

                    <!-- BADGE -->
                    <span class="badge <?php echo $badge['class']; ?>"><?php echo $badge['label']; ?></span>

                    <div>
                        <!-- CATEGORY TAG -->
                        <span class="cat-tag"><?php echo htmlspecialchars($result->category_name); ?></span>

                        <!-- TITLE -->
                        <h4><?php echo htmlspecialchars($result->titlename); ?></h4>

                        <!-- PRICE -->
                        <div class="price">
                            &#8369;<?php echo number_format((float)$result->Price, 2); ?>
                        </div>

                        <!-- DURATION -->
                        <div class="duration">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                            <?php echo htmlspecialchars($result->PackageDuration); ?>
                        </div>

                        <!-- DESCRIPTION -->
                        <div class="desc-clamp" id="<?php echo $descId; ?>">
                            <?php echo htmlspecialchars($result->Description); ?>
                        </div>
                        <?php if(strlen($result->Description) > 120){ ?>
                        <button class="read-more" onclick="toggleDesc('<?php echo $descId; ?>', this)">Read more</button>
                        <?php } ?>

                        <!-- ENROLL COUNT -->
                        <?php if((int)$result->booking_count > 0){ ?>
                        <div class="enroll-count">
                            <?php echo (int)$result->booking_count; ?> member<?php echo ((int)$result->booking_count !== 1 ? 's' : ''); ?> enrolled
                        </div>
                        <?php } ?>
                    </div>

                    <!-- CTA BUTTON -->
                    <div class="btn-container">
                        <?php if(!$approved){ ?>
                            <button class="btn-book-disabled" disabled>Awaiting Approval</button>
                        <?php } elseif(in_array((int)$result->id, $bookedPackageIds)){ ?>
                            <button class="btn-book-disabled" disabled>Already Booked</button>
                        <?php } else { ?>
                            <form method="post">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="pid" value="<?php echo (int)$result->id; ?>">
                                <input type="submit" name="submit" class="btn-book" value="Book Now">
                            </form>
                        <?php } ?>
                    </div>

                </div>
            </div>

        <?php } ?>

        </div>
        <?php } ?>

    </div>
</div>

<!-- FOOTER -->
<div class="footer">
    &copy; 2026 Gym Management System
</div>

<script>
// Category filter tabs
document.querySelectorAll('.tab').forEach(function(tab){
    tab.addEventListener('click', function(){
        document.querySelectorAll('.tab').forEach(function(t){ t.classList.remove('active'); });
        this.classList.add('active');
        var cat = this.getAttribute('data-cat');
        document.querySelectorAll('.plan-card').forEach(function(card){
            if(cat === 'all' || card.getAttribute('data-cat') === cat){
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    });
});

// Read more toggle
function toggleDesc(id, btn){
    var el = document.getElementById(id);
    if(el.style.webkitLineClamp === 'unset' || el.style.overflow === 'visible'){
        el.style.webkitLineClamp = '';
        el.style.overflow = 'hidden';
        el.style.display = '-webkit-box';
        btn.textContent = 'Read more';
    } else {
        el.style.webkitLineClamp = 'unset';
        el.style.overflow = 'visible';
        el.style.display = 'block';
        btn.textContent = 'Read less';
    }
}
</script>

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
