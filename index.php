<?php
session_start();
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

/* CHECK IF USER HAS BOOKING */
$hasBooking = false;
$checkBooking = $dbh->prepare("SELECT id FROM tblbooking WHERE userid = :uid LIMIT 1");
$checkBooking->bindParam(':uid', $uid, PDO::PARAM_INT);
$checkBooking->execute();

if($checkBooking->rowCount() > 0){
    $hasBooking = true;
}

/* BOOKING */
if(isset($_POST['submit'])){

    if(!$approved){
        echo "<script>alert('Your account is pending admin approval.');</script>";
        exit;
    }

    $pid=$_POST['pid'];

    $sql="INSERT INTO tblbooking (package_id,userid) VALUES (:pid,:uid)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':pid',$pid,PDO::PARAM_STR);
    $query->bindParam(':uid',$uid,PDO::PARAM_STR);
    $query->execute();

    echo "<script>alert('Package booked successfully');</script>";
    echo "<script>window.location.href='booking-history.php'</script>";
}

/* FETCH PACKAGES WITH ENRICHED DATA */
$sql = "SELECT t1.id, t1.titlename, t1.PackageDuratiobn, t1.Price, t1.Description,
    COALESCE(t2.category_name, 'General') AS category_name,
    COALESCE(t3.PackageName, '') AS PackageName,
    (SELECT COUNT(*) FROM tblbooking WHERE package_id = t1.id) AS booking_count
FROM tbladdpackage t1
LEFT JOIN tblcategory t2 ON t1.category = t2.id
LEFT JOIN tblpackage t3 ON t1.PackageType = t3.id
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
<style>

body{
    margin:0;
    font-family:Segoe UI, sans-serif;
    background:#000;
    color:#fff;
}

