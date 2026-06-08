<?php
require_once 'auth.php'; require_login();
require_once 'db.php';
$pageTitle = 'Testimonials'; $activeNav = 'testimonials';

$msg = ''; $error = '';

// Delete
if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM testimonials WHERE id=".(int)$_GET['delete']);
    header('Location: testimonials.php?deleted=1'); exit;
}
if (isset($_GET['deleted'])) $msg = 'Testimonial deleted.';

// Toggle active
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $conn->query("UPDATE testimonials SET is_active = 1-is_active WHERE id=$id");
    header('Location: testimonials.php'); exit;
}

// Save
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name       = trim($_POST['name']   ?? '');
    $photo      = trim($_POST['photo']  ?? '');
    $quote      = trim($_POST['quote']  ?? '');
    $rating     = max(1, min(5, (int)($_POST['rating'] ?? 5)));
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    $edit_id    = (int)($_POST['edit_id'] ?? 0);

    if (!$name || !$quote) { $error = 'Name and quote are required.'; }
    else {
        if ($edit_id > 0) {
            $s = $conn->prepare("UPDATE testimonials SET name=?,photo=?,quote=?,rating=?,sort_order=? WHERE id=?");
            $s->bind_param('sssiii',$name,$photo,$quote,$rating,$sort_order,$edit_id);
        } else {
            $s = $conn->prepare("INSERT INTO testimonials (name,photo,quote,rating,sort_order) VALUES (?,?,?,?,?)");
            $s->bind_param('sssii',$name,$photo,$quote,$rating,$sort_order);
        }
        $s->execute();
        $msg = $edit_id ? 'Testimonial updated.' : 'Testimonial added.';
    }
}

$editRow = null;
if (isset($_GET['edit'])) {
    $editRow = $conn->query("SELECT * FROM testimonials WHERE id=".(int)$_GET['edit'])->fetch_assoc();
}

$list = $conn->query("SELECT * FROM testimonials ORDER BY sort_order ASC, id ASC");
include 'includes/header.php';
?>

<?php if($msg):?><div class="alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($msg) ?></div><?php endif;?>
<?php if($error):?><div class="alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div><?php endif;?>

<div class="row-admin g-4">
  <!-- Form -->
  <div class="col-admin-form">
    <div class="admin-card">
      <div class="admin-card-header">
        <h3><i class="fas fa-<?= $editRow?'edit':'plus' ?>"></i> <?= $editRow?'Edit':'Add' ?> Testimonial</h3>
      </div>
      <form method="POST" class="admin-form">
        <input type="hidden" name="edit_id" value="<?= $editRow['id']??0 ?>">
        <div class="field-group-admin"><label>Patient Name <span class="required">*</span></label>
          <input type="text" name="name" required value="<?= htmlspecialchars($editRow['name']??'') ?>"></div>
        <div class="field-group-admin"><label>Photo Path <small>(relative to site root)</small></label>
          <input type="text" name="photo" value="<?= htmlspecialchars($editRow['photo']??'') ?>" placeholder="assets/img/testimonial1.jpg"></div>
        <div class="field-group-admin"><label>Quote / Review <span class="required">*</span></label>
          <textarea name="quote" rows="4" required><?= htmlspecialchars($editRow['quote']??'') ?></textarea></div>
        <div class="field-group-admin"><label>Rating (1–5)</label>
          <select name="rating">
            <?php for($i=5;$i>=1;$i--): ?>
              <option value="<?= $i ?>" <?= ($editRow['rating']??5)==$i?'selected':'' ?>><?= $i ?> Star<?= $i>1?'s':'' ?></option>
            <?php endfor; ?>
          </select></div>
        <div class="field-group-admin"><label>Sort Order <small>(lower = first)</small></label>
          <input type="number" name="sort_order" value="<?= $editRow['sort_order']??0 ?>"></div>
        <div class="d-flex gap-2 mt-3">
          <button type="submit" class="btn-admin-primary"><i class="fas fa-save"></i> <?= $editRow?'Update':'Add' ?></button>
          <?php if($editRow):?><a href="testimonials.php" class="btn-admin-outline">Cancel</a><?php endif;?>
        </div>
      </form>
    </div>
  </div>

  <!-- List -->
  <div class="col-admin-list">
    <div class="admin-card">
      <div class="admin-card-header"><h3><i class="fas fa-star"></i> All Testimonials (<?= $list->num_rows ?>)</h3></div>
      <?php if($list->num_rows===0): ?>
        <p class="empty-row">No testimonials yet.</p>
      <?php else: ?>
        <?php while($t=$list->fetch_assoc()): ?>
        <div class="testimonial-admin-card <?= !$t['is_active']?'inactive':'' ?>">
          <div class="testi-photo">
            <?php if($t['photo']): ?>
              <img src="../<?= htmlspecialchars($t['photo']) ?>" alt="<?= htmlspecialchars($t['name']) ?>">
            <?php else: ?>
              <div class="doc-photo-placeholder"><i class="fas fa-user"></i></div>
            <?php endif; ?>
          </div>
          <div class="testi-info">
            <strong><?= htmlspecialchars($t['name']) ?></strong>
            <span class="testi-stars">
              <?php for($i=1;$i<=5;$i++) echo $i<=$t['rating']?'★':'☆'; ?>
            </span>
            <p>"<?= htmlspecialchars(mb_strimwidth($t['quote'],0,80,'…')) ?>"</p>
          </div>
          <div class="doc-actions">
            <a href="testimonials.php?toggle=<?= $t['id'] ?>" class="btn-action <?= $t['is_active']?'view':'del' ?>" title="<?= $t['is_active']?'Deactivate':'Activate' ?>">
              <i class="fas fa-<?= $t['is_active']?'eye':'eye-slash' ?>"></i>
            </a>
            <a href="testimonials.php?edit=<?= $t['id'] ?>" class="btn-action edit" title="Edit"><i class="fas fa-edit"></i></a>
            <a href="testimonials.php?delete=<?= $t['id'] ?>" class="btn-action del" onclick="return confirm('Delete this testimonial?')" title="Delete"><i class="fas fa-trash"></i></a>
          </div>
        </div>
        <?php endwhile; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
