<?php
require_once 'auth.php'; require_doctor();
require_once 'db.php';
$pageTitle = 'Prescriptions'; $activeNav = 'prescriptions';
$did = (int)$_SESSION['doctor_id'];
$msg = ''; $error = '';

if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM prescriptions WHERE id=".(int)$_GET['delete']." AND doctor_id=$did");
    header('Location: prescriptions.php?deleted=1'); exit;
}
if (isset($_GET['deleted'])) $msg = 'Prescription deleted.';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_rx'])) {
    $rid   = (int)$_POST['record_id'];
    $pid   = (int)$_POST['patient_id'];
    $meds  = $_POST['medicine_name'] ?? [];
    $doses = $_POST['dosage']        ?? [];
    $freqs = $_POST['frequency']     ?? [];
    $durs  = $_POST['duration']      ?? [];
    $instrs= $_POST['instructions']  ?? [];
    $eid   = (int)($_POST['edit_id'] ?? 0);

    if (!$pid || !$rid) { $error = 'Please select patient and record.'; }
    else {
        if ($eid > 0) {
            $mn = trim($meds[0]??''); $do = trim($doses[0]??''); $fr = trim($freqs[0]??''); $du = trim($durs[0]??''); $ins = trim($instrs[0]??'');
            $s = $conn->prepare("UPDATE prescriptions SET patient_id=?,record_id=?,medicine_name=?,dosage=?,frequency=?,duration=?,instructions=? WHERE id=? AND doctor_id=?");
            $s->bind_param('iisssssii', $pid, $rid, $mn, $do, $fr, $du, $ins, $eid, $did);
            $s->execute();
            $msg = 'Prescription updated.';
        } else {
            $stmt = $conn->prepare("INSERT INTO prescriptions (record_id,patient_id,doctor_id,medicine_name,dosage,frequency,duration,instructions) VALUES (?,?,?,?,?,?,?,?)");
            $count = 0;
            foreach ($meds as $i => $mn) {
                $mn  = trim($mn);
                if (!$mn) continue;
                $do  = trim($doses[$i]  ?? '');
                $fr  = trim($freqs[$i]  ?? '');
                $du  = trim($durs[$i]   ?? '');
                $ins = trim($instrs[$i] ?? '');
                $stmt->bind_param('iiisssss', $rid, $pid, $did, $mn, $do, $fr, $du, $ins);
                $stmt->execute();
                $count++;
            }
            $msg = "$count prescription(s) saved.";
        }
    }
}

// Edit prefill
$editRow = null;
if (isset($_GET['edit'])) $editRow = $conn->query("SELECT * FROM prescriptions WHERE id=".(int)$_GET['edit']." AND doctor_id=$did")->fetch_assoc();

// Preset record
$presetRecord = null; $presetRid = (int)($_GET['record_id'] ?? $editRow['record_id'] ?? 0);
if ($presetRid) $presetRecord = $conn->query("SELECT mr.*, CONCAT(p.first_name,' ',p.last_name) AS patient_name, p.id AS pid FROM medical_records mr JOIN patients p ON p.id=mr.patient_id WHERE mr.id=$presetRid AND mr.doctor_id=$did")->fetch_assoc();

// List — filter by patient
$patFilter = (int)($_GET['patient_id'] ?? 0);
$search    = trim($_GET['q'] ?? '');
$where = ["rx.doctor_id=$did"];
if ($patFilter) $where[] = "rx.patient_id=$patFilter";
if ($search)    { $like = "%".$conn->real_escape_string($search)."%"; $where[] = "(p.first_name LIKE '$like' OR p.last_name LIKE '$like' OR rx.medicine_name LIKE '$like')"; }
$rxList = $conn->query("SELECT rx.*, CONCAT(p.first_name,' ',p.last_name) AS patient_name, p.blood_group FROM prescriptions rx JOIN patients p ON p.id=rx.patient_id WHERE ".implode(' AND ',$where)." ORDER BY rx.created_at DESC");

$ptList  = $conn->query("SELECT id, CONCAT(first_name,' ',last_name) AS name FROM patients ORDER BY first_name");
$recList = $conn->query("SELECT mr.id, mr.visit_date, mr.diagnosis, CONCAT(p.first_name,' ',p.last_name) AS pname FROM medical_records mr JOIN patients p ON p.id=mr.patient_id WHERE mr.doctor_id=$did ORDER BY mr.visit_date DESC LIMIT 50");
$showForm= isset($_GET['action']) && $_GET['action'] === 'add';