/* NAVBAR */
.navbar{
    display:flex;
    justify-content:center;
    gap:40px;
    padding:20px;
    background:#000;
    border-bottom:2px solid #ff6a00;
    position:sticky;
    top:0;
    z-index:100;
}
.navbar a{
    color:#fff;
    text-decoration:none;
    font-weight:600;
}
.navbar a:hover{ color:#ff6a00; }

/* HERO */
.hero{
    height:55vh;
    background:url('https://images.unsplash.com/photo-1554284126-aa88f22d8b74?auto=format&fit=crop&w=1600&q=80') center/cover no-repeat;
    display:flex;
    align-items:center;
    justify-content:center;
    position:relative;
}
.hero::before{
    content:"";
    position:absolute;
    width:100%;height:100%;
    background:rgba(0,0,0,0.7);
}
.hero-content{ position:relative; text-align:center; }
.hero h1{ font-size:55px; color:#ff6a00; }

/* SECTION */
.section{ padding:60px 20px; }
.title{ text-align:center; margin-bottom:20px; }
.section-title-bar {
    width: 50px;
    height: 3px;
    background: #ff6a00;
    margin: 8px auto 30px;
    border-radius: 2px;
}

/* APPROVAL BANNER */
.approval-banner{
    background:#1a1200;
    border:1px solid #ff6a00;
    border-radius:8px;
    padding:14px 20px;
    margin:0 auto 30px;
    max-width:900px;
    color:#ffcc66;
    font-size:15px;
    display:flex;
    align-items:center;
    gap:10px;
}
.approval-banner svg{ flex-shrink:0; }

/* FILTER TABS */
.filter-tabs{
    display:flex;
    flex-wrap:wrap;
    gap:10px;
    justify-content:center;
    margin-bottom:35px;
}
.tab{
    background:#111;
    color:#ccc;
    border:1px solid #333;
    border-radius:20px;
    padding:8px 20px;
    cursor:pointer;
    font-size:14px;
    font-weight:600;
    transition:all .2s;
}
.tab:hover{ border-color:#ff6a00; color:#ff6a00; }
.tab.active{
    background:#ff6a00;
    color:#000;
    border-color:#ff6a00;
}

/* GRID */
#plans-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 24px;
    margin: 0;
}
@media (max-width: 1199px) { #plans-grid { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 767px)  { #plans-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 480px)  { #plans-grid { grid-template-columns: 1fr; } }

.plan-card { display: flex; }

/* CARD */
.pricing-item{
    background:#111;
    border:1px solid #222;
    border-top:3px solid #ff6a00;
    padding:25px;
    border-radius:15px;
    width:100%;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    position:relative;
    transition:transform .2s, box-shadow .2s;
}
.pricing-item:hover{
    transform:translateY(-4px);
    box-shadow:0 8px 25px rgba(255,106,0,.25);
}
.pricing-item.featured{
    border-top:3px solid #ff6a00;
    box-shadow:0 0 0 2px #ff6a00, 0 6px 30px rgba(255,106,0,.4);
}
.pricing-item h4{ color:#ff6a00; margin-bottom:4px; }

/* BADGE */
.badge{
    position:absolute;
    top:14px; right:14px;
    padding:4px 10px;
    border-radius:20px;
    font-size:11px;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.5px;
}
.badge-popular{ background:#ff6a00; color:#000; }
.badge-value{   background:#00cc66; color:#000; }
.badge-premium{ background:#c0a060; color:#000; }
.badge-rec{     background:#4da6ff; color:#000; }

/* CATEGORY TAG */
.cat-tag{
    display:inline-block;
    background:#1e1e1e;
    border:1px solid #333;
    color:#aaa;
    border-radius:10px;
    font-size:11px;
    padding:2px 9px;
    margin-bottom:8px;
}
.pkg-type{
    font-size:12px;
    color:#888;
    margin-bottom:10px;
}

/* PRICE */
.price{
    font-size:28px;
    font-weight:bold;
    color:#fff;
}
.price span{ font-size:14px; color:#aaa; font-weight:normal; }

/* DURATION */
.duration{
    display:flex;
    align-items:center;
    gap:6px;
    font-size:13px;
    color:#bbb;
    margin:6px 0;
}
.duration svg{ flex-shrink:0; }

/* DESCRIPTION */
.desc-clamp{
    font-size:13px;
    color:#ccc;
    display:-webkit-box;
    -webkit-line-clamp:3;
    -webkit-box-orient:vertical;
    overflow:hidden;
    margin:8px 0 4px;
}
.read-more{
    font-size:12px;
    color:#ff6a00;
    background:none;
    border:none;
    padding:0;
    cursor:pointer;
    margin-bottom:10px;
}

/* ENROLL COUNT */
.enroll-count{
    font-size:12px;
    color:#555;
    margin-bottom:10px;
}

/* BUTTON */
.btn-container{ margin-top:auto; }
.btn-book{
    background:#ff6a00;
    color:#000;
    padding:10px 20px;
    border:none;
    border-radius:8px;
    font-weight:bold;
    width:100%;
    cursor:pointer;
    font-size:14px;
    transition:background .2s;
}
.btn-book:hover{ background:#e05a00; }
.btn-book-outline{
    background:transparent;
    color:#ff6a00;
    border:2px solid #ff6a00;
    padding:9px 20px;
    border-radius:8px;
    font-weight:bold;
    width:100%;
    display:block;
    text-align:center;
    text-decoration:none;
    font-size:14px;
    transition:all .2s;
}
.btn-book-outline:hover{
    background:#ff6a00;
    color:#000;
    text-decoration:none;
}
.btn-book-disabled{
    background:#2a2a2a;
    color:#555;
    border:1px solid #333;
    padding:10px 20px;
    border-radius:8px;
    font-weight:bold;
    width:100%;
    font-size:14px;
    cursor:not-allowed;
}

/* EMPTY STATE */
.empty-state{
    text-align:center;
    padding:60px 20px;
    color:#555;
}
.empty-state svg{ margin-bottom:20px; opacity:.4; }

/* FOOTER */
.footer{ text-align:center; padding:20px; color:#777; }

</style>
</head>
<body>

<!-- NAVBAR -->
<div class="navbar">
    <a href="index.php">Home</a>
    <a href="about.php">About</a>
    <a href="contact.php">Contact</a>
    <?php if($hasBooking){ ?>
        <a href="booking-history.php">Booking History</a>
    <?php } ?>
    <a href="logout.php">Logout</a>
</div>

<!-- HERO -->
<div class="hero">
    <div class="hero-content">
        <h1>BUILD YOUR BODY</h1>
        <p>Train hard. Stay strong.</p>
    </div>
</div>

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

                        <!-- PACKAGE TYPE -->
                        <?php if($result->PackageName){ ?>
                            <div class="pkg-type"><?php echo htmlspecialchars($result->PackageName); ?></div>
                        <?php } ?>

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
                            <?php echo htmlspecialchars($result->PackageDuratiobn); ?>
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
                        <?php } elseif($hasBooking){ ?>
                            <a href="booking-history.php" class="btn-book-outline">View My Booking</a>
                        <?php } else { ?>
                            <form method="post">
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

</body>
</html>
