<?php
require_once 'auth.php'; require_doctor();
require_once 'db.php';
$pageTitle = 'Staff Messages'; $activeNav = 'messages';
$did  = (int)$_SESSION['doctor_id'];
$dname= $_SESSION['doctor_name'];
$msg  = '';

// Delete
if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM staff_messages WHERE id=".(int)$_GET['delete']." AND sender_id=$did AND sender_type='doctor'");
    header('Location: messages.php?deleted=1'); exit;
}
if (isset($_GET['deleted'])) $msg = 'Message deleted.';

// Send message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_msg'])) {
    $subject   = trim($_POST['subject']  ?? '');
    $body      = trim($_POST['body']     ?? '');
    $recv_type = $_POST['receiver_type'] ?? 'all';
    $recv_id   = (int)($_POST['receiver_id'] ?? 0);
    $parent_id = (int)($_POST['parent_id']   ?? 0);

    if ($subject && $body) {
        $s = $conn->prepare("INSERT INTO staff_messages (sender_type,sender_id,sender_name,receiver_type,receiver_id,subject,body,parent_id) VALUES ('doctor',?,?,?,?,?,?,?)");
        $s->bind_param('isssisi', $did, $dname, $recv_type, $recv_id, $subject, $body, $parent_id);
        $s->execute();
        $msg = 'Message sent.';
    }
}

// Mark read on view
if (isset($_GET['view'])) {
    $vid = (int)$_GET['view'];
    $conn->query("UPDATE staff_messages SET is_read=1 WHERE id=$vid AND (receiver_type='all' OR (receiver_type='doctor' AND receiver_id=$did))");
}

// Tabs: inbox / sent / compose
$tab = $_GET['tab'] ?? 'inbox';
$showCompose = isset($_GET['action']) && $_GET['action'] === 'compose';

// Inbox
$inbox = $conn->query("SELECT * FROM staff_messages WHERE (receiver_type='all' OR (receiver_type='doctor' AND receiver_id=$did)) AND sender_id!=$did ORDER BY created_at DESC");

// Sent
$sent  = $conn->query("SELECT * FROM staff_messages WHERE sender_id=$did AND sender_type='doctor' ORDER BY created_at DESC");

// View single message
$viewMsg = null;
if (isset($_GET['view'])) $viewMsg = $conn->query("SELECT * FROM staff_messages WHERE id=".(int)$_GET['view'])->fetch_assoc();

// unread count
$unreadCount = $conn->query("SELECT COUNT(*) c FROM staff_messages WHERE (receiver_type='doctor' AND receiver_id=$did OR receiver_type='all') AND is_read=0 AND sender_id!=$did")->fetch_assoc()['c'] ?? 0;

// Staff list for sending to specific doctor
$doctorsList = $conn->query("SELECT d.id, d.name FROM doctors d JOIN doctor_accounts da ON da.doctor_id=d.id WHERE d.id!=$did AND da.is_active=1");

include 'includes/header.php';
?>

<?php if($msg): ?><div class="alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($msg) ?></div><?php endif; ?>

