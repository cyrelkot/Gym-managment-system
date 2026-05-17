<link rel="stylesheet" href="css/admin-theme.css">
<header class="app-header">
  <a class="app-header__logo" href="index.php">GYM MS</a>
  <a class="app-sidebar__toggle" href="#" data-toggle="sidebar" aria-label="Hide Sidebar"></a>
  <ul class="app-nav ml-auto">
    <li class="dropdown">
      <a class="app-nav__item header-user-label" href="#" data-toggle="dropdown" aria-label="Open Profile Menu">
        <?php
          $adminEmail = $_SESSION['email'] ?? 'Admin';
          $avatarLetter = strtoupper(substr($adminEmail, 0, 1));
        ?>
        <span class="admin-avatar"><?php echo htmlspecialchars($avatarLetter, ENT_QUOTES, 'UTF-8'); ?></span>
        <span><?php echo htmlspecialchars($adminEmail, ENT_QUOTES, 'UTF-8'); ?></span>
      </a>
      <ul class="dropdown-menu settings-menu dropdown-menu-right">
        <li><a class="dropdown-item" href="change-password.php"><i class="fa fa-cog fa-lg"></i> Change Password</a></li>
        <li><a class="dropdown-item" href="profile.php"><i class="fa fa-user fa-lg"></i> Profile</a></li>
        <li><a class="dropdown-item" href="logout.php"><i class="fa fa-sign-out fa-lg"></i> Logout</a></li>
      </ul>
    </li>
  </ul>
</header>