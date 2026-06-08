<?php
$page    = 'contact';
require_once 'includes/settings.php';
$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['Name']    ?? '');
    $email   = trim($_POST['Email']   ?? '');
    $subject = trim($_POST['Subject'] ?? '');
    $message = trim($_POST['Message'] ?? '');

    if (!$name || !$email || !$subject || !$message) {
        $error = 'Please fill in all fields.';
    } else {
        $stmt = $conn->prepare("INSERT INTO contacts (name,email,subject,message) VALUES (?,?,?,?)");
        $stmt->bind_param('ssss', $name, $email, $subject, $message);
        if ($stmt->execute()) {
            $success = 'Thank you! Your message has been sent. We will get back to you soon.';
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
  <title>Contact Us – Apex Health Care</title>
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
  <link rel="stylesheet" href="assets/css/contact.css">
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="page-banner">
  <div class="page-banner-bg"></div>
  <div class="page-banner-overlay"></div>
  <div class="container page-banner-content">
    <h1>Contact <span>Us</span></h1>
    <div class="breadcrumb-wrap">
      <a href="index.php">Home</a>
      <span class="sep">›</span>
      <span class="current">Contact</span>
    </div>
  </div>
</div>

<section class="contact-section">
  <div class="container">
    <div class="row g-5">
      <!-- Contact Info -->
      <div class="col-lg-5" data-aos="fade-right">
        <div class="glass-card p-4 p-md-4 mb-4 mb-lg-0">
          <h3 class="section-heading mb-4 text-secondary-theme">Get in Touch</h3>
          
          <div class="d-flex align-items-start mb-4">
            <div class="icon-box-teal me-3 flex-shrink-0" style="width:50px;height:50px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:rgba(0,201,167,0.1);color:var(--teal);font-size:1.2rem;">
              <i class="fas fa-map-marker-alt"></i>
            </div>
            <div>
              <h5 class="text-secondary-theme fw-semibold mb-1">Address</h5>
              <p class="text-muted mb-0"><?= nl2br(htmlspecialchars(cfg('hospital_address','Apex Health Care, Freeganj, Ujjain, MP 456001'))) ?></p>
            </div>
          </div>

          <div class="d-flex align-items-start mb-4">
            <div class="icon-box-teal me-3 flex-shrink-0" style="width:50px;height:50px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:rgba(0,201,167,0.1);color:var(--teal);font-size:1.2rem;">
              <i class="fas fa-phone"></i>
            </div>
            <div>
              <h5 class="text-secondary-theme fw-semibold mb-1">Phone</h5>
              <p class="text-muted mb-0"><?= htmlspecialchars(cfg('hospital_phone','+91 9111XXXX13')) ?></p>
            </div>
          </div>

          <div class="d-flex align-items-start">
            <div class="icon-box-teal me-3 flex-shrink-0" style="width:50px;height:50px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:rgba(0,201,167,0.1);color:var(--teal);font-size:1.2rem;">
              <i class="fas fa-envelope"></i>
            </div>
            <div>
              <h5 class="text-secondary-theme fw-semibold mb-1">Email</h5>
              <p class="text-muted mb-0"><?= htmlspecialchars(cfg('hospital_email','apex.healthcare@gmail.com')) ?></p>
            </div>
          </div>

        </div>
      </div>

      <!-- Contact Form -->
      <div class="col-lg-7" data-aos="fade-left" data-aos-delay="100">
        <div class="glass-card p-4 p-md-4">
          <h3 class="section-heading mb-4 text-secondary-theme">Send a Message</h3>

          <?php if ($success): ?>
            <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
              <i class="fas fa-check-circle text-success"></i> <?= htmlspecialchars($success) ?>
            </div>
          <?php endif; ?>
          <?php if ($error): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
              <i class="fas fa-exclamation-circle text-danger"></i> <?= htmlspecialchars($error) ?>
            </div>
          <?php endif; ?>

          <form method="POST" action="">
            <div class="row g-4">
              <div class="col-md-6">
                <input type="text" name="Name" class="form-control form-control-glass" placeholder="Your Name" required>
              </div>
              <div class="col-md-6">
                <input type="email" name="Email" class="form-control form-control-glass" placeholder="Your Email" required>
              </div>
              <div class="col-12">
                <input type="text" name="Subject" class="form-control form-control-glass" placeholder="Subject" required>
              </div>
              <div class="col-12">
                <textarea name="Message" class="form-control form-control-glass" rows="5" placeholder="Your Message" required></textarea>
              </div>
              <div class="col-12">
                <button type="submit" class="btn-teal border-0">Send Message</button>
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