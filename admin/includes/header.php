<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $pageTitle ?? 'Admin' ?> – Apex Health Care Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="<?= $assetBase ?? '../' ?>admin/assets/admin.css">
</head>
<body class="admin-body">

<!-- Top bar -->
<header class="admin-topbar">
  <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
    <i class="fas fa-bars"></i>
  </button>
  <div class="topbar-brand">
    <img src="<?= $assetBase ?? '../' ?>assets/img/logo.png" alt="Apex Health Care">
    <span>Admin Panel</span>
  </div>
  <div class="topbar-right">
    <span class="admin-name"><i class="fas fa-user-shield"></i> <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></span>
    <a href="<?= $assetBase ?? '../' ?>admin/logout.php" class="btn-logout">
      <i class="fas fa-sign-out-alt"></i> Logout
    </a>
  </div>
</header>

<!-- Layout wrapper -->
<div class="admin-layout">

  <!-- Sidebar -->
  <aside class="admin-sidebar" id="adminSidebar">
    <nav class="sidebar-nav">

      <div class="nav-section-label">OVERVIEW</div>
      <a href="index.php" class="nav-item <?= ($activeNav??'')==='dashboard'?'active':'' ?>">
        <i class="fas fa-tachometer-alt"></i><span>Dashboard</span>
      </a>

      <div class="nav-section-label">PATIENTS</div>
      <a href="appointments.php" class="nav-item <?= ($activeNav??'')==='appointments'?'active':'' ?>">
        <i class="fas fa-calendar-check"></i><span>Appointments</span>
        <?php if(isset($conn)){$r=$conn->query("SELECT COUNT(*) c FROM appointments WHERE status='Pending'");$c=$r->fetch_assoc()['c'];if($c>0)echo "<span class='nav-badge'>$c</span>";}?>
      </a>
      <a href="contacts.php" class="nav-item <?= ($activeNav??'')==='contacts'?'active':'' ?>">
        <i class="fas fa-envelope"></i><span>Messages</span>
        <?php if(isset($conn)){$r=$conn->query("SELECT COUNT(*) c FROM contacts WHERE is_read=0");$c=$r->fetch_assoc()['c'];if($c>0)echo "<span class='nav-badge'>$c</span>";}?>
      </a>

      <div class="nav-section-label">WEBSITE</div>
      <a href="doctors.php" class="nav-item <?= ($activeNav??'')==='doctors'?'active':'' ?>">
        <i class="fas fa-user-md"></i><span>Doctors</span>
      </a>
      <a href="doctor_accounts.php" class="nav-item <?= ($activeNav??'')==='doctor_accounts'?'active':'' ?>">
        <i class="fas fa-id-badge"></i><span>Doctor Accounts</span>
      </a>
      <a href="facilities_mgr.php" class="nav-item <?= ($activeNav??'')==='facilities'?'active':'' ?>">
        <i class="fas fa-hospital"></i><span>Facilities</span>
      </a>
      <a href="testimonials.php" class="nav-item <?= ($activeNav??'')==='testimonials'?'active':'' ?>">
        <i class="fas fa-star"></i><span>Testimonials</span>
      </a>
      <a href="departments.php" class="nav-item <?= ($activeNav??'')==='departments'?'active':'' ?>">
        <i class="fas fa-list"></i><span>Departments</span>
      </a>

      <div class="nav-section-label">SYSTEM</div>
      <a href="settings.php" class="nav-item <?= ($activeNav??'')==='settings'?'active':'' ?>">
        <i class="fas fa-cog"></i><span>Site Settings</span>
      </a>
      <a href="change_password.php" class="nav-item <?= ($activeNav??'')==='password'?'active':'' ?>">
        <i class="fas fa-key"></i><span>Change Password</span>
      </a>

      <div class="nav-divider"></div>
      <a href="../index.php" class="nav-item" target="_blank">
        <i class="fas fa-external-link-alt"></i><span>View Website</span>
      </a>
      <a href="logout.php" class="nav-item nav-logout">
        <i class="fas fa-sign-out-alt"></i><span>Logout</span>
      </a>

    </nav>
  </aside>

  <!-- Mobile overlay -->
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <!-- Main content -->
  <main class="admin-main">
    <div class="admin-page-header">
      <h1><?= $pageTitle ?? '' ?></h1>
      <?php if(!empty($pageSubtitle)):?><p class="page-subtitle"><?= $pageSubtitle ?></p><?php endif;?>
    </div>
