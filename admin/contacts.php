<?php
require_once 'auth.php';
require_login();
require_once 'db.php';

$pageTitle = 'Contact Messages';
$activeNav = 'contacts';

// Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM contacts WHERE id=$id");
    header('Location: contacts.php?deleted=1');
    exit;
}

// View / mark read
$viewMsg = null;
if (isset($_GET['view'])) {
    $id = (int)$_GET['view'];
    $conn->query("UPDATE contacts SET is_read=1 WHERE id=$id");
    $viewMsg = $conn->query("SELECT * FROM contacts WHERE id=$id")->fetch_assoc();
}

$msg = '';
if (isset($_GET['deleted'])) $msg = 'Message deleted.';

// List
$search  = trim($_GET['q'] ?? '');
$filter  = $_GET['filter'] ?? 'all';
$where   = [];
$params  = [];
$types   = '';
if ($filter === 'unread') { $where[] = 'is_read=0'; }
if ($search !== '') {
    $like = "%$search%";
    $where[] = "(name LIKE ? OR email LIKE ? OR subject LIKE ?)";
    array_push($params, $like, $like, $like);
    $types .= 'sss';
}
$whereSQL = $where ? 'WHERE '.implode(' AND ', $where) : '';
$stmt = $conn->prepare("SELECT * FROM contacts $whereSQL ORDER BY created_at DESC");
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$contacts = $stmt->get_result();

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
      <input type="text" name="q" placeholder="Search name, email, subject…" value="<?= htmlspecialchars($search) ?>">
    </div>
    <select name="filter" onchange="this.form.submit()">
      <option value="all"    <?= $filter==='all'    ? 'selected':'' ?>>All Messages</option>
      <option value="unread" <?= $filter==='unread' ? 'selected':'' ?>>Unread Only</option>
    </select>
    <button type="submit" class="btn-sm-teal">Search</button>
    <a href="contacts.php" class="btn-sm-outline">Reset</a>
  </form>
</div>

<div class="admin-card">
  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr><th>#</th><th>Name</th><th>Email</th><th>Subject</th><th>Date</th><th>Status</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php if ($contacts->num_rows === 0): ?>
          <tr><td colspan="7" class="empty-row">No messages found.</td></tr>
        <?php else: ?>
          <?php while ($c = $contacts->fetch_assoc()): ?>
          <tr class="<?= !$c['is_read'] ? 'row-unread' : '' ?>">
            <td><?= $c['id'] ?></td>
            <td><?= htmlspecialchars($c['name']) ?></td>
            <td><?= htmlspecialchars($c['email']) ?></td>
            <td><?= htmlspecialchars(mb_strimwidth($c['subject'],0,50,'…')) ?></td>
            <td><?= date('d M Y, g:ia', strtotime($c['created_at'])) ?></td>
            <td>
              <?php if (!$c['is_read']): ?>
                <span class="status-badge status-pending">Unread</span>
              <?php else: ?>
                <span class="status-badge status-completed">Read</span>
              <?php endif; ?>
            </td>
            <td class="td-actions">
              <a href="contacts.php?view=<?= $c['id'] ?>" class="btn-action view" title="View"><i class="fas fa-eye"></i></a>
              <a href="contacts.php?delete=<?= $c['id'] ?>" class="btn-action del"
                 onclick="return confirm('Delete this message?')" title="Delete"><i class="fas fa-trash"></i></a>
            </td>
          </tr>
          <?php endwhile; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- View Message Modal -->
<?php if ($viewMsg): ?>
<div class="modal-backdrop-custom" id="viewModal">
  <div class="modal-box">
    <div class="modal-box-header">
      <h4><i class="fas fa-envelope-open-text"></i> Message from <?= htmlspecialchars($viewMsg['name']) ?></h4>
      <a href="contacts.php" class="modal-close"><i class="fas fa-times"></i></a>
    </div>
    <div class="modal-box-body">
      <table class="detail-table">
        <tr><th>Name</th><td><?= htmlspecialchars($viewMsg['name']) ?></td></tr>
        <tr><th>Email</th><td><a href="mailto:<?= htmlspecialchars($viewMsg['email']) ?>"><?= htmlspecialchars($viewMsg['email']) ?></a></td></tr>
        <tr><th>Subject</th><td><?= htmlspecialchars($viewMsg['subject']) ?></td></tr>
        <tr><th>Received</th><td><?= date('d M Y, g:ia', strtotime($viewMsg['created_at'])) ?></td></tr>
        <tr><th>Message</th><td><?= nl2br(htmlspecialchars($viewMsg['message'])) ?></td></tr>
      </table>
      <div class="mt-3 d-flex gap-2 flex-wrap">
        <a href="mailto:<?= htmlspecialchars($viewMsg['email']) ?>" class="btn-admin-primary">
          <i class="fas fa-reply"></i> Reply via Email
        </a>
        <a href="contacts.php" class="btn-admin-outline">Close</a>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
