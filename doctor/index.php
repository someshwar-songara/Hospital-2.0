<?php
require_once 'auth.php'; require_doctor();
require_once 'db.php';
$pageTitle = 'Dashboard'; $activeNav = 'dashboard';
$did = (int)$_SESSION['doctor_id'];

// Stats
$myAppts     = $conn->query("SELECT COUNT(*) c FROM appointments WHERE assigned_doctor_id=$did OR assigned_doctor_id IS NULL")->fetch_assoc()['c'];
$todayAppts  = $conn->query("SELECT COUNT(*) c FROM appointments WHERE DATE(pref_date)=CURDATE() AND (assigned_doctor_id=$did OR assigned_doctor_id IS NULL)")->fetch_assoc()['c'];
$pendingAppts= $conn->query("SELECT COUNT(*) c FROM appointments WHERE status='Pending' AND (assigned_doctor_id=$did OR assigned_doctor_id IS NULL)")->fetch_assoc()['c'];
$totalPts    = $conn->query("SELECT COUNT(DISTINCT patient_id) c FROM medical_records WHERE doctor_id=$did")->fetch_assoc()['c'];
$totalRx     = $conn->query("SELECT COUNT(*) c FROM prescriptions WHERE doctor_id=$did")->fetch_assoc()['c'];

// unread messages
$uid = $did;
$unreadCount = $conn->query("SELECT COUNT(*) c FROM staff_messages WHERE (receiver_type='doctor' AND receiver_id=$uid OR receiver_type='all') AND is_read=0 AND sender_id!=$uid")->fetch_assoc()['c'] ?? 0;

// Today's appointments
$todayList = $conn->query("SELECT a.*, CONCAT(a.first_name,' ',a.last_name) AS patient_name FROM appointments a WHERE DATE(a.pref_date)=CURDATE() AND (a.assigned_doctor_id=$did OR a.assigned_doctor_id IS NULL) ORDER BY a.created_at DESC LIMIT 8");

// Recent patients
$recentPts = $conn->query("SELECT p.*, mr.visit_date, mr.diagnosis FROM patients p JOIN medical_records mr ON mr.patient_id=p.id WHERE mr.doctor_id=$did ORDER BY mr.created_at DESC LIMIT 5");

include 'includes/header.php';
?>

<!-- Stats -->
<div class="stats-grid">
  <div class="stat-card"><div class="stat-icon bg-blue"><i class="fas fa-calendar-check"></i></div><div class="stat-info"><span class="stat-number"><?= $myAppts ?></span><span class="stat-label">Total Appointments</span></div></div>
  <div class="stat-card"><div class="stat-icon bg-orange"><i class="fas fa-clock"></i></div><div class="stat-info"><span class="stat-number"><?= $pendingAppts ?></span><span class="stat-label">Pending</span></div></div>
  <div class="stat-card"><div class="stat-icon bg-teal"><i class="fas fa-calendar-day"></i></div><div class="stat-info"><span class="stat-number"><?= $todayAppts ?></span><span class="stat-label">Today</span></div></div>
  <div class="stat-card"><div class="stat-icon bg-green"><i class="fas fa-users"></i></div><div class="stat-info"><span class="stat-number"><?= $totalPts ?></span><span class="stat-label">My Patients</span></div></div>
  <div class="stat-card"><div class="stat-icon bg-purple"><i class="fas fa-prescription-bottle-alt"></i></div><div class="stat-info"><span class="stat-number"><?= $totalRx ?></span><span class="stat-label">Prescriptions</span></div></div>
</div>

<!-- Quick Actions -->
<div class="quick-actions mt-4">
  <a href="appointments.php" class="qa-btn"><i class="fas fa-calendar-check"></i> Appointments</a>
  <a href="patients.php?action=add" class="qa-btn"><i class="fas fa-user-plus"></i> New Patient</a>
  <a href="records.php?action=add" class="qa-btn"><i class="fas fa-file-medical-alt"></i> New Record</a>
  <a href="prescriptions.php?action=add" class="qa-btn"><i class="fas fa-prescription"></i> New Prescription</a>
  <a href="messages.php?action=compose" class="qa-btn <?= $unreadCount>0?'qa-btn-alert':'' ?>">
    <i class="fas fa-comments"></i> Messages <?= $unreadCount>0?"<span class='qa-badge'>$unreadCount</span>":'' ?>
  </a>
</div>

<div class="row-admin g-4 mt-1">
  <!-- Today's schedule -->
  <div class="col-admin-list">
    <div class="admin-card">
      <div class="admin-card-header">
        <h3><i class="fas fa-calendar-day"></i> Today's Schedule</h3>
        <a href="appointments.php" class="btn-sm-teal">View All</a>
      </div>
      <div class="table-responsive">
        <table class="admin-table">
          <thead><tr><th>#</th><th>Patient</th><th>Phone</th><th>Department</th><th>Status</th><th>Action</th></tr></thead>
          <tbody>
          <?php if ($todayList->num_rows === 0): ?>
            <tr><td colspan="6" class="empty-row">No appointments today.</td></tr>
          <?php else: while ($a = $todayList->fetch_assoc()): ?>
            <tr>
              <td><?= $a['id'] ?></td>
              <td><strong><?= htmlspecialchars($a['patient_name']) ?></strong></td>
              <td><?= htmlspecialchars($a['phone']) ?></td>
              <td><?= htmlspecialchars($a['department']) ?></td>
              <td><span class="status-badge status-<?= strtolower($a['status']) ?>"><?= $a['status'] ?></span></td>
              <td>
                <a href="appointments.php?edit=<?= $a['id'] ?>" class="btn-action edit" title="Update"><i class="fas fa-edit"></i></a>
              </td>
            </tr>
          <?php endwhile; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Recent patients -->
  <div class="col-admin-form" style="flex:0 0 320px">
    <div class="admin-card">
      <div class="admin-card-header">
        <h3><i class="fas fa-users"></i> Recent Patients</h3>
        <a href="patients.php" class="btn-sm-teal">All</a>
      </div>
      <div style="padding:12px">
        <?php if (!$recentPts || $recentPts->num_rows === 0): ?>
          <p class="empty-row">No patient records yet.</p>
        <?php else: while ($p = $recentPts->fetch_assoc()): ?>
        <div class="patient-mini-card">
          <div class="pmc-avatar"><?= strtoupper(substr($p['first_name'],0,1).substr($p['last_name'],0,1)) ?></div>
          <div class="pmc-info">
            <strong><?= htmlspecialchars($p['first_name'].' '.$p['last_name']) ?></strong>
            <span><?= htmlspecialchars($p['diagnosis'] ?? 'No diagnosis') ?></span>
            <small><?= $p['visit_date'] ? date('d M Y', strtotime($p['visit_date'])) : '' ?></small>
          </div>
          <a href="records.php?patient_id=<?= $p['id'] ?>" class="btn-action view"><i class="fas fa-eye"></i></a>
        </div>
        <?php endwhile; endif; ?>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
