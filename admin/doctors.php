<?php
require_once 'auth.php'; require_login();
require_once 'db.php';
$pageTitle = 'Manage Doctors'; $activeNav = 'doctors';

$msg = ''; $error = '';

if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM doctors WHERE id=".(int)$_GET['delete']);
    header('Location: doctors.php?deleted=1'); exit;
}
if (isset($_GET['deleted'])) $msg = 'Doctor deleted.';

if (isset($_GET['toggle'])) {
    $conn->query("UPDATE doctors SET is_active=1-is_active WHERE id=".(int)$_GET['toggle']);
    header('Location: doctors.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name         = trim($_POST['name']         ?? '');
    $specialty    = trim($_POST['specialty']    ?? '');
    $photo        = trim($_POST['photo']        ?? '');
    $profile_url  = trim($_POST['profile_url']  ?? '');
    $availability = trim($_POST['availability'] ?? '');
    $bio          = trim($_POST['bio']          ?? '');
    $qualifications = trim($_POST['qualifications'] ?? '');
    $phone        = trim($_POST['phone']        ?? '');
    $sort_order   = (int)($_POST['sort_order']  ?? 0);
    $edit_id      = (int)($_POST['edit_id']     ?? 0);

    if (!$name || !$specialty) { $error = 'Name and specialty are required.'; }
    else {
        if ($edit_id > 0) {
            $s = $conn->prepare("UPDATE doctors SET name=?,specialty=?,photo=?,profile_url=?,availability=?,bio=?,qualifications=?,phone=?,sort_order=? WHERE id=?");
            $s->bind_param('ssssssssii',$name,$specialty,$photo,$profile_url,$availability,$bio,$qualifications,$phone,$sort_order,$edit_id);
        } else {
            $s = $conn->prepare("INSERT INTO doctors (name,specialty,photo,profile_url,availability,bio,qualifications,phone,sort_order) VALUES (?,?,?,?,?,?,?,?,?)");
            $s->bind_param('ssssssssi',$name,$specialty,$photo,$profile_url,$availability,$bio,$qualifications,$phone,$sort_order);
        }
        $s->execute();
        $msg = $edit_id ? 'Doctor updated.' : 'Doctor added.';
    }
}

$editRow = null;
if (isset($_GET['edit'])) {
    $editRow = $conn->query("SELECT * FROM doctors WHERE id=".(int)$_GET['edit'])->fetch_assoc();
}

// Check if sort_order column exists (handles old DB schema)
$has_sort = $conn->query("SHOW COLUMNS FROM doctors LIKE 'sort_order'")->num_rows > 0;
$order = $has_sort ? "sort_order ASC, name ASC" : "name ASC";
$list = $conn->query("SELECT * FROM doctors ORDER BY $order");
include 'includes/header.php';
?>

<?php if($msg):?><div class="alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($msg) ?></div><?php endif;?>
<?php if($error):?><div class="alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div><?php endif;?>

<div class="row-admin g-4">
  <!-- Form -->
  <div class="col-admin-form" style="flex:0 0 360px">
    <div class="admin-card">
      <div class="admin-card-header">
        <h3><i class="fas fa-<?= $editRow?'edit':'user-plus' ?>"></i> <?= $editRow?'Edit':'Add New' ?> Doctor</h3>
      </div>
      <form method="POST" class="admin-form">
        <input type="hidden" name="edit_id" value="<?= $editRow['id']??0 ?>">

        <div class="field-group-admin"><label>Full Name <span class="required">*</span></label>
          <input type="text" name="name" required value="<?= htmlspecialchars($editRow['name']??'') ?>" placeholder="Dr. Priya Sharma"></div>

        <div class="field-group-admin"><label>Specialty <span class="required">*</span></label>
          <input type="text" name="specialty" required value="<?= htmlspecialchars($editRow['specialty']??'') ?>" placeholder="Cardiologist"></div>

        <div class="field-group-admin"><label>Phone</label>
          <input type="text" name="phone" value="<?= htmlspecialchars($editRow['phone']??'') ?>" placeholder="+91 9111XXXX13"></div>

        <div class="field-group-admin"><label>Photo Path <small>(relative to site root)</small></label>
          <input type="text" name="photo" value="<?= htmlspecialchars($editRow['photo']??'') ?>" placeholder="assets/img/doctor.jpg">
          <?php if(!empty($editRow['photo'])): ?>
            <img src="../<?= htmlspecialchars($editRow['photo']) ?>" alt="" style="width:80px;height:80px;object-fit:cover;border-radius:50%;margin-top:8px;border:2px solid var(--teal)">
          <?php endif; ?>
        </div>

        <div class="field-group-admin"><label>Profile Page URL</label>
          <input type="text" name="profile_url" value="<?= htmlspecialchars($editRow['profile_url']??'') ?>" placeholder="doctor-name.php"></div>

        <div class="field-group-admin"><label>Availability</label>
          <input type="text" name="availability" value="<?= htmlspecialchars($editRow['availability']??'') ?>" placeholder="Mon–Sat: 10AM–2PM, 5PM–8PM"></div>

        <div class="field-group-admin"><label>Bio / About</label>
          <textarea name="bio" rows="3" placeholder="Brief description about the doctor…"><?= htmlspecialchars($editRow['bio']??'') ?></textarea></div>

        <div class="field-group-admin"><label>Qualifications <small>(one per line)</small></label>
          <textarea name="qualifications" rows="3" placeholder="MBBS, MD&#10;Fellowship in Cardiology&#10;12+ Years Experience"><?= htmlspecialchars($editRow['qualifications']??'') ?></textarea></div>

        <div class="field-group-admin"><label>Sort Order <small>(lower = first)</small></label>
          <input type="number" name="sort_order" value="<?= $editRow['sort_order']??0 ?>"></div>

        <div class="d-flex gap-2 flex-wrap mt-3">
          <button type="submit" class="btn-admin-primary"><i class="fas fa-save"></i> <?= $editRow?'Update Doctor':'Add Doctor' ?></button>
          <?php if($editRow):?><a href="doctors.php" class="btn-admin-outline">Cancel</a><?php endif;?>
        </div>
      </form>
    </div>
  </div>

  <!-- List -->
  <div class="col-admin-list">
    <div class="admin-card">
      <div class="admin-card-header">
        <h3><i class="fas fa-user-md"></i> All Doctors (<?= $list->num_rows ?>)</h3>
      </div>
      <div class="doctors-grid">
        <?php if($list->num_rows===0): ?>
          <p class="empty-row">No doctors found.</p>
        <?php else: ?>
          <?php while($d=$list->fetch_assoc()): ?>
          <div class="doctor-admin-card <?= !$d['is_active']?'inactive':'' ?>">
            <div class="doc-photo">
              <?php if($d['photo']): ?>
                <img src="../<?= htmlspecialchars($d['photo']) ?>" alt="<?= htmlspecialchars($d['name']) ?>">
              <?php else: ?>
                <div class="doc-photo-placeholder"><i class="fas fa-user-md"></i></div>
              <?php endif; ?>
            </div>
            <div class="doc-info">
              <strong><?= htmlspecialchars($d['name']) ?></strong>
              <span><?= htmlspecialchars($d['specialty']) ?></span>
              <?php if($d['phone']): ?><small><i class="fas fa-phone"></i> <?= htmlspecialchars($d['phone']) ?></small><?php endif; ?>
              <?php if($d['availability']): ?><small><i class="fas fa-clock"></i> <?= htmlspecialchars($d['availability']) ?></small><?php endif; ?>
              <?php if(!$d['is_active']): ?><small style="color:#dc2626"><i class="fas fa-eye-slash"></i> Hidden</small><?php endif; ?>
            </div>
            <div class="doc-actions">
              <a href="doctors.php?toggle=<?= $d['id'] ?>" class="btn-action <?= $d['is_active']?'view':'del' ?>" title="<?= $d['is_active']?'Deactivate':'Activate' ?>"><i class="fas fa-<?= $d['is_active']?'eye':'eye-slash' ?>"></i></a>
              <a href="doctors.php?edit=<?= $d['id'] ?>" class="btn-action edit" title="Edit"><i class="fas fa-edit"></i></a>
              <a href="doctors.php?delete=<?= $d['id'] ?>" class="btn-action del" onclick="return confirm('Delete <?= htmlspecialchars(addslashes($d['name'])) ?>?')" title="Delete"><i class="fas fa-trash"></i></a>
            </div>
          </div>
          <?php endwhile; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
