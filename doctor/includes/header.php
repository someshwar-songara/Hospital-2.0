<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $pageTitle ?? 'Doctor Portal' ?> – Apex Health Care</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../admin/assets/admin.css">
  <link rel="stylesheet" href="assets/doctor.css">
</head>
<body class="admin-body">

<header class="admin-topbar">
  <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
  <div class="topbar-brand">
    <img src="../assets/img/logo.png" alt="Apex Health Care">
    <span>Doctor Portal</span>
  </div>
  <div class="topbar-right">
    <?php
    // Unread messages badge for topbar
    $unreadCount = 0;
    if (isset($conn)) {
        $uid = (int)$_SESSION['doctor_id'];
        $ur  = $conn->query("SELECT COUNT(*) c FROM staff_messages WHERE (receiver_type='doctor' AND receiver_id=$uid OR receiver_type='all') AND is_read=0 AND sender_id!=$uid");
        if ($ur) $unreadCount = $ur->fetch_assoc()['c'] ?? 0;
    }
    ?>
    <a href="messages.php" class="topbar-icon-btn" title="Messages">
      <i class="fas fa-bell"></i>
      <?php if ($unreadCount > 0): ?><span class="topbar-badge"><?= $unreadCount ?></span><?php endif; ?>
    </a>
    <span class="admin-name">
      <img src="../<?= htmlspecialchars($_SESSION['doctor_photo'] ?? 'assets/img/55.png') ?>" class="topbar-avatar" alt="">
      <?= htmlspecialchars($_SESSION['doctor_name'] ?? 'Doctor') ?>
    </span>
    <a href="logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>
</header>

<div class="admin-layout">
  <aside class="admin-sidebar" id="adminSidebar">
    <div class="doctor-sidebar-profile">
      <img src="../<?= htmlspecialchars($_SESSION['doctor_photo'] ?? 'assets/img/55.png') ?>" alt="" class="dsb-photo">
      <div>
        <strong><?= htmlspecialchars($_SESSION['doctor_name'] ?? 'Doctor') ?></strong>
        <span><?= htmlspecialchars($_SESSION['doctor_specialty'] ?? '') ?></span>
      </div>
    </div>

    <nav class="sidebar-nav">
      <div class="nav-section-label">OVERVIEW</div>
      <a href="index.php"       class="nav-item <?= ($activeNav??'')==='dashboard'   ?'active':'' ?>"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>

      <div class="nav-section-label">PATIENTS</div>
      <a href="appointments.php" class="nav-item <?= ($activeNav??'')==='appointments'?'active':'' ?>">
        <i class="fas fa-calendar-check"></i><span>Appointments</span>
        <?php if(isset($conn)){$r=$conn->query("SELECT COUNT(*) c FROM appointments WHERE status='Pending' AND (assigned_doctor_id=".(int)$_SESSION['doctor_id']." OR assigned_doctor_id IS NULL)");if($r){$c=$r->fetch_assoc()['c'];if($c>0)echo "<span class='nav-badge'>$c</span>";}}?>
      </a>
      <a href="patients.php"    class="nav-item <?= ($activeNav??'')==='patients'     ?'active':'' ?>"><i class="fas fa-users"></i><span>Patients</span></a>
      <a href="records.php"     class="nav-item <?= ($activeNav??'')==='records'      ?'active':'' ?>"><i class="fas fa-file-medical"></i><span>Medical Records</span></a>
      <a href="prescriptions.php" class="nav-item <?= ($activeNav??'')==='prescriptions'?'active':'' ?>"><i class="fas fa-prescription-bottle-alt"></i><span>Prescriptions</span></a>

      <div class="nav-section-label">COMMUNICATION</div>
      <a href="messages.php"    class="nav-item <?= ($activeNav??'')==='messages'     ?'active':'' ?>">
        <i class="fas fa-comments"></i><span>Staff Messages</span>
        <?php if(isset($conn) && $unreadCount>0): ?><span class="nav-badge"><?= $unreadCount ?></span><?php endif; ?>
      </a>

      <div class="nav-divider"></div>
      <a href="../index.php" class="nav-item" target="_blank"><i class="fas fa-external-link-alt"></i><span>View Website</span></a>
      <a href="logout.php"   class="nav-item nav-logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </nav>
  </aside>

  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <main class="admin-main">
    <div class="admin-page-header">
      <h1><?= $pageTitle ?? '' ?></h1>
      <?php if (!empty($pageSubtitle)): ?><p class="page-subtitle"><?= $pageSubtitle ?></p><?php endif; ?>
    </div>
