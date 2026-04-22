	<header class="header-section">
		<div class="header-top">
			<div class="row m-0">
				<div class="col-md-6 d-none d-md-block p-0"></div>
				<div class="col-md-6 text-left text-md-right p-0">
					<?php if(strlen($_SESSION['uid'])==0): ?>
					<div class="header-info d-none d-md-inline-flex">
						<i class="material-icons">account_circle</i>
						<a href="login.php"><p>Login</p></a>
					</div>
					<?php else :?>
					<div class="header-info d-none d-md-inline-flex header-profile-menu">
						<a href="#" class="header-profile-toggle">
							<span>Profile</span>
							<i class="material-icons profile-caret">arrow_drop_down</i>
						</a>
						<ul class="header-profile-submenu">
							<li><a href="profile.php">My Profile</a></li>
							<li><a href="changepassword.php">Change Password</a></li>
							<li><a href="logout.php">Logout</a></li>
						</ul>
					</div>
					<?php endif;?>
				</div>
			</div>
		</div>
		<div class="header-bottom">
			<a href="index.php" class="site-logo" style="color:#fff; font-weight:bold; font-size:26px;">
				GYM MS<br />
				<small style="margin-top:-4%;">Gym Management System</small>
			</a>
			<div class="container">
				<ul class="main-menu">
					<li><a href="index.php" class="active">Home</a></li>
					<li><a href="about.php">About</a></li>
					<li><a href="contact.php">Contact</a></li>
					<?php if(strlen($_SESSION['uid'])==0): ?>
					<li><a href="admin/">Admin</a></li>
					<?php else :?>
					<li><a href="booking-history.php">Booking History</a></li>
					<?php endif;?>
				</ul>
			</div>
		</div>
	</header>