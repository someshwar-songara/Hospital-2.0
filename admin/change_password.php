<?php
require_once 'auth.php'; require_login();
require_once 'db.php';
$pageTitle = 'Change Password'; $activeNav = 'password';

$msg = ''; $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current  = $_POST['current_password']  ?? '';
    $new      = $_POST['new_password']       ?? '';
    $confirm  = $_POST['confirm_password']   ?? '';

    if (!$current || !$new || !$confirm) {
        $error = 'All fields are required.';
    } elseif (strlen($new) < 6) {
        $error = 'New password must be at least 6 characters.';
    } elseif ($new !== $confirm) {
        $error = 'New passwords do not match.';
    } else {
        $id   = (int)$_SESSION['admin_id'];
        $row  = $conn->query("SELECT password FROM admin_users WHERE id=$id")->fetch_assoc();
        if (!$row || !password_verify($current, $row['password'])) {
            $error = 'Current password is incorrect.';
        } else {
            $hash = password_hash($new, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE admin_users SET password=? WHERE id=?");
            $stmt->bind_param('si', $hash, $id);
            $stmt->execute();
            $msg = 'Password changed successfully.';
        }
    }
}
include 'includes/header.php';
?>

<?php if($msg):?><div class="alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($msg) ?></div><?php endif;?>
<?php if($error):?><div class="alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div><?php endif;?>

<div style="max-width:460px">
  <div class="admin-card">
    <div class="admin-card-header"><h3><i class="fas fa-key"></i> Change Password</h3></div>
    <form method="POST" class="admin-form">
      <div class="field-group-admin"><label>Current Password <span class="required">*</span></label>
        <input type="password" name="current_password" required></div>
      <div class="field-group-admin"><label>New Password <span class="required">*</span></label>
        <input type="password" name="new_password" required minlength="6"></div>
      <div class="field-group-admin"><label>Confirm New Password <span class="required">*</span></label>
        <input type="password" name="confirm_password" required minlength="6"></div>
      <button type="submit" class="btn-admin-primary mt-2"><i class="fas fa-save"></i> Update Password</button>
    </form>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
