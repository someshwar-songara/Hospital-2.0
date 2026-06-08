<?php $page = 'doctors'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dr. H. P. Sonaniya – Apex Health Care</title>
  <link rel="shortcut icon" href="assets/img/55.ico" type="image/x-icon">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
  <link rel="stylesheet" href="assets/css/doctor-profile.css">
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="page-banner">
  <div class="page-banner-bg"></div>
  <div class="page-banner-overlay"></div>
  <div class="container page-banner-content">
    <h1>Dr. H. P. Sonaniya</h1>
    <div class="breadcrumb-wrap">
      <a href="index.php">Home</a>
      <span class="sep">›</span>
      <a href="doctors.php">Doctors</a>
      <span class="sep">›</span>
      <span class="current">Dr. H. P. Sonaniya</span>
    </div>
  </div>
</div>

<section style="padding: 80px 0; background:#f8f9fa;">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-4" data-aos="fade-right">
        <div style="background:#fff; border-radius:15px; overflow:hidden; box-shadow:0 10px 30px rgba(0,0,0,0.05); text-align:center;">
          <img src="assets/img/sonaniya.jpg" alt="Dr. H. P. Sonaniya" style="width:100%; height:350px; object-fit:cover;">
          <div style="padding:30px;">
            <h3 style="color:var(--secondary-color); font-weight:700;">Dr. H. P. Sonaniya</h3>
            <span style="color:var(--primary-color); font-weight:600; display:block; margin-bottom:20px;">Cardiologist</span>
            <a href="book.php" class="btn btn-primary" style="background:var(--primary-color); border:none; padding:10px 20px; border-radius:25px; width:100%; margin-bottom:10px;"><i class="fas fa-calendar-check"></i> Book Appointment</a>
            <a href="tel:+919111XXXX13" class="btn btn-outline-secondary" style="padding:10px 20px; border-radius:25px; width:100%;"><i class="fas fa-phone"></i> Call Clinic</a>
          </div>
        </div>
      </div>
      <div class="col-lg-8" data-aos="fade-left" data-aos-delay="100">
        <div style="background:#fff; padding:40px; border-radius:15px; box-shadow:0 10px 30px rgba(0,0,0,0.05);">
          <h3 style="color:var(--secondary-color); font-weight:700; margin-bottom:20px;"><i class="fas fa-user-md" style="color:var(--primary-color);"></i> About Dr. H. P. Sonaniya</h3>
          <p style="color:#666; line-height:1.8;">Dr. H. P. Sonaniya is a highly experienced Cardiologist dedicated to providing the best possible care to patients. With years of extensive training and practice, Dr. H. P. Sonaniya brings a wealth of knowledge to Apex Health Care.</p>
          
          <h4 style="color:var(--secondary-color); font-weight:700; margin-top:40px; margin-bottom:20px;"><i class="fas fa-graduation-cap" style="color:var(--primary-color);"></i> Qualifications</h4>
          <ul style="color:#666; line-height:1.8; list-style-type:none; padding-left:0;">
            <li><i class="fas fa-check-circle" style="color:var(--primary-color); margin-right:10px;"></i> MBBS, MD / MS</li>
            <li><i class="fas fa-check-circle" style="color:var(--primary-color); margin-right:10px;"></i> Advanced Fellowship in Cardiologist</li>
            <li><i class="fas fa-check-circle" style="color:var(--primary-color); margin-right:10px;"></i> 10+ Years of Clinical Experience</li>
          </ul>

          <h4 style="color:var(--secondary-color); font-weight:700; margin-top:40px; margin-bottom:20px;"><i class="fas fa-clock" style="color:var(--primary-color);"></i> Availability</h4>
          <p style="color:#666;">Monday - Saturday: 10:00 AM - 2:00 PM & 5:00 PM - 8:00 PM</p>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
</body>
</html>
