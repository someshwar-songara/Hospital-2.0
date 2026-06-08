<?php $page = 'doctors'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dr. Rahul Tejankar – Apex Health Care</title>
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
  <link rel="stylesheet" href="assets/css/doctor-profile.css">
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="page-banner">
  <div class="page-banner-bg"></div>
  <div class="page-banner-overlay"></div>
  <div class="container page-banner-content">
    <h1>Dr. Rahul Tejankar</h1>
    <div class="breadcrumb-wrap">
      <a href="index.php">Home</a>
      <span class="sep">›</span>
      <a href="doctors.php">Doctors</a>
      <span class="sep">›</span>
      <span class="current">Dr. Rahul Tejankar</span>
    </div>
  </div>
</div>

<section class="profile-section">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-4" data-aos="fade-right">
        <div class="doctor-photo-card text-center">
          <img src="assets/img/tejanakar.jpg" alt="Dr. Rahul Tejankar">
          <div class="photo-card-body">
            <h2>Dr. Rahul Tejankar</h2>
            <span class="specialty-tag"><i class="fas fa-stethoscope"></i> ENT Surgery</span>
            <a href="book.php" class="btn-book mb-2"><i class="fas fa-calendar-check"></i> Book Appointment</a>
            <a href="tel:+919111XXXX13" class="btn-call"><i class="fas fa-phone"></i> Call Clinic</a>
          </div>
        </div>
      </div>
      <div class="col-lg-8" data-aos="fade-left" data-aos-delay="100">
        <div class="doctor-info-panel">
          <div class="info-block">
            <h3 class="block-title"><i class="fas fa-user-md"></i> About Dr. Rahul Tejankar</h3>
            <p>Dr. Rahul Tejankar is a highly experienced ENT Surgeon dedicated to providing the best possible care to patients. With years of extensive training and practice, Dr. Tejankar brings a wealth of knowledge to Apex Health Care.</p>
            
            <h4 class="block-title mt-4"><i class="fas fa-graduation-cap"></i> Qualifications</h4>
            <ul class="qual-list">
              <li><i class="fas fa-check-circle"></i> MBBS, MS</li>
              <li><i class="fas fa-check-circle"></i> Fellowship in ENT Surgery</li>
              <li><i class="fas fa-check-circle"></i> 10+ Years of Clinical Experience</li>
            </ul>

            <h4 class="block-title mt-4"><i class="fas fa-clock"></i> Availability</h4>
            <p>Monday - Saturday: 10:00 AM - 2:00 PM & 5:00 PM - 8:00 PM</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
</body>
</html>
