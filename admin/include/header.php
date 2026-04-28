<style>
  .app-header {
    background: rgba(0, 0, 0, 0.95) !important;
    border-bottom: 1px solid rgba(255, 102, 0, 0.45);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.35);
  }

  .app-header__logo {
    background: #ff6600 !important;
    color: #fff !important;
    font-weight: 700;
    letter-spacing: 1px;
  }

  .app-sidebar__toggle,
  .app-nav__item {
    color: #fff !important;
  }

  .app-sidebar__toggle:hover,
  .app-nav__item:hover,
  .app-nav__item:focus {
    background: rgba(255, 102, 0, 0.15) !important;
    color: #ff6600 !important;
  }

  .dropdown-menu.settings-menu {
    background: #111 !important;
    border: 1px solid rgba(255, 102, 0, 0.35);
  }

  .dropdown-menu.settings-menu .dropdown-item {
    color: #fff !important;
  }

  .dropdown-menu.settings-menu .dropdown-item:hover {
    background: rgba(255, 102, 0, 0.18) !important;
    color: #ff6600 !important;
  }
</style>
<header class="app-header"><a class="app-header__logo" href="index.php">GYM MS</a>
      <!-- Sidebar toggle button--><a class="app-sidebar__toggle" href="#" data-toggle="sidebar" aria-label="Hide Sidebar"></a>
      <!-- Navbar Right Menu-->
      <ul class="app-nav">
      
       
        <!-- User Menu-->
        <li class="dropdown"><a class="app-nav__item" href="#" data-toggle="dropdown" aria-label="Open Profile Menu">Welcome : Admin <i class="fa fa-user fa-lg"></i></a>
          <ul class="dropdown-menu settings-menu dropdown-menu-right">
            <li><a class="dropdown-item" href="change-password.php"><i class="fa fa-cog fa-lg"></i> Change Password</a></li>
            <li><a class="dropdown-item" href="profile.php"><i class="fa fa-user fa-lg"></i> Profile</a></li>
            <li><a class="dropdown-item" href="logout.php"><i class="fa fa-sign-out fa-lg"></i> Logout</a></li>
          </ul>
        </li>
      </ul>
    </header>