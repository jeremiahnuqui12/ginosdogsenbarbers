<nav class="col-md-2 sidebar">
  <div>
    <img src="<?php echo getWebroot(); ?>\images\18622390_1507902379228378_3089499811326682571_n.jpg" class="img-fluid" alt="Responsive image">
  </div>
  <div class="container sidebar-links" style="margin-top:10px;">
    <ul class="sidebar-links">
      <li class="disable">
        <i class="fa fa-home"></i>
        <a href="<?php echo getWebroot(); ?>/dashboard.php">
          <span>Dashboard</span>
        </a>
      </li>
      <li>
        <i class="fa fa-calendar"></i>
        <a href="<?php echo getWebroot(); ?>/appointments.php">
          <span>Appointments</span>
        </a>
      </li>
      <li>
        <i class="fa fa-users"></i>
        <a href="<?php echo getWebroot(); ?>/customer">
          <span>Customer Accounts</span>
        </a>
      </li>
      <li>
        <i class="fa fa-product-hunt"></i>
        <a href="<?php echo getWebroot(); ?>/products">
          Products
        </a>
      </li>
      <li>
        <i class="fa fa-cogs"></i>
        <a href="#" onclick="setting_onclick(this)">
          <span>Setting</span>
        </a>
        <i class="fa fa-caret-right" id="setting-icon"></i>
        <ul class="d-none" id="sub-setting">
          <li>
            <i class="fa fa-users"></i>
            <a href="<?php echo getWebRoot(); ?>/settings/admins.php">Admins</a>
          </li>
          <li>
            <i class="fa fa-list-alt"></i>
            <a href="<?php echo getWebRoot(); ?>/settings/activity log.php">Activity Log</a>
          </li>
        </ul>
      </li>
    </ul>
  </div>
</nav>
