<?php
require_once 'auth.php'; require_doctor();
require_once 'db.php';
$pageTitle = 'Appointments'; $activeNav = 'appointments';
$did = (int)$_SESSION['doctor_id'];
$msg = '';

// Status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $id     = (int)$_POST['appt_id'];
    $status = $_POST['status'];
    $notes  = trim($_POST['notes'] ?? '');
    $allowed = ['Pending','Confirmed','Cancelled','Completed'];
    if (in_array($status, $allowed)) {
        $s = $conn->prepare("UPDATE appointments SET status=?, notes=?, assigned_doctor_id=? WHERE id=?");
        $s->bind_param('ssii', $status, $notes, $did, $id);
        $s->execute();
        $msg = 'Appointment updated.';
    }
}

// Delete
if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM appointments WHERE id=".(int)$_GET['delete']);
    header('Location: appointments.php?deleted=1'); exit;
}
if (isset($_GET['deleted'])) $msg = 'Appointment deleted.';

// Filters
$filter = $_GET['status'] ?? 'all';
$search = trim($_GET['q'] ?? '');
$dateF  = $_GET['date']   ?? '';
$where  = ["(assigned_doctor_id=$did OR assigned_doctor_id IS NULL)"];
$params = []; $types = '';
if ($filter !== 'all') { $where[] = 'status=?'; $params[] = $filter; $types .= 's'; }
if ($search !== '')    { $like = "%$search%"; $where[] = "(first_name LIKE ? OR last_name LIKE ? OR phone LIKE ? OR department LIKE ?)"; array_push($params,$like,$like,$like,$like); $types .= 'ssss'; }
if ($dateF !== '')     { $where[] = 'pref_date=?'; $params[] = $dateF; $types .= 's'; }
$whereSQL = 'WHERE '.implode(' AND ', $where);
$stmt = $conn->prepare("SELECT * FROM appointments $whereSQL ORDER BY pref_date ASC, created_at DESC");
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$appts = $stmt->get_result();

// Edit row
$editRow = null;
if (isset($_GET['edit'])) $editRow = $conn->query("SELECT * FROM appointments WHERE id=".(int)$_GET['edit'])->fetch_assoc();

include 'includes/header.php';
?>

<?php if ($msg): ?><div class="alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($msg) ?></div><?php endif; ?>

<div class="filter-bar">
  <form method="GET" class="filter-form">
    <div class="search-wrap"><i class="fas fa-search"></i>
      <input type="text" name="q" placeholder="Search patient, phone, department…" value="<?= htmlspecialchars($search) ?>">
    </div>
    <input type="date" name="date" value="<?= htmlspecialchars($dateF) ?>" class="form-ctrl-sm">
    <select name="status" onchange="this.form.submit()">
      <option value="all"      <?= $filter==='all'       ?'selected':''?>>All Status</option>
      <option value="Pending"  <?= $filter==='Pending'   ?'selected':''?>>Pending</option>
      <option value="Confirmed"<?= $filter==='Confirmed' ?'selected':''?>>Confirmed</option>
      <option value="Completed"<?= $filter==='Completed' ?'selected':''?>>Completed</option>
      <option value="Cancelled"<?= $filter==='Cancelled' ?'selected':''?>>Cancelled</option>
    </select>
    <button type="submit" class="btn-sm-teal">Filter</button>
    <a href="appointments.php" class="btn-sm-outline">Reset</a>
  </form>
</div>

<div class="admin-card">
  <div class="table-responsive">
    <table class="admin-table">
      <thead><tr><th>#</th><th>Patient</th><th>Phone</th><th>Email</th><th>Department</th><th>Date</th><th>Message</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
      <?php if ($appts->num_rows === 0): ?>
        <tr><td colspan="9" class="empty-row">No appointments found.</td></tr>
      <?php else: while ($r = $appts->fetch_assoc()): ?>
        <tr>
          <td><?= $r['id'] ?></td>
          <td><strong><?= htmlspecialchars($r['first_name'].' '.$r['last_name']) ?></strong></td>
          <td><?= htmlspecialchars($r['phone']) ?></td>
          <td><?= htmlspecialchars($r['email'] ?: '—') ?></td>
          <td><?= htmlspecialchars($r['department']) ?></td>
          <td><?= date('d M Y', strtotime($r['pref_date'])) ?></td>
          <td class="td-message"><?= htmlspecialchars(mb_strimwidth($r['message'] ?? '', 0, 35, '…')) ?></td>
          <td><span class="status-badge status-<?= strtolower($r['status']) ?>"><?= $r['status'] ?></span></td>
          <td class="td-actions">
            <a href="appointments.php?edit=<?= $r['id'] ?>" class="btn-action edit" title="Update"><i class="fas fa-edit"></i></a>
            <a href="appointments.php?delete=<?= $r['id'] ?>" class="btn-action del" onclick="return confirm('Delete?')" title="Delete"><i class="fas fa-trash"></i></a>
          </td>
        </tr>
      <?php endwhile; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php if ($editRow): ?>
<div class="modal-backdrop-custom">
  <div class="modal-box" style="max-width:540px">
    <div class="modal-box-header">
      <h4><i class="fas fa-calendar-check"></i> Update Appointment #<?= $editRow['id'] ?></h4>
      <a href="appointments.php" class="modal-close"><i class="fas fa-times"></i></a>
    </div>
    <div class="modal-box-body">
      <table class="detail-table mb-3">
        <tr><th>Patient</th><td><?= htmlspecialchars($editRow['first_name'].' '.$editRow['last_name']) ?></td></tr>
        <tr><th>Phone</th><td><?= htmlspecialchars($editRow['phone']) ?></td></tr>
        <tr><th>Department</th><td><?= htmlspecialchars($editRow['department']) ?></td></tr>
        <tr><th>Preferred Date</th><td><?= date('d M Y', strtotime($editRow['pref_date'])) ?></td></tr>
        <?php if($editRow['message']): ?><tr><th>Note</th><td><?= nl2br(htmlspecialchars($editRow['message'])) ?></td></tr><?php endif; ?>
      </table>
      <form method="POST">
        <input type="hidden" name="appt_id" value="<?= $editRow['id'] ?>">
        <div class="field-group-admin"><label>Status</label>
          <select name="status" class="form-select-admin">
            <?php foreach(['Pending','Confirmed','Completed','Cancelled'] as $s): ?>
              <option value="<?= $s ?>" <?= $editRow['status']===$s?'selected':'' ?>><?= $s ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="field-group-admin"><label>Doctor Notes</label>
          <textarea name="notes" rows="3" class="form-select-admin"><?= htmlspecialchars($editRow['notes'] ?? '') ?></textarea>
        </div>
        <div class="d-flex gap-2 mt-2">
          <button type="submit" name="update_status" class="btn-admin-primary"><i class="fas fa-save"></i> Save</button>
          <a href="appointments.php" class="btn-admin-outline">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
