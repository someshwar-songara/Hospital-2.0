<?php
require_once 'auth.php'; require_login();
require_once 'db.php';
$pageTitle = 'Departments'; $activeNav = 'departments';

$msg = ''; $error = '';

if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM departments WHERE id=".(int)$_GET['delete']);
    header('Location: departments.php?deleted=1'); exit;
}
if (isset($_GET['deleted'])) $msg = 'Department deleted.';

if (isset($_GET['toggle'])) {
    $conn->query("UPDATE departments SET is_active=1-is_active WHERE id=".(int)$_GET['toggle']);
    header('Location: departments.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name       = trim($_POST['name']       ?? '');
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    $edit_id    = (int)($_POST['edit_id']    ?? 0);

    if (!$name) { $error = 'Department name is required.'; }
    else {
        if ($edit_id > 0) {
            $s = $conn->prepare("UPDATE departments SET name=?,sort_order=? WHERE id=?");
            $s->bind_param('sii',$name,$sort_order,$edit_id);
        } else {
            $s = $conn->prepare("INSERT INTO departments (name,sort_order) VALUES (?,?)");
            $s->bind_param('si',$name,$sort_order);
        }
        $s->execute();
        $msg = $edit_id ? 'Department updated.' : 'Department added.';
    }
}

$editRow = null;
if (isset($_GET['edit'])) {
    $editRow = $conn->query("SELECT * FROM departments WHERE id=".(int)$_GET['edit'])->fetch_assoc();
}

$list = $conn->query("SELECT * FROM departments ORDER BY sort_order ASC, name ASC");
include 'includes/header.php';
?>

<?php if($msg):?><div class="alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($msg) ?></div><?php endif;?>
<?php if($error):?><div class="alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div><?php endif;?>

<p class="page-subtitle" style="margin-bottom:20px;">These departments appear in the appointment booking form dropdown.</p>

<div class="row-admin g-4">
  <div class="col-admin-form" style="flex:0 0 300px">
    <div class="admin-card">
      <div class="admin-card-header"><h3><i class="fas fa-<?= $editRow?'edit':'plus' ?>"></i> <?= $editRow?'Edit':'Add' ?> Department</h3></div>
      <form method="POST" class="admin-form">
        <input type="hidden" name="edit_id" value="<?= $editRow['id']??0 ?>">
        <div class="field-group-admin"><label>Department Name <span class="required">*</span></label>
          <input type="text" name="name" required value="<?= htmlspecialchars($editRow['name']??'') ?>" placeholder="e.g. Cardiology"></div>
        <div class="field-group-admin"><label>Sort Order</label>
          <input type="number" name="sort_order" value="<?= $editRow['sort_order']??0 ?>"></div>
        <div class="d-flex gap-2 mt-3">
          <button type="submit" class="btn-admin-primary"><i class="fas fa-save"></i> <?= $editRow?'Update':'Add' ?></button>
          <?php if($editRow):?><a href="departments.php" class="btn-admin-outline">Cancel</a><?php endif;?>
        </div>
      </form>
    </div>
  </div>

  <div class="col-admin-list">
    <div class="admin-card">
      <div class="admin-card-header"><h3><i class="fas fa-list"></i> All Departments (<?= $list->num_rows ?>)</h3></div>
      <div class="table-responsive">
        <table class="admin-table">
          <thead><tr><th>#</th><th>Department Name</th><th>Status</th><th>Order</th><th>Actions</th></tr></thead>
          <tbody>
          <?php if($list->num_rows===0): ?>
            <tr><td colspan="5" class="empty-row">No departments yet.</td></tr>
          <?php else: ?>
            <?php while($d=$list->fetch_assoc()): ?>
            <tr>
              <td><?= $d['id'] ?></td>
              <td><strong><?= htmlspecialchars($d['name']) ?></strong></td>
              <td><?= $d['is_active']?'<span class="status-badge status-completed">Active</span>':'<span class="status-badge status-cancelled">Hidden</span>' ?></td>
              <td><?= $d['sort_order'] ?></td>
              <td class="td-actions">
                <a href="departments.php?toggle=<?= $d['id'] ?>" class="btn-action <?= $d['is_active']?'view':'del' ?>" title="Toggle"><i class="fas fa-<?= $d['is_active']?'eye':'eye-slash' ?>"></i></a>
                <a href="departments.php?edit=<?= $d['id'] ?>" class="btn-action edit" title="Edit"><i class="fas fa-edit"></i></a>
                <a href="departments.php?delete=<?= $d['id'] ?>" class="btn-action del" onclick="return confirm('Delete?')" title="Delete"><i class="fas fa-trash"></i></a>
              </td>
            </tr>
            <?php endwhile; ?>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