<div class="msg-layout">

  <!-- Left: message list -->
  <div class="msg-sidebar">
    <a href="messages.php?action=compose" class="btn-admin-primary w-100 justify-content-center mb-3">
      <i class="fas fa-pen"></i> Compose
    </a>
    <div class="msg-tabs">
      <a href="messages.php?tab=inbox" class="msg-tab <?= $tab==='inbox'?'active':'' ?>">
        <i class="fas fa-inbox"></i> Inbox <?= $unreadCount>0?"<span class='nav-badge'>$unreadCount</span>":'' ?>
      </a>
      <a href="messages.php?tab=sent" class="msg-tab <?= $tab==='sent'?'active':'' ?>">
        <i class="fas fa-paper-plane"></i> Sent
      </a>
    </div>

    <div class="msg-list">
      <?php
      $listData = ($tab === 'sent') ? $sent : $inbox;
      if ($listData->num_rows === 0):
      ?>
        <p class="empty-row">No messages.</p>
      <?php else: while ($m = $listData->fetch_assoc()):
        $isUnread = !$m['is_read'] && $tab === 'inbox';
        $activeMsg= isset($_GET['view']) && (int)$_GET['view'] === $m['id'];
      ?>
      <a href="messages.php?tab=<?= $tab ?>&view=<?= $m['id'] ?>" class="msg-item <?= $isUnread?'unread':'' ?> <?= $activeMsg?'active':'' ?>">
        <div class="msg-item-avatar"><?= strtoupper(substr($m['sender_name'],0,1)) ?></div>
        <div class="msg-item-body">
          <div class="msg-item-from"><?= htmlspecialchars($tab==='sent'?'To: '.($m['receiver_type']==='all'?'Everyone':$m['receiver_type']):$m['sender_name']) ?></div>
          <div class="msg-item-subject"><?= htmlspecialchars(mb_strimwidth($m['subject'],0,36,'…')) ?></div>
          <div class="msg-item-time"><?= date('d M, g:ia', strtotime($m['created_at'])) ?></div>
        </div>
        <?php if ($isUnread): ?><span class="msg-unread-dot"></span><?php endif; ?>
      </a>
      <?php endwhile; endif; ?>
    </div>
  </div>

  <!-- Right: detail / compose -->
  <div class="msg-main">
    <?php if ($showCompose || (isset($_GET['action']) && $_GET['action']==='reply')): ?>
    <!-- Compose -->
    <div class="admin-card">
      <div class="admin-card-header"><h3><i class="fas fa-pen"></i> New Message</h3></div>
      <form method="POST" class="admin-form">
        <input type="hidden" name="parent_id" value="<?= (int)($_GET['parent_id']??0) ?>">
        <div class="row g-3">
          <div class="col-md-6">
            <div class="field-group-admin"><label>Send To</label>
              <select name="receiver_type" id="recvType" onchange="toggleRecv()">
                <option value="all">All Staff (broadcast)</option>
                <option value="admin">Admin</option>
                <option value="doctor">Specific Doctor</option>
              </select>
            </div>
          </div>
          <div class="col-md-6" id="recvDrWrap" style="display:none">
            <div class="field-group-admin"><label>Select Doctor</label>
              <select name="receiver_id">
                <option value="0">— Select —</option>
                <?php if($doctorsList) while($dr=$doctorsList->fetch_assoc()): ?>
                  <option value="<?= $dr['id'] ?>"><?= htmlspecialchars($dr['name']) ?></option>
                <?php endwhile; ?>
              </select>
            </div>
          </div>
        </div>
        <div class="field-group-admin"><label>Subject</label>
          <input type="text" name="subject" required value="<?= isset($_GET['re'])?'Re: '.htmlspecialchars(urldecode($_GET['re'])):'' ?>"></div>
        <div class="field-group-admin"><label>Message</label>
          <textarea name="body" rows="6" required></textarea></div>
        <button type="submit" name="send_msg" class="btn-admin-primary"><i class="fas fa-paper-plane"></i> Send Message</button>
      </form>
    </div>

    <?php elseif ($viewMsg): ?>
    <!-- View message -->
    <div class="admin-card">
      <div class="admin-card-header">
        <h3><i class="fas fa-envelope-open-text"></i> <?= htmlspecialchars($viewMsg['subject']) ?></h3>
        <div class="d-flex gap-2">
          <a href="messages.php?action=compose&parent_id=<?= $viewMsg['id'] ?>&re=<?= urlencode($viewMsg['subject']) ?>" class="btn-sm-teal"><i class="fas fa-reply"></i> Reply</a>
          <?php if ($viewMsg['sender_id'] === $did): ?>
            <a href="messages.php?delete=<?= $viewMsg['id'] ?>" class="btn-sm-outline" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></a>
          <?php endif; ?>
        </div>
      </div>
      <div class="admin-form">
        <div class="msg-view-meta">
          <span><strong>From:</strong> <?= htmlspecialchars($viewMsg['sender_name']) ?></span>
          <span><strong>To:</strong> <?= $viewMsg['receiver_type']==='all'?'All Staff':ucfirst($viewMsg['receiver_type']) ?></span>
          <span><strong>Date:</strong> <?= date('d M Y, g:ia', strtotime($viewMsg['created_at'])) ?></span>
        </div>
        <div class="msg-body-text"><?= nl2br(htmlspecialchars($viewMsg['body'])) ?></div>
      </div>
    </div>

    <?php else: ?>
    <div class="msg-empty-state">
      <i class="fas fa-comments"></i>
      <h3>Select a message</h3>
      <p>Choose a message from the list, or compose a new one.</p>
      <a href="messages.php?action=compose" class="btn-admin-primary"><i class="fas fa-pen"></i> Compose</a>
    </div>
    <?php endif; ?>
  </div>
</div>

<script>
function toggleRecv() {
  const v = document.getElementById('recvType').value;
  document.getElementById('recvDrWrap').style.display = v === 'doctor' ? 'block' : 'none';
}
</script>

<?php include 'includes/footer.php'; ?>
