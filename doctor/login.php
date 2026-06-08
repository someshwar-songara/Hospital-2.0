<?php
require_once 'auth.php';
if (doctor_logged_in()) { header('Location: index.php'); exit; }

require_once 'db.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $stmt = $conn->prepare("
            SELECT da.id, da.password, da.is_active, d.id AS doctor_id,
                   d.name, d.specialty, d.photo
            FROM doctor_accounts da
            JOIN doctors d ON d.id = da.doctor_id
            WHERE da.username = ? LIMIT 1
        ");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        if ($row && $row['is_active'] && password_verify($password, $row['password'])) {
            $_SESSION['doctor_id']        = $row['doctor_id'];
            $_SESSION['doctor_account_id']= $row['id'];
            $_SESSION['doctor_name']      = $row['name'];
            $_SESSION['doctor_specialty'] = $row['specialty'];
            $_SESSION['doctor_photo']     = $row['photo'];
            // Update last login
            $conn->query("UPDATE doctor_accounts SET last_login=NOW() WHERE id=".(int)$row['id']);
            header('Location: index.php'); exit;
        }
        $error = 'Invalid username or password.';
    } else {
        $error = 'Please enter both fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Doctor Login – Apex Health Care</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../admin/assets/admin.css">
  <link rel="stylesheet" href="assets/doctor.css">
</head>
<body class="admin-login-page">
  <div class="login-wrapper">
    <div class="login-card">
      <div class="login-logo"><img src="../assets/img/logo.png" alt="Apex Health Care"></div>
      <h2>Doctor Portal</h2>
      <p class="login-sub">Sign in to access your clinical dashboard</p>

      <?php if ($error): ?>
        <div class="alert-error mb-3"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="field-group">
          <label>Username</label>
          <div class="input-icon-wrap">
            <i class="fas fa-user"></i>
            <input type="text" name="username" placeholder="doctor username" autofocus
                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
          </div>
        </div>
        <div class="field-group">
          <label>Password</label>
          <div class="input-icon-wrap">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" id="pwd" placeholder="password" required>
            <button type="button" class="toggle-pwd" onclick="togglePwd()"><i class="fas fa-eye" id="pIcon"></i></button>
          </div>
        </div>
        <button type="submit" class="btn-login">Sign In <i class="fas fa-arrow-right"></i></button>
      </form>
      <a href="../admin/login.php" class="back-link"><i class="fas fa-shield-alt"></i> Admin Login</a>
      &nbsp;·&nbsp;
      <a href="../index.php" class="back-link"><i class="fas fa-arrow-left"></i> Website</a>
    </div>
  </div>
  <script>
    function togglePwd() {
      const i = document.getElementById('pwd'), ic = document.getElementById('pIcon');
      i.type = i.type === 'password' ? 'text' : 'password';
      ic.classList.toggle('fa-eye'); ic.classList.toggle('fa-eye-slash');
    }
  </script>
</body>
</html>
