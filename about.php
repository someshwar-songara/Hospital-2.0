<?php $page = 'about'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>About Us – Apex Health Care</title>
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
  <link rel="stylesheet" href="assets/css/about.css">
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<!-- PAGE BANNER -->
<div class="page-banner">
  <div class="page-banner-bg"></div>
  <div class="page-banner-overlay"></div>
  <div class="container page-banner-content">
    <h1>About <span>Us</span></h1>
    <div class="breadcrumb-wrap">
      <a href="index.php">Home</a>
      <span class="sep">›</span>
      <span class="current">About Us</span>
    </div>
  </div>
</div>

<!-- ABOUT SECTION -->
<section id="about" class="about">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-5 col-md-6" data-aos="fade-right">
        <div id="aboutCarousel" class="carousel slide hover-lift" data-bs-ride="carousel">
          <div class="carousel-inner glass-card">
            <div class="carousel-item active">
              <img src="assets/img/about-us-1.jpg" alt="Our Hospital" class="about-image">
            </div>
            <div class="carousel-item">
              <img src="assets/img/about-us-2.jpg" alt="Our Team" class="about-image">
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-7 col-md-6 about-content" data-aos="fade-left" data-aos-delay="100">
        <span class="section-eyebrow">Who We Are</span>
        <h2 class="section-heading">About Our <span>Hospital</span></h2>
        <p>We are a team of dedicated healthcare professionals committed to providing exceptional care to our patients. Our mission is to deliver high-quality medical services with compassion and expertise.</p>
        <p>We believe in building strong relationships with our patients and working collaboratively to achieve their health goals. At our hospital, patient well-being is our top priority.</p>

        <div class="about-features">
          <div class="feature-card glass-card">
            <i class="fas fa-user-md about-icon"></i>
            <h4>Experienced Doctors</h4>
            <p>Highly qualified specialists.</p>
          </div>
          <div class="feature-card glass-card">
            <i class="fas fa-heartbeat about-icon"></i>
            <h4>Comprehensive Services</h4>
            <p>From preventive to specialised.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- WHY CHOOSE US -->
<section id="why-choose-us" class="bg-secondary-theme">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <span class="section-eyebrow">Our Advantages</span>
      <h2 class="section-heading" style="color: white;">Why <span>Choose Us</span></h2>
    </div>
    <div class="row g-4">
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="0">
        <div class="white-box glass-card-dark hover-lift">
          <img src="assets/img/trustworthy2.jpg" alt="Trustworthy Care">
          <h4>Trustworthy Care</h4>
          <p>Building trust between patient and provider is the foundation of everything we do.</p>
        </div>
      </div>
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
        <div class="white-box glass-card-dark hover-lift">
          <img src="assets/img/save-money-image.jpg" alt="Save Money">
          <h4>Save Money</h4>
          <p>We offer competitive pricing and cost-effective solutions.</p>
        </div>
      </div>
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
        <div class="white-box glass-card-dark hover-lift">
          <img src="assets/img/flexible-life-insurance-image.jpg" alt="Flexible Insurance">
          <h4>Flexible Insurance</h4>
          <p>We provide flexible life insurance options to ensure financial security.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
</body>
</html>