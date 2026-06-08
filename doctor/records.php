<?php
require_once 'auth.php'; require_doctor();
require_once 'db.php';
$pageTitle = 'Medical Records'; $activeNav = 'records';
$did = (int)$_SESSION['doctor_id'];
$msg = ''; $error = '';

if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM medical_records WHERE id=".(int)$_GET['delete']." AND doctor_id=$did");
    header('Location: records.php?deleted=1'); exit;
}
if (isset($_GET['deleted'])) $msg = 'Record deleted.';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pid    = (int)$_POST['patient_id'];
    $date   = trim($_POST['visit_date']      ?? date('Y-m-d'));
    $chief  = trim($_POST['chief_complaint'] ?? '');
    $diag   = trim($_POST['diagnosis']       ?? '');
    $notes  = trim($_POST['notes']           ?? '');
    $bp     = trim($_POST['vitals_bp']       ?? '');
    $pulse  = trim($_POST['vitals_pulse']    ?? '');
    $temp   = trim($_POST['vitals_temp']     ?? '');
    $weight = trim($_POST['vitals_weight']   ?? '');
    $eid    = (int)($_POST['edit_id']        ?? 0);

    if (!$pid) { $error = 'Please select a patient.'; }
    else {
        if ($eid > 0) {
            $s = $conn->prepare("UPDATE medical_records SET patient_id=?,visit_date=?,chief_complaint=?,diagnosis=?,notes=?,vitals_bp=?,vitals_pulse=?,vitals_temp=?,vitals_weight=? WHERE id=? AND doctor_id=?");
            $s->bind_param('issssssssii',$pid,$date,$chief,$diag,$notes,$bp,$pulse,$temp,$weight,$eid,$did);
        } else {
            $s = $conn->prepare("INSERT INTO medical_records (patient_id,doctor_id,visit_date,chief_complaint,diagnosis,notes,vitals_bp,vitals_pulse,vitals_temp,vitals_weight) VALUES (?,?,?,?,?,?,?,?,?,?)");
            $s->bind_param('iissssssss',$pid,$did,$date,$chief,$diag,$notes,$bp,$pulse,$temp,$weight);
        }
        $s->execute();
        $newId = $eid ?: (int)$conn->insert_id;
        $msg = $eid ? 'Record updated.' : 'Record saved. <a href="prescriptions.php?action=add&record_id='.$newId.'">Add prescription →</a>';
    }
}

$editRow = null;
if (isset($_GET['edit'])) $editRow = $conn->query("SELECT * FROM medical_records WHERE id=".(int)$_GET['edit']." AND doctor_id=$did")->fetch_assoc();

// Filter
$patFilter = (int)($_GET['patient_id'] ?? 0);
$search    = trim($_GET['q'] ?? '');
$where     = ["mr.doctor_id=$did"];
if ($patFilter) $where[] = "mr.patient_id=$patFilter";
if ($search)    { $like = "%".$conn->real_escape_string($search)."%"; $where[] = "(p.first_name LIKE '$like' OR p.last_name LIKE '$like' OR mr.diagnosis LIKE '$like')"; }
$whereSQL = 'WHERE '.implode(' AND ', $where);
$records = $conn->query("SELECT mr.*, CONCAT(p.first_name,' ',p.last_name) AS patient_name, p.dob, p.gender, p.blood_group FROM medical_records mr JOIN patients p ON p.id=mr.patient_id $whereSQL ORDER BY mr.visit_date DESC, mr.created_at DESC");

// Patient list for dropdown
$ptList = $conn->query("SELECT id, CONCAT(first_name,' ',last_name) AS name FROM patients ORDER BY first_name ASC");
$presetPid = (int)($_GET['patient_id'] ?? $editRow['patient_id'] ?? 0);
$showForm = isset($_GET['action']) && $_GET['action'] === 'add';

include 'includes/header.php';
?>

<?php if($msg): ?><div class="alert-success"><i class="fas fa-check-circle"></i> <?= $msg ?></div><?php endif; ?>
<?php if($error): ?><div class="alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div><?php endif; ?>

<!-- Add/Edit form toggle -->
<div class="mb-3 d-flex gap-2 align-items-center">
  <button class="btn-admin-primary" onclick="document.getElementById('recForm').classList.toggle('d-none')">
    <i class="fas fa-<?= ($editRow||$showForm)?'minus':'plus' ?>"></i> <?= $editRow?'Edit Record':'New Record' ?>
  </button>
  <a href="records.php" class="btn-admin-outline">View All</a>
</div>

