<?php
$page = 'book';
require_once 'includes/settings.php';  // loads $conn and cfg()

$success = '';
$error   = '';

// Load active departments for dropdown
$deptRows = $conn->query("SELECT name FROM departments WHERE is_active=1 ORDER BY sort_order ASC, name ASC");
$deptList = [];
if ($deptRows) while ($dr = $deptRows->fetch_assoc()) $deptList[] = $dr['name'];
if (empty($deptList)) $deptList = ['Cardiology','Neurology','Pediatrics','Orthopedics','Gynecology','ENT Surgery','Dental Care','Emergency Care','Radiology','General Medicine'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $first_name = trim($_POST['first_name'] ?? '');
    $last_name  = trim($_POST['last_name']  ?? '');
    $phone      = trim($_POST['phone']      ?? '');
    $email      = trim($_POST['email']      ?? '');
    $department = trim($_POST['department'] ?? '');
    $pref_date  = trim($_POST['pref_date']  ?? '');
    $message    = trim($_POST['message']    ?? '');

    if (!$first_name || !$last_name || !$phone || !$department || !$pref_date) {
        $error = 'Please fill in all required fields.';
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO appointments (first_name,last_name,phone,email,department,pref_date,message)
             VALUES (?,?,?,?,?,?,?)"
        );
        $stmt->bind_param('sssssss', $first_name, $last_name, $phone, $email, $department, $pref_date, $message);
        if ($stmt->execute()) {
            $success = 'Thank you! Your appointment request has been received. We will contact you shortly.';
        } else {
            $error = 'Something went wrong. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Book Appointment – Apex Health Care</title>
  <link rel="shortcut icon" href="assets/img/55.ico" type="image/x-icon">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
  <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<section class="booking-wrapper">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="glass-card hover-lift p-4 p-md-5" data-aos="fade-up">
          <div class="text-center mb-4">
            <h2 class="section-heading text-secondary-theme">Book an <span>Appointment</span></h2>
            <p class="section-subtext mx-auto">Fill out the form below and we will contact you to confirm your appointment.</p>
          </div>

          <?php if ($success): ?>
            <div class="alert alert-success d-flex align-items-center gap-2 mb-4" role="alert">
              <i class="fas fa-check-circle text-success"></i>
              <?= htmlspecialchars($success) ?>
            </div>
          <?php endif; ?>
          <?php if ($error): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2 mb-4" role="alert">
              <i class="fas fa-exclamation-circle text-danger"></i>
              <?= htmlspecialchars($error) ?>
            </div>
          <?php endif; ?>

          <form method="POST" action="">
            <div class="row g-4">
              <div class="col-md-6">
                <label class="form-label text-secondary-theme fw-semibold">First Name <span class="text-danger">*</span></label>
                <input type="text" name="first_name" class="form-control form-control-glass"
                       value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label text-secondary-theme fw-semibold">Last Name <span class="text-danger">*</span></label>
                <input type="text" name="last_name" class="form-control form-control-glass"
                       value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label text-secondary-theme fw-semibold">Phone Number <span class="text-danger">*</span></label>
                <input type="tel" name="phone" class="form-control form-control-glass"
                       value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label text-secondary-theme fw-semibold">Email</label>
                <input type="email" name="email" class="form-control form-control-glass"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label text-secondary-theme fw-semibold">Select Department <span class="text-danger">*</span></label>
                <select name="department" class="form-select form-control-glass" required>
                  <option value="">Choose…</option>
                  <?php foreach ($deptList as $d):
                    $sel = (($_POST['department'] ?? '') === $d) ? 'selected' : '';
                  ?>
                    <option value="<?= htmlspecialchars($d) ?>" <?= $sel ?>><?= htmlspecialchars($d) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label text-secondary-theme fw-semibold">Preferred Date <span class="text-danger">*</span></label>
                <input type="date" name="pref_date" class="form-control form-control-glass"
                       min="<?= date('Y-m-d') ?>"
                       value="<?= htmlspecialchars($_POST['pref_date'] ?? '') ?>" required>
              </div>
              <div class="col-12">
                <label class="form-label text-secondary-theme fw-semibold">Additional Message</label>
                <textarea name="message" class="form-control form-control-glass" rows="3"><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
              </div>
              <div class="col-12 text-center mt-4">
                <button type="submit" class="btn-teal border-0">
                  <i class="fas fa-calendar-check me-2"></i>Submit Appointment Request
                </button>
              </div>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
</body>
</html>
