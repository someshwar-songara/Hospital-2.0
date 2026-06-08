<?php
require_once 'auth.php'; require_login();
require_once 'db.php';
$pageTitle = 'Doctor Accounts'; $activeNav = 'doctor_accounts';

$msg = ''; $error = '';

// Toggle active
if (isset($_GET['toggle'])) {
    $conn->query("UPDATE doctor_accounts SET is_active=1-is_active WHERE id=".(int)$_GET['toggle']);
    header('Location: doctor_accounts.php'); exit;
}

// Delete account
if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM doctor_accounts WHERE id=".(int)$_GET['delete']);
    header('Location: doctor_accounts.php?deleted=1'); exit;
}
if (isset($_GET['deleted'])) $msg = 'Account deleted.';

// Create / reset password
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $doctor_id = (int)$_POST['doctor_id'];
    $username  = trim($_POST['username'] ?? '');
    $password  = trim($_POST['password'] ?? '');
    $edit_id   = (int)($_POST['edit_id'] ?? 0);

    if (!$doctor_id || !$username) { $error = 'Doctor and username are required.'; }
    elseif (!$edit_id && !$password) { $error = 'Password is required for new accounts.'; }
    else {
        if ($edit_id > 0) {
            if ($password) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $s = $conn->prepare("UPDATE doctor_accounts SET username=?,password=? WHERE id=?");
                $s->bind_param('ssi', $username, $hash, $edit_id);
            } else {
                $s = $conn->prepare("UPDATE doctor_accounts SET username=? WHERE id=?");
                $s->bind_param('si', $username, $edit_id);
            }
            $s->execute();
            $msg = $conn->error ?: 'Account updated.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $s = $conn->prepare("INSERT INTO doctor_accounts (doctor_id,username,password) VALUES (?,?,?)");
            $s->bind_param('iss', $doctor_id, $username, $hash);
            $s->execute();
            $msg = $conn->error ?: 'Account created. Doctor can now log in at /doctor/login.php';
        }
    }
}

$editRow = null;
if (isset($_GET['edit'])) $editRow = $conn->query("SELECT * FROM doctor_accounts WHERE id=".(int)$_GET['edit'])->fetch_assoc();

// Doctors without accounts (for dropdown)
$allDoctors  = $conn->query("SELECT d.id, d.name, d.specialty FROM doctors d ORDER BY d.name");
$existingDAs = $conn->query("SELECT * FROM doctor_accounts ORDER BY created_at DESC");

include 'includes/header.php';
?>

<?php if($msg): ?><div class="alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if($error): ?><div class="alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="row-admin g-4">
  <!-- Form -->
  <div class="col-admin-form" style="flex:0 0 340px">
    <div class="admin-card">
      <div class="admin-card-header"><h3><i class="fas fa-<?= $editRow?'edit':'user-plus' ?>"></i> <?= $editRow?'Edit':'Create' ?> Doctor Account</h3></div>
      <form method="POST" class="admin-form">
        <input type="hidden" name="edit_id" value="<?= $editRow['id']??0 ?>">
        <div class="field-group-admin"><label>Doctor <span class="required">*</span></label>
          <select name="doctor_id" required <?= $editRow?'disabled':'' ?>>
            <option value="">— Select Doctor —</option>
            <?php $allDoctors->data_seek(0); while($d=$allDoctors->fetch_assoc()): ?>
              <option value="<?= $d['id'] ?>" <?= ($editRow['doctor_id']??0)==$d['id']?'selected':'' ?>><?= htmlspecialchars($d['name']) ?> (<?= htmlspecialchars($d['specialty']) ?>)</option>
            <?php endwhile; ?>
          </select>
          <?php if($editRow): ?><input type="hidden" name="doctor_id" value="<?= $editRow['doctor_id'] ?>"> <?php endif; ?>
        </div>
        <div class="field-group-admin"><label>Username <span class="required">*</span></label>
          <input type="text" name="username" required value="<?= htmlspecialchars($editRow['username']??'') ?>" placeholder="e.g. drjaya"></div>
        <div class="field-group-admin"><label>Password <?= $editRow?'<small>(leave blank to keep current)</small>':'<span class="required">*</span>' ?></label>
          <input type="password" name="password" <?= $editRow?'':'required' ?> placeholder="<?= $editRow?'Leave blank to keep':'Set password' ?>"></div>
        <div class="d-flex gap-2 mt-2">
          <button type="submit" class="btn-admin-primary"><i class="fas fa-save"></i> <?= $editRow?'Update':'Create Account' ?></button>
          <?php if($editRow): ?><a href="doctor_accounts.php" class="btn-admin-outline">Cancel</a><?php endif; ?>
        </div>
      </form>
    </div>
    <div class="admin-card mt-3" style="padding:16px">
      <p style="font-size:.85rem;color:var(--grey-500);margin:0">
        <i class="fas fa-info-circle" style="color:var(--teal)"></i>
        Doctor portal login URL:<br>
        <strong><a href="../doctor/login.php" target="_blank">…/doctor/login.php</a></strong>
      </p>
    </div>
  </div>

  <!-- List -->
  <div class="col-admin-list">
    <div class="admin-card">
      <div class="admin-card-header"><h3><i class="fas fa-user-md"></i> All Doctor Accounts (<?= $existingDAs->num_rows ?>)</h3></div>
      <div class="table-responsive">
        <table class="admin-table">
          <thead><tr><th>Doctor</th><th>Username</th><th>Status</th><th>Last Login</th><th>Actions</th></tr></thead>
          <tbody>
          <?php if ($existingDAs->num_rows === 0): ?>
            <tr><td colspan="5" class="empty-row">No doctor accounts yet.</td></tr>
          <?php else: while ($da = $existingDAs->fetch_assoc()):
            $dRow = $conn->query("SELECT name,specialty,photo FROM doctors WHERE id=".$da['doctor_id'])->fetch_assoc();
          ?>
          <tr class="<?= !$da['is_active']?'row-inactive':'' ?>">
            <td>
              <div class="patient-name-cell">
                <div class="pmc-avatar" style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--teal),var(--teal-dark));color:var(--navy);font-weight:700;font-size:.75rem;display:flex;align-items:center;justify-content:center">
                  <?= strtoupper(substr($dRow['name']??'?',0,2)) ?>
                </div>
                <div><strong><?= htmlspecialchars($dRow['name']??'Unknown') ?></strong><br><small><?= htmlspecialchars($dRow['specialty']??'') ?></small></div>
              </div>
            </td>
            <td><code><?= htmlspecialchars($da['username']) ?></code></td>
            <td><?= $da['is_active']?'<span class="status-badge status-completed">Active</span>':'<span class="status-badge status-cancelled">Disabled</span>' ?></td>
            <td><?= $da['last_login'] ? date('d M Y, g:ia', strtotime($da['last_login'])) : '<span class="text-muted">Never</span>' ?></td>
            <td class="td-actions">
              <a href="doctor_accounts.php?toggle=<?= $da['id'] ?>" class="btn-action <?= $da['is_active']?'view':'del' ?>" title="<?= $da['is_active']?'Disable':'Enable' ?>"><i class="fas fa-<?= $da['is_active']?'eye':'eye-slash' ?>"></i></a>
              <a href="doctor_accounts.php?edit=<?= $da['id'] ?>" class="btn-action edit" title="Edit"><i class="fas fa-edit"></i></a>
              <a href="doctor_accounts.php?delete=<?= $da['id'] ?>" class="btn-action del" onclick="return confirm('Delete account?')" title="Delete"><i class="fas fa-trash"></i></a>
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
