<?php
require_once 'auth.php'; require_doctor();
require_once 'db.php';
$pageTitle = 'Patients'; $activeNav = 'patients';
$did = (int)$_SESSION['doctor_id'];
$msg = ''; $error = '';

if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM patients WHERE id=".(int)$_GET['delete']);
    header('Location: patients.php?deleted=1'); exit;
}
if (isset($_GET['deleted'])) $msg = 'Patient deleted.';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fn   = trim($_POST['first_name'] ?? '');
    $ln   = trim($_POST['last_name']  ?? '');
    $dob  = trim($_POST['dob']        ?? '');
    $gen  = $_POST['gender']          ?? 'Male';
    $bg   = trim($_POST['blood_group']?? '');
    $ph   = trim($_POST['phone']      ?? '');
    $em   = trim($_POST['email']      ?? '');
    $addr = trim($_POST['address']    ?? '');
    $eid  = (int)($_POST['edit_id']   ?? 0);

    if (!$fn || !$ln || !$ph) { $error = 'First name, last name and phone are required.'; }
    else {
        if ($eid > 0) {
            $s = $conn->prepare("UPDATE patients SET first_name=?,last_name=?,dob=?,gender=?,blood_group=?,phone=?,email=?,address=? WHERE id=?");
            $s->bind_param('ssssssssi',$fn,$ln,$dob,$gen,$bg,$ph,$em,$addr,$eid);
        } else {
            $s = $conn->prepare("INSERT INTO patients (first_name,last_name,dob,gender,blood_group,phone,email,address) VALUES (?,?,?,?,?,?,?,?)");
            $s->bind_param('ssssssss',$fn,$ln,$dob,$gen,$bg,$ph,$em,$addr);
        }
        $s->execute();
        $msg = $eid ? 'Patient updated.' : 'Patient added.';
    }
}

$editRow = null;
if (isset($_GET['edit'])) $editRow = $conn->query("SELECT * FROM patients WHERE id=".(int)$_GET['edit'])->fetch_assoc();

$search = trim($_GET['q'] ?? '');
$whereSQL = '';
if ($search) { $like = "%".$conn->real_escape_string($search)."%"; $whereSQL = "WHERE first_name LIKE '$like' OR last_name LIKE '$like' OR phone LIKE '$like'"; }
$patients = $conn->query("SELECT p.*, (SELECT COUNT(*) FROM medical_records mr WHERE mr.patient_id=p.id AND mr.doctor_id=$did) AS rec_count FROM patients p $whereSQL ORDER BY p.first_name ASC");

include 'includes/header.php';
?>