include 'includes/header.php';
?>

<?php if($msg): ?><div class="alert-success"><i class="fas fa-check-circle"></i> <?= $msg ?></div><?php endif; ?>
<?php if($error): ?><div class="alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="mb-3">
  <button class="btn-admin-primary" onclick="document.getElementById('rxForm').classList.toggle('d-none')">
    <i class="fas fa-prescription"></i> <?= $editRow?'Edit Prescription':'New Prescription' ?>
  </button>
</div>

<!-- Prescription Form -->
<div id="rxForm" class="admin-card mb-4 <?= (!$editRow && !$showForm)?'d-none':'' ?>">
  <div class="admin-card-header"><h3><i class="fas fa-prescription-bottle-alt"></i> <?= $editRow?'Edit':'Write' ?> Prescription</h3></div>
  <form method="POST" class="admin-form" id="rxFormInner">
    <input type="hidden" name="edit_id" value="<?= $editRow['id']??0 ?>">
    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <div class="field-group-admin"><label>Patient <span class="required">*</span></label>
          <select name="patient_id" required id="ptSel">
            <option value="">— Select —</option>
            <?php $ptList->data_seek(0); while($pt=$ptList->fetch_assoc()): ?>
              <option value="<?= $pt['id'] ?>" <?= $pt['id']==($editRow['patient_id']??($presetRecord['pid']??0))?'selected':'' ?>><?= htmlspecialchars($pt['name']) ?></option>
            <?php endwhile; ?>
          </select></div>
      </div>
      <div class="col-md-6">
        <div class="field-group-admin"><label>Medical Record <span class="required">*</span></label>
          <select name="record_id" required>
            <option value="">— Select Record —</option>
            <?php $recList->data_seek(0); while($rc=$recList->fetch_assoc()): ?>
              <option value="<?= $rc['id'] ?>" <?= $rc['id']==($editRow['record_id']??$presetRid)?'selected':'' ?>>
                <?= htmlspecialchars($rc['pname']) ?> — <?= date('d M Y',strtotime($rc['visit_date'])) ?> (<?= htmlspecialchars(mb_strimwidth($rc['diagnosis']??'',0,30,'…')) ?>)
              </option>
            <?php endwhile; ?>
          </select></div>
      </div>
    </div>

    <!-- Medicine rows -->
    <div id="medRows">
      <?php if ($editRow): ?>
      <div class="med-row">
        <div class="field-group-admin" style="flex:2"><label>Medicine Name</label><input type="text" name="medicine_name[]" required value="<?= htmlspecialchars($editRow['medicine_name']) ?>"></div>
        <div class="field-group-admin" style="flex:1"><label>Dosage</label><input type="text" name="dosage[]" placeholder="500mg" value="<?= htmlspecialchars($editRow['dosage']) ?>"></div>
        <div class="field-group-admin" style="flex:1"><label>Frequency</label><input type="text" name="frequency[]" placeholder="3x daily" value="<?= htmlspecialchars($editRow['frequency']) ?>"></div>
        <div class="field-group-admin" style="flex:1"><label>Duration</label><input type="text" name="duration[]" placeholder="7 days" value="<?= htmlspecialchars($editRow['duration']) ?>"></div>
        <div class="field-group-admin" style="flex:2"><label>Instructions</label><input type="text" name="instructions[]" placeholder="After meals" value="<?= htmlspecialchars($editRow['instructions']) ?>"></div>
      </div>
      <?php else: ?>
      <div class="med-row">
        <div class="field-group-admin" style="flex:2"><label>Medicine Name</label><input type="text" name="medicine_name[]" placeholder="e.g. Paracetamol" required></div>
        <div class="field-group-admin" style="flex:1"><label>Dosage</label><input type="text" name="dosage[]" placeholder="500mg"></div>
        <div class="field-group-admin" style="flex:1"><label>Frequency</label><input type="text" name="frequency[]" placeholder="3x daily"></div>
        <div class="field-group-admin" style="flex:1"><label>Duration</label><input type="text" name="duration[]" placeholder="5 days"></div>
        <div class="field-group-admin" style="flex:2"><label>Instructions</label><input type="text" name="instructions[]" placeholder="After meals"></div>
        <button type="button" class="btn-action del align-self-end mb-4" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
      </div>
      <?php endif; ?>
    </div>
    <?php if (!$editRow): ?>
    <button type="button" class="btn-sm-outline mb-3" onclick="addMed()"><i class="fas fa-plus"></i> Add Medicine</button>
    <?php endif; ?>
    <div class="d-flex gap-2 mt-2">
      <button type="submit" name="save_rx" class="btn-admin-primary"><i class="fas fa-save"></i> Save Prescription</button>
      <a href="prescriptions.php" class="btn-admin-outline">Cancel</a>
    </div>
  </form>
