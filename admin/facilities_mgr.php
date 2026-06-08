<?php
require_once 'auth.php'; require_login();
require_once 'db.php';
$pageTitle = 'Facilities'; $activeNav = 'facilities';

$msg = ''; $error = '';

if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM facilities WHERE id=".(int)$_GET['delete']);
    header('Location: facilities_mgr.php?deleted=1'); exit;
}
if (isset($_GET['deleted'])) $msg = 'Facility deleted.';

if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $conn->query("UPDATE facilities SET is_active=1-is_active WHERE id=$id");
    header('Location: facilities_mgr.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name']        ?? '');
    $description = trim($_POST['description'] ?? '');
    $image       = trim($_POST['image']       ?? '');
    $category    = $_POST['category'] === 'surgical' ? 'surgical' : 'medical';
    $sort_order  = (int)($_POST['sort_order'] ?? 0);
    $edit_id     = (int)($_POST['edit_id']    ?? 0);

    if (!$name) { $error = 'Facility name is required.'; }
    else {
        if ($edit_id > 0) {
            $s = $conn->prepare("UPDATE facilities SET name=?,description=?,image=?,category=?,sort_order=? WHERE id=?");
            $s->bind_param('ssssis',$name,$description,$image,$category,$sort_order,$edit_id);
            // fix: edit_id is int
            $s = $conn->prepare("UPDATE facilities SET name=?,description=?,image=?,category=?,sort_order=? WHERE id=?");
            $s->bind_param('ssssii',$name,$description,$image,$category,$sort_order,$edit_id);
        } else {
            $s = $conn->prepare("INSERT INTO facilities (name,description,image,category,sort_order) VALUES (?,?,?,?,?)");
            $s->bind_param('ssssi',$name,$description,$image,$category,$sort_order);
        }
        $s->execute();
        $msg = $edit_id ? 'Facility updated.' : 'Facility added.';
    }
}

$editRow = null;
if (isset($_GET['edit'])) {
    $editRow = $conn->query("SELECT * FROM facilities WHERE id=".(int)$_GET['edit'])->fetch_assoc();
}

$list = $conn->query("SELECT * FROM facilities ORDER BY sort_order ASC, id ASC");
include 'includes/header.php';
?>

<?php if($msg):?><div class="alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($msg) ?></div><?php endif;?>
<?php if($error):?><div class="alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div><?php endif;?>

<div class="row-admin g-4">
  <!-- Form -->
  <div class="col-admin-form">
    <div class="admin-card">
      <div class="admin-card-header">
        <h3><i class="fas fa-<?= $editRow?'edit':'plus' ?>"></i> <?= $editRow?'Edit':'Add' ?> Facility</h3>
      </div>
      <form method="POST" class="admin-form">
        <input type="hidden" name="edit_id" value="<?= $editRow['id']??0 ?>">
        <div class="field-group-admin"><label>Facility Name <span class="required">*</span></label>
          <input type="text" name="name" required value="<?= htmlspecialchars($editRow['name']??'') ?>"></div>
        <div class="field-group-admin"><label>Description</label>
          <textarea name="description" rows="3"><?= htmlspecialchars($editRow['description']??'') ?></textarea></div>
        <div class="field-group-admin"><label>Image Path <small>(relative to site root)</small></label>
          <input type="text" name="image" value="<?= htmlspecialchars($editRow['image']??'') ?>" placeholder="assets/img/cardiology.jpg">
          <?php if(!empty($editRow['image'])): ?>
            <img src="../<?= htmlspecialchars($editRow['image']) ?>" alt="" style="width:100%;max-height:120px;object-fit:cover;border-radius:8px;margin-top:8px;">
          <?php endif; ?>
        </div>
        <div class="field-group-admin"><label>Category</label>
          <select name="category">
            <option value="medical"  <?= ($editRow['category']??'medical')==='medical' ?'selected':'' ?>>Medical</option>
            <option value="surgical" <?= ($editRow['category']??'')==='surgical'?'selected':'' ?>>Surgical</option>
          </select></div>
        <div class="field-group-admin"><label>Sort Order <small>(lower = first)</small></label>
          <input type="number" name="sort_order" value="<?= $editRow['sort_order']??0 ?>"></div>
        <div class="d-flex gap-2 mt-3">
          <button type="submit" class="btn-admin-primary"><i class="fas fa-save"></i> <?= $editRow?'Update':'Add' ?></button>
          <?php if($editRow):?><a href="facilities_mgr.php" class="btn-admin-outline">Cancel</a><?php endif;?>
        </div>
      </form>
    </div>
  </div>

  <!-- List -->
  <div class="col-admin-list">
    <div class="admin-card">
      <div class="admin-card-header"><h3><i class="fas fa-hospital"></i> All Facilities (<?= $list->num_rows ?>)</h3></div>
      <div class="table-responsive">
        <table class="admin-table">
          <thead><tr><th>Image</th><th>Name</th><th>Category</th><th>Status</th><th>Order</th><th>Actions</th></tr></thead>
          <tbody>
          <?php if($list->num_rows===0): ?>
            <tr><td colspan="6" class="empty-row">No facilities yet.</td></tr>
          <?php else: ?>
            <?php while($f=$list->fetch_assoc()): ?>
            <tr class="<?= !$f['is_active']?'row-inactive':'' ?>">
              <td>
                <?php if($f['image']): ?>
                  <img src="../<?= htmlspecialchars($f['image']) ?>" alt="" style="width:60px;height:44px;object-fit:cover;border-radius:6px;">
                <?php else: ?>
                  <div style="width:60px;height:44px;background:var(--grey-100);border-radius:6px;display:flex;align-items:center;justify-content:center;color:var(--grey-300)"><i class="fas fa-image"></i></div>
                <?php endif; ?>
              </td>
              <td><strong><?= htmlspecialchars($f['name']) ?></strong><br><small class="text-muted"><?= htmlspecialchars(mb_strimwidth($f['description']??'',0,50,'…')) ?></small></td>
              <td><span class="status-badge <?= $f['category']==='surgical'?'status-confirmed':'status-completed' ?>"><?= ucfirst($f['category']) ?></span></td>
              <td>
                <?php if($f['is_active']): ?>
                  <span class="status-badge status-completed">Active</span>
                <?php else: ?>
                  <span class="status-badge status-cancelled">Hidden</span>
                <?php endif; ?>
              </td>
              <td><?= $f['sort_order'] ?></td>
              <td class="td-actions">
                <a href="facilities_mgr.php?toggle=<?= $f['id'] ?>" class="btn-action <?= $f['is_active']?'view':'del' ?>" title="<?= $f['is_active']?'Hide':'Show' ?>"><i class="fas fa-<?= $f['is_active']?'eye':'eye-slash' ?>"></i></a>
                <a href="facilities_mgr.php?edit=<?= $f['id'] ?>" class="btn-action edit" title="Edit"><i class="fas fa-edit"></i></a>
                <a href="facilities_mgr.php?delete=<?= $f['id'] ?>" class="btn-action del" onclick="return confirm('Delete this facility?')" title="Delete"><i class="fas fa-trash"></i></a>
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