<?php if($msg): ?><div class="alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if($error): ?><div class="alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="row-admin g-4">
  <!-- Form -->
  <div class="col-admin-form" style="flex:0 0 340px">
    <div class="admin-card">
      <div class="admin-card-header"><h3><i class="fas fa-<?= $editRow?'edit':'user-plus' ?>"></i> <?= $editRow?'Edit':'Add New' ?> Patient</h3></div>
      <form method="POST" class="admin-form">
        <input type="hidden" name="edit_id" value="<?= $editRow['id']??0 ?>">
        <div class="row g-2">
          <div class="col-6"><div class="field-group-admin"><label>First Name <span class="required">*</span></label>
            <input type="text" name="first_name" required value="<?= htmlspecialchars($editRow['first_name']??'') ?>"></div></div>
          <div class="col-6"><div class="field-group-admin"><label>Last Name <span class="required">*</span></label>
            <input type="text" name="last_name" required value="<?= htmlspecialchars($editRow['last_name']??'') ?>"></div></div>
        </div>
        <div class="row g-2">
          <div class="col-6"><div class="field-group-admin"><label>Date of Birth</label>
            <input type="date" name="dob" value="<?= htmlspecialchars($editRow['dob']??'') ?>"></div></div>
          <div class="col-6"><div class="field-group-admin"><label>Gender</label>
            <select name="gender"><option <?= ($editRow['gender']??'')==='Male'?'selected':''?>>Male</option><option <?= ($editRow['gender']??'')==='Female'?'selected':''?>>Female</option><option <?= ($editRow['gender']??'')==='Other'?'selected':''?>>Other</option></select></div></div>
        </div>
        <div class="row g-2">
          <div class="col-5"><div class="field-group-admin"><label>Blood Group</label>
            <select name="blood_group">
              <option value="">—</option>
              <?php foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg): ?>
                <option <?= ($editRow['blood_group']??'')===$bg?'selected':'' ?>><?= $bg ?></option>
              <?php endforeach; ?>
            </select></div></div>
          <div class="col-7"><div class="field-group-admin"><label>Phone <span class="required">*</span></label>
            <input type="tel" name="phone" required value="<?= htmlspecialchars($editRow['phone']??'') ?>"></div></div>
        </div>
        <div class="field-group-admin"><label>Email</label>
          <input type="email" name="email" value="<?= htmlspecialchars($editRow['email']??'') ?>"></div>
        <div class="field-group-admin"><label>Address</label>
          <textarea name="address" rows="2"><?= htmlspecialchars($editRow['address']??'') ?></textarea></div>
        <div class="d-flex gap-2 mt-2">
          <button type="submit" class="btn-admin-primary"><i class="fas fa-save"></i> <?= $editRow?'Update':'Add Patient' ?></button>
          <?php if($editRow): ?><a href="patients.php" class="btn-admin-outline">Cancel</a><?php endif; ?>
        </div>
      </form>
    </div>
  </div>

  <!-- List -->
  <div class="col-admin-list">
    <div class="admin-card">
      <div class="admin-card-header">
        <h3><i class="fas fa-users"></i> All Patients (<?= $patients->num_rows ?>)</h3>
        <form method="GET" class="d-flex gap-2">
          <div class="search-wrap" style="flex:1"><i class="fas fa-search"></i>
            <input type="text" name="q" placeholder="Search…" value="<?= htmlspecialchars($search) ?>"></div>
          <button type="submit" class="btn-sm-teal">Go</button>
        </form>
      </div>
      <div class="table-responsive">
        <table class="admin-table">
          <thead><tr><th>#</th><th>Name</th><th>Age/Gender</th><th>Blood</th><th>Phone</th><th>Records</th><th>Actions</th></tr></thead>
          <tbody>
          <?php if ($patients->num_rows === 0): ?>
            <tr><td colspan="7" class="empty-row">No patients found.</td></tr>
          <?php else: while ($p = $patients->fetch_assoc()):
            $age = $p['dob'] ? (int)date_diff(date_create($p['dob']),date_create('today'))->y : '—';
          ?>
          <tr>
            <td><?= $p['id'] ?></td>
            <td>
              <div class="patient-name-cell">
                <div class="pnc-avatar"><?= strtoupper(substr($p['first_name'],0,1).substr($p['last_name'],0,1)) ?></div>
                <div><strong><?= htmlspecialchars($p['first_name'].' '.$p['last_name']) ?></strong><br><small><?= htmlspecialchars($p['email']?:'—') ?></small></div>
              </div>
            </td>
            <td><?= $age ?> / <?= $p['gender'] ?></td>
            <td><?= $p['blood_group'] ?: '—' ?></td>
            <td><?= htmlspecialchars($p['phone']) ?></td>
            <td><a href="records.php?patient_id=<?= $p['id'] ?>" class="status-badge status-confirmed"><?= $p['rec_count'] ?> records</a></td>
            <td class="td-actions">
              <a href="records.php?action=add&patient_id=<?= $p['id'] ?>" class="btn-action view" title="Add Record"><i class="fas fa-file-medical"></i></a>
              <a href="patients.php?edit=<?= $p['id'] ?>" class="btn-action edit" title="Edit"><i class="fas fa-edit"></i></a>
              <a href="patients.php?delete=<?= $p['id'] ?>" class="btn-action del" onclick="return confirm('Delete patient and all records?')" title="Delete"><i class="fas fa-trash"></i></a>
            </td>
          </tr>
          <?php endwhile; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
