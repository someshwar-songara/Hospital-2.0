<?php
require_once 'auth.php';
require_login();
require_once 'db.php';

$pageTitle = 'Appointments';
$activeNav = 'appointments';

$msg = '';

// Update status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $id     = (int)$_POST['appt_id'];
    $status = $_POST['status'];
    $allowed = ['Pending','Confirmed','Cancelled','Completed'];
    if (in_array($status, $allowed)) {
        $stmt = $conn->prepare("UPDATE appointments SET status=? WHERE id=?");
        $stmt->bind_param('si', $status, $id);
        $stmt->execute();
        $msg = 'Status updated successfully.';
    }
}

// Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM appointments WHERE id=$id");
    header('Location: appointments.php?deleted=1');
    exit;
}
if (isset($_GET['deleted'])) $msg = 'Appointment deleted.';

// Filters
$filter   = $_GET['status'] ?? 'all';
$search   = trim($_GET['q'] ?? '');
$where    = [];
$params   = [];
$types    = '';
if ($filter !== 'all') { $where[] = 'status=?'; $params[] = $filter; $types .= 's'; }
if ($search !== '') {
    $like = "%$search%";
    $where[] = "(first_name LIKE ? OR last_name LIKE ? OR phone LIKE ? OR department LIKE ?)";
    array_push($params, $like, $like, $like, $like);
    $types .= 'ssss';
}
$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$stmt = $conn->prepare("SELECT * FROM appointments $whereSQL ORDER BY created_at DESC");
if ($params) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$appts = $stmt->get_result();

// For edit modal
$editRow = null;
if (isset($_GET['edit'])) {
    $eid  = (int)$_GET['edit'];
    $editRow = $conn->query("SELECT * FROM appointments WHERE id=$eid")->fetch_assoc();
}

include 'includes/header.php';
?>

<?php if ($msg): ?>
  <div class="alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<!-- Filter Bar -->
<div class="filter-bar">
  <form method="GET" class="filter-form">
    <div class="search-wrap">
      <i class="fas fa-search"></i>
      <input type="text" name="q" placeholder="Search patient, phone, department…" value="<?= htmlspecialchars($search) ?>">
    </div>
    <select name="status" onchange="this.form.submit()">
      <option value="all"      <?= $filter==='all'       ? 'selected':'' ?>>All Statuses</option>
      <option value="Pending"  <?= $filter==='Pending'   ? 'selected':'' ?>>Pending</option>
      <option value="Confirmed"<?= $filter==='Confirmed' ? 'selected':'' ?>>Confirmed</option>
      <option value="Completed"<?= $filter==='Completed' ? 'selected':'' ?>>Completed</option>
      <option value="Cancelled"<?= $filter==='Cancelled' ? 'selected':'' ?>>Cancelled</option>
    </select>
    <button type="submit" class="btn-sm-teal">Search</button>
    <a href="appointments.php" class="btn-sm-outline">Reset</a>
  </form>
</div>

<!-- Table -->
<div class="admin-card">
  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>#</th><th>Patient</th><th>Phone</th><th>Email</th>
          <th>Department</th><th>Date</th><th>Message</th><th>Status</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($appts->num_rows === 0): ?>
          <tr><td colspan="9" class="empty-row">No appointments found.</td></tr>
        <?php else: ?>
          <?php while ($row = $appts->fetch_assoc()): ?>
          <tr>
            <td><?= $row['id'] ?></td>
            <td><strong><?= htmlspecialchars($row['first_name'].' '.$row['last_name']) ?></strong></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td><?= htmlspecialchars($row['email'] ?: '—') ?></td>
            <td><?= htmlspecialchars($row['department']) ?></td>
            <td><?= date('d M Y', strtotime($row['pref_date'])) ?></td>
            <td class="td-message"><?= htmlspecialchars(mb_strimwidth($row['message'] ?? '', 0, 40, '…')) ?></td>
            <td><span class="status-badge status-<?= strtolower($row['status']) ?>"><?= $row['status'] ?></span></td>
            <td class="td-actions">
              <a href="appointments.php?edit=<?= $row['id'] ?>" class="btn-action edit" title="Edit"><i class="fas fa-edit"></i></a>
              <a href="appointments.php?delete=<?= $row['id'] ?>" class="btn-action del"
                 onclick="return confirm('Delete this appointment?')" title="Delete"><i class="fas fa-trash"></i></a>
            </td>
          </tr>
          <?php endwhile; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Edit Modal -->
<?php if ($editRow): ?>
<div class="modal-backdrop-custom" id="editModal">
  <div class="modal-box">
    <div class="modal-box-header">
      <h4><i class="fas fa-edit"></i> Update Appointment</h4>
      <a href="appointments.php" class="modal-close"><i class="fas fa-times"></i></a>
    </div>
    <div class="modal-box-body">
      <p>
        <strong>Patient:</strong> <?= htmlspecialchars($editRow['first_name'].' '.$editRow['last_name']) ?><br>
        <strong>Phone:</strong> <?= htmlspecialchars($editRow['phone']) ?><br>
        <strong>Department:</strong> <?= htmlspecialchars($editRow['department']) ?><br>
        <strong>Date:</strong> <?= date('d M Y', strtotime($editRow['pref_date'])) ?><br>
        <?php if ($editRow['message']): ?>
        <strong>Message:</strong> <?= nl2br(htmlspecialchars($editRow['message'])) ?>
        <?php endif; ?>
      </p>
      <form method="POST">
        <input type="hidden" name="appt_id" value="<?= $editRow['id'] ?>">
        <label class="form-label-admin">Update Status</label>
        <select name="status" class="form-select-admin">
          <?php foreach (['Pending','Confirmed','Completed','Cancelled'] as $s): ?>
            <option value="<?= $s ?>" <?= $editRow['status']===$s ? 'selected':'' ?>><?= $s ?></option>
          <?php endforeach; ?>
        </select>
        <button type="submit" name="update_status" class="btn-admin-primary mt-3">Save Changes</button>
        <a href="appointments.php" class="btn-admin-outline mt-3">Cancel</a>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
