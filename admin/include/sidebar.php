<style>
  .app-sidebar {
    background: rgba(0, 0, 0, 0.94) !important;
    border-right: 1px solid rgba(255, 102, 0, 0.35);
    box-shadow: 4px 0 16px rgba(0, 0, 0, 0.3);
  }

  .app-menu,
  .treeview-menu {
    background: transparent !important;
  }

  .app-menu__item,
  .treeview-item {
    color: #f1f1f1 !important;
    border-radius: 8px;
    margin: 4px 10px;
    transition: all 0.2s ease;
  }

  .app-menu__item:hover,
  .app-menu__item:focus,
  .treeview-item:hover,
  .treeview-item:focus {
    background: rgba(255, 102, 0, 0.16) !important;
    color: #ff6600 !important;
  }

  .app-menu__item.active,
  .treeview.is-expanded > .app-menu__item {
    background: rgba(255, 102, 0, 0.22) !important;
    color: #ff6600 !important;
    border-left: 3px solid #ff6600;
  }

  .app-menu__icon,
  .treeview-indicator,
  .treeview-item .icon {
    color: inherit !important;
  }

  .treeview-menu {
    padding-bottom: 6px;
  }
</style>
<aside class="app-sidebar">
     
      <ul class="app-menu">
        <li><a class="app-menu__item" href="index.php"><i class="app-menu__icon fa fa-dashboard"></i><span class="app-menu__label">Dashboard</span></a></li>
        <li class="treeview"><a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-laptop"></i><span class="app-menu__label">Package Type</span><i class="treeview-indicator fa fa-angle-right"></i></a>
          <ul class="treeview-menu">
            <li><a class="treeview-item" href="add-package.php"><i class="icon fa fa-circle-o"></i> Add Package</a></li>
<!--             <li><a class="treeview-item" href="widgets.html"><i class="icon fa fa-circle-o"></i> Manage Category</a></li>
 -->          </ul>
        </li>
       
        <li class="treeview"><a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-th-list"></i><span class="app-menu__label">Package</span><i class="treeview-indicator fa fa-angle-right"></i></a>
          <ul class="treeview-menu">
            <li><a class="treeview-item" href="add-post.php"><i class="icon fa fa-circle-o"></i>Add</a></li>
            <li><a class="treeview-item" href="manage-post.php"><i class="icon fa fa-circle-o"></i> Manage</a></li>
          </ul>
        </li>


 <li class="treeview"><a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-th-list"></i><span class="app-menu__label">Booking History</span><i class="treeview-indicator fa fa-angle-right"></i></a>
          <ul class="treeview-menu">
            <li><a class="treeview-item" href="new-bookings.php"><i class="icon fa fa-circle-o"></i>New</a></li>
            <li><a class="treeview-item" href="partial-payment-bookings.php"><i class="icon fa fa-circle-o"></i> Partial Payment </a></li>
            <li><a class="treeview-item" href="full-payment-bookings.php"><i class="icon fa fa-circle-o"></i> Full Payment </a></li>
            <li><a class="treeview-item" href="booking-history.php"><i class="icon fa fa-circle-o"></i> All</a></li>
          </ul>
        </li>

        
     

          <li class="treeview"><a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-th-list"></i><span class="app-menu__label">Report</span><i class="treeview-indicator fa fa-angle-right"></i></a>
          <ul class="treeview-menu">
            <li><a class="treeview-item" href="report-booking.php"><i class="icon fa fa-circle-o"></i>Booking Report</a></li>
            <li><a class="treeview-item" href="report-registration.php"><i class="icon fa fa-circle-o"></i>Registration Report</a></li>
          </ul>
        </li>
      </ul>
    </aside>