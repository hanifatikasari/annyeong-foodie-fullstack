<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-dark navbar-primary">
	<!-- Left navbar links -->
	<ul class="navbar-nav">
	  <li class="nav-item">
		<a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
	  </li>
	</ul>

	<!-- Right navbar links -->
	<ul class="navbar-nav ml-auto">
	  <!-- Notifications Dropdown Menu -->
	  <li class="nav-item dropdown">
		<a class="nav-link" data-toggle="dropdown" href="#" id="notificationBell">
		  <i class="far fa-bell"></i>
		  <span class="badge badge-danger navbar-badge" id="notificationBadge" style="display:none;">0</span>
		</a>
		<div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
		  <span class="dropdown-item dropdown-header" id="notificationHeader">0 Pesanan Baru</span>
		  <div class="dropdown-divider"></div>
		  
          <div id="notificationList" style="max-height: 300px; overflow-y: auto;">
            <!-- Notifikasi akan dimuat via AJAX -->
            <a href="#" class="dropdown-item text-center text-muted">Tidak ada notifikasi</a>
          </div>

		  <div class="dropdown-divider"></div>
		  <a href="#" class="dropdown-item dropdown-footer" id="markAllReadBtn">Tandai semua dibaca</a>
		</div>
	  </li>
	  <li class="nav-item">
		<a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
		  <i class="fas fa-th-large"></i>
		</a>
	  </li>
	</ul>
  </nav>
  <!-- /.navbar -->

  <!-- Audio for notification -->
  <audio id="orderNotificationSound" preload="auto">
    <source src="<?= base_url('assets/sounds/notification.mp3') ?>" type="audio/mpeg">
  </audio>