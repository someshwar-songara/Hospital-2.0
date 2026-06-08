<?php
require_once 'auth.php';
require_login();
require_once 'db.php';

$pageTitle    = 'Dashboard';
$pageSubtitle = 'Welcome back, ' . htmlspecialchars($_SESSION['admin_name'] ?? 'Admin');
$activeNav    = 'dashboard';

// Stats
$totalAppts    = $conn->query("SELECT COUNT(*) AS c FROM appointments")->fetch_assoc()['c'];
$pendingAppts  = $conn->query("SELECT COUNT(*) AS c FROM appointments WHERE status='Pending'")->fetch_assoc()['c'];
$todayAppts    = $conn->query("SELECT COUNT(*) AS c FROM appointments WHERE DATE(pref_date)=CURDATE()")->fetch_assoc()['c'];
$totalContacts = $conn->query("SELECT COUNT(*) AS c FROM contacts")->fetch_assoc()['c'];
$unreadMsgs    = $conn->query("SELECT COUNT(*) AS c FROM contacts WHERE is_read=0")->fetch_assoc()['c'];
$totalDoctors  = $conn->query("SELECT COUNT(*) AS c FROM doctors")->fetch_assoc()['c'];

// Recent appointments
$recentAppts = $conn->query("SELECT * FROM appointments ORDER BY created_at DESC LIMIT 6");

// Recent messages
$recentMsgs = $conn->query("SELECT * FROM contacts ORDER BY created_at DESC LIMIT 4");

include 'includes/header.php';
?>

<!-- Stats Grid -->
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-icon bg-blue"><i class="fas fa-calendar-check"></i></div>
    <div class="stat-info">
      <span class="stat-number"><?= $totalAppts ?></span>
      <span class="stat-label">Total Appointments</span>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon bg-orange"><i class="fas fa-clock"></i></div>
    <div class="stat-info">
      <span class="stat-number"><?= $pendingAppts ?></span>
      <span class="stat-label">Pending</span>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon bg-teal"><i class="fas fa-calendar-day"></i></div>
    <div class="stat-info">
      <span class="stat-number"><?= $todayAppts ?></span>
      <span class="stat-label">Today's Appointments</span>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon bg-purple"><i class="fas fa-envelope"></i></div>
    <div class="stat-info">
      <span class="stat-number"><?= $unreadMsgs ?></span>
      <span class="stat-label">Unread Messages</span>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon bg-green"><i class="fas fa-user-md"></i></div>
    <div class="stat-info">
      <span class="stat-number"><?= $totalDoctors ?></span>
      <span class="stat-label">Doctors</span>
    </div>
  </div>
</div>

<!-- Recent Appointments Table -->
<div class="admin-card mt-4">
  <div class="admin-card-header">
    <h3><i class="fas fa-calendar-check"></i> Recent Appointments</h3>
    <a href="appointments.php" class="btn-sm-teal">View All</a>
  </div>
  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Patient</th>
          <th>Phone</th>
          <th>Department</th>
          <th>Date</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($recentAppts->num_rows === 0): ?>
          <tr><td colspan="7" class="empty-row">No appointments yet.</td></tr>
        <?php else: ?>
          <?php while ($row = $recentAppts->fetch_assoc()): ?>
          <tr>
            <td><?= $row['id'] ?></td>
            <td><strong><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></strong></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td><?= htmlspecialchars($row['department']) ?></td>
            <td><?= date('d M Y', strtotime($row['pref_date'])) ?></td>
            <td><span class="status-badge status-<?= strtolower($row['status']) ?>"><?= $row['status'] ?></span></td>
            <td>
              <a href="appointments.php?edit=<?= $row['id'] ?>" class="btn-action edit" title="Edit"><i class="fas fa-edit"></i></a>
            </td>
          </tr>
          <?php endwhile; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Recent Messages -->
<div class="admin-card mt-4">
  <div class="admin-card-header">
    <h3><i class="fas fa-envelope"></i> Recent Messages</h3>
    <a href="contacts.php" class="btn-sm-teal">View All</a>
  </div>
  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr><th>#</th><th>Name</th><th>Email</th><th>Subject</th><th>Date</th><th>Status</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php if ($recentMsgs->num_rows === 0): ?>
          <tr><td colspan="7" class="empty-row">No messages yet.</td></tr>
        <?php else: ?>
          <?php while ($msg = $recentMsgs->fetch_assoc()): ?>
          <tr class="<?= !$msg['is_read'] ? 'row-unread' : '' ?>">
            <td><?= $msg['id'] ?></td>
            <td><?= htmlspecialchars($msg['name']) ?></td>
            <td><?= htmlspecialchars($msg['email']) ?></td>
            <td><?= htmlspecialchars($msg['subject']) ?></td>
            <td><?= date('d M Y', strtotime($msg['created_at'])) ?></td>
            <td><?= $msg['is_read'] ? '<span class="status-badge status-completed">Read</span>' : '<span class="status-badge status-pending">Unread</span>' ?></td>
            <td><a href="contacts.php?view=<?= $msg['id'] ?>" class="btn-action view" title="View"><i class="fas fa-eye"></i></a></td>
          </tr>
          <?php endwhile; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
