<?php
require_once 'auth.php';
if (is_logged_in()) { header('Location: index.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'db.php';
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } else {
        $stmt = $conn->prepare("SELECT id, name, password FROM admin_users WHERE username = ? LIMIT 1");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if ($row && password_verify($password, $row['password'])) {
            $_SESSION['admin_id']   = $row['id'];
            $_SESSION['admin_name'] = $row['name'];
            header('Location: index.php');
            exit;
        }
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Login – Apex Health Care</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="assets/admin.css">
</head>
<body class="admin-login-page">
  <div class="login-wrapper">
    <div class="login-card">
      <div class="login-logo">
        <img src="../assets/img/logo.png" alt="Apex Health Care">
      </div>
      <h2>Admin Panel</h2>
      <p class="login-sub">Sign in to manage your hospital</p>

      <?php if ($error): ?>
        <div class="alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="field-group">
          <label>Username</label>
          <div class="input-icon-wrap">
            <i class="fas fa-user"></i>
            <input type="text" name="username" placeholder="Enter username"
                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required autofocus>
          </div>
        </div>
        <div class="field-group">
          <label>Password</label>
          <div class="input-icon-wrap">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" placeholder="Enter password" required id="pwdInput">
            <button type="button" class="toggle-pwd" onclick="togglePwd()">
              <i class="fas fa-eye" id="pwdIcon"></i>
            </button>
          </div>
        </div>
        <button type="submit" class="btn-login">Sign In <i class="fas fa-arrow-right"></i></button>
      </form>

      <a href="../index.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Website</a>
    </div>
  </div>

  <script>
    function togglePwd() {
      const inp = document.getElementById('pwdInput');
      const ico = document.getElementById('pwdIcon');
      if (inp.type === 'password') { inp.type = 'text'; ico.classList.replace('fa-eye','fa-eye-slash'); }
      else { inp.type = 'password'; ico.classList.replace('fa-eye-slash','fa-eye'); }
    }
  </script>
</body>
</html>