<div id="recForm" class="admin-card mb-4 <?= (!$editRow && !$showForm)?'d-none':'' ?>">
  <div class="admin-card-header"><h3><i class="fas fa-file-medical-alt"></i> <?= $editRow?'Edit':'New' ?> Medical Record</h3></div>
  <form method="POST" class="admin-form">
    <input type="hidden" name="edit_id" value="<?= $editRow['id']??0 ?>">
    <div class="row g-3">
      <div class="col-md-6">
        <div class="field-group-admin"><label>Patient <span class="required">*</span></label>
          <select name="patient_id" required>
            <option value="">— Select Patient —</option>
            <?php $ptList->data_seek(0); while($pt=$ptList->fetch_assoc()): ?>
              <option value="<?= $pt['id'] ?>" <?= $pt['id']==($editRow['patient_id']??$presetPid)?'selected':'' ?>><?= htmlspecialchars($pt['name']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>
      </div>
      <div class="col-md-6">
        <div class="field-group-admin"><label>Visit Date</label>
          <input type="date" name="visit_date" value="<?= $editRow['visit_date']??date('Y-m-d') ?>"></div>
      </div>
    </div>

    <!-- Vitals -->
    <div class="vitals-row">
      <div class="vital-box"><i class="fas fa-heartbeat"></i><label>BP</label><input type="text" name="vitals_bp" placeholder="120/80" value="<?= htmlspecialchars($editRow['vitals_bp']??'') ?>"></div>
      <div class="vital-box"><i class="fas fa-wave-square"></i><label>Pulse</label><input type="text" name="vitals_pulse" placeholder="72 bpm" value="<?= htmlspecialchars($editRow['vitals_pulse']??'') ?>"></div>
      <div class="vital-box"><i class="fas fa-thermometer-half"></i><label>Temp</label><input type="text" name="vitals_temp" placeholder="98.6°F" value="<?= htmlspecialchars($editRow['vitals_temp']??'') ?>"></div>
      <div class="vital-box"><i class="fas fa-weight"></i><label>Weight</label><input type="text" name="vitals_weight" placeholder="70 kg" value="<?= htmlspecialchars($editRow['vitals_weight']??'') ?>"></div>
    </div>

    <div class="field-group-admin"><label>Chief Complaint</label>
      <input type="text" name="chief_complaint" placeholder="Main reason for visit…" value="<?= htmlspecialchars($editRow['chief_complaint']??'') ?>"></div>
    <div class="field-group-admin"><label>Diagnosis</label>
      <input type="text" name="diagnosis" placeholder="Diagnosed condition…" value="<?= htmlspecialchars($editRow['diagnosis']??'') ?>"></div>
    <div class="field-group-admin"><label>Clinical Notes</label>
      <textarea name="notes" rows="4" placeholder="Detailed clinical observations, treatment plan…"><?= htmlspecialchars($editRow['notes']??'') ?></textarea></div>
    <div class="d-flex gap-2 mt-2">
      <button type="submit" class="btn-admin-primary"><i class="fas fa-save"></i> <?= $editRow?'Update':'Save Record' ?></button>
      <a href="records.php" class="btn-admin-outline">Cancel</a>
    </div>
  </form>
</div>

<!-- Filter -->
<div class="filter-bar">
  <form method="GET" class="filter-form">
    <div class="search-wrap"><i class="fas fa-search"></i>
      <input type="text" name="q" placeholder="Search patient, diagnosis…" value="<?= htmlspecialchars($search) ?>"></div>
    <select name="patient_id" onchange="this.form.submit()">
      <option value="">All Patients</option>
      <?php $ptList->data_seek(0); while($pt=$ptList->fetch_assoc()): ?>
        <option value="<?= $pt['id'] ?>" <?= $pt['id']===$patFilter?'selected':'' ?>><?= htmlspecialchars($pt['name']) ?></option>
      <?php endwhile; ?>
    </select>
    <button type="submit" class="btn-sm-teal">Filter</button>
    <a href="records.php" class="btn-sm-outline">Reset</a>
  </form>
</div>

<div class="admin-card">
  <div class="table-responsive">
    <table class="admin-table">
      <thead><tr><th>#</th><th>Patient</th><th>Visit Date</th><th>Chief Complaint</th><th>Diagnosis</th><th>Vitals</th><th>Actions</th></tr></thead>
      <tbody>
      <?php if ($records->num_rows === 0): ?>
        <tr><td colspan="7" class="empty-row">No records found.</td></tr>
      <?php else: while ($r = $records->fetch_assoc()): ?>
      <tr>
        <td><?= $r['id'] ?></td>
        <td>
          <strong><?= htmlspecialchars($r['patient_name']) ?></strong><br>
          <small><?= $r['blood_group'] ? '<span class="blood-badge">'.$r['blood_group'].'</span>' : '' ?> <?= $r['gender'] ?></small>
        </td>
        <td><?= date('d M Y', strtotime($r['visit_date'])) ?></td>
        <td><?= htmlspecialchars(mb_strimwidth($r['chief_complaint']??'—',0,40,'…')) ?></td>
        <td><?= htmlspecialchars(mb_strimwidth($r['diagnosis']??'—',0,40,'…')) ?></td>
        <td>
          <small class="vitals-mini">
            <?php if($r['vitals_bp']): ?>BP: <?= htmlspecialchars($r['vitals_bp']) ?> &nbsp;<?php endif; ?>
            <?php if($r['vitals_pulse']): ?>P: <?= htmlspecialchars($r['vitals_pulse']) ?><?php endif; ?>
          </small>
        </td>
        <td class="td-actions">
          <a href="prescriptions.php?action=add&record_id=<?= $r['id'] ?>" class="btn-action view" title="Add Prescription"><i class="fas fa-prescription"></i></a>
          <a href="records.php?edit=<?= $r['id'] ?>" class="btn-action edit" title="Edit"><i class="fas fa-edit"></i></a>
          <a href="records.php?delete=<?= $r['id'] ?>" class="btn-action del" onclick="return confirm('Delete record?')" title="Delete"><i class="fas fa-trash"></i></a>
        </td>
      </tr>
      <?php endwhile; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