</div>

<!-- Filter -->
<div class="filter-bar">
  <form method="GET" class="filter-form">
    <div class="search-wrap"><i class="fas fa-search"></i>
      <input type="text" name="q" placeholder="Search patient, medicine…" value="<?= htmlspecialchars($search) ?>"></div>
    <select name="patient_id" onchange="this.form.submit()">
      <option value="">All Patients</option>
      <?php $ptList->data_seek(0); while($pt=$ptList->fetch_assoc()): ?>
        <option value="<?= $pt['id'] ?>" <?= $pt['id']===$patFilter?'selected':''?>><?= htmlspecialchars($pt['name']) ?></option>
      <?php endwhile; ?>
    </select>
    <button type="submit" class="btn-sm-teal">Filter</button>
    <a href="prescriptions.php" class="btn-sm-outline">Reset</a>
  </form>
</div>

<div class="admin-card">
  <div class="table-responsive">
    <table class="admin-table">
      <thead><tr><th>#</th><th>Patient</th><th>Medicine</th><th>Dosage</th><th>Frequency</th><th>Duration</th><th>Instructions</th><th>Date</th><th>Actions</th></tr></thead>
      <tbody>
      <?php if ($rxList->num_rows === 0): ?>
        <tr><td colspan="9" class="empty-row">No prescriptions found.</td></tr>
      <?php else: while ($r = $rxList->fetch_assoc()): ?>
      <tr>
        <td><?= $r['id'] ?></td>
        <td><strong><?= htmlspecialchars($r['patient_name']) ?></strong></td>
        <td><strong><?= htmlspecialchars($r['medicine_name']) ?></strong></td>
        <td><?= htmlspecialchars($r['dosage']?:'—') ?></td>
        <td><?= htmlspecialchars($r['frequency']?:'—') ?></td>
        <td><?= htmlspecialchars($r['duration']?:'—') ?></td>
        <td class="td-message"><?= htmlspecialchars($r['instructions']?:'—') ?></td>
        <td><?= date('d M Y',strtotime($r['created_at'])) ?></td>
        <td class="td-actions">
          <a href="prescriptions.php?edit=<?= $r['id'] ?>" class="btn-action edit" title="Edit"><i class="fas fa-edit"></i></a>
          <a href="prescriptions.php?delete=<?= $r['id'] ?>" class="btn-action del" onclick="return confirm('Delete?')" title="Delete"><i class="fas fa-trash"></i></a>
        </td>
      </tr>
      <?php endwhile; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
function addMed() {
  const row = document.createElement('div');
  row.className = 'med-row';
  row.innerHTML = `
    <div class="field-group-admin" style="flex:2"><label>Medicine Name</label><input type="text" name="medicine_name[]" placeholder="e.g. Amoxicillin" required></div>
    <div class="field-group-admin" style="flex:1"><label>Dosage</label><input type="text" name="dosage[]" placeholder="250mg"></div>
    <div class="field-group-admin" style="flex:1"><label>Frequency</label><input type="text" name="frequency[]" placeholder="2x daily"></div>
    <div class="field-group-admin" style="flex:1"><label>Duration</label><input type="text" name="duration[]" placeholder="5 days"></div>
    <div class="field-group-admin" style="flex:2"><label>Instructions</label><input type="text" name="instructions[]" placeholder="Before meals"></div>
    <button type="button" class="btn-action del align-self-end mb-4" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
  `;
  document.getElementById('medRows').appendChild(row);
}
</script>

<?php include 'includes/footer.php'; ?>
