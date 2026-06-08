<?php
$page = 'home';
require_once 'includes/settings.php';

// Load active testimonials from DB
$testimonials = $conn->query("SELECT * FROM testimonials WHERE is_active=1 ORDER BY sort_order ASC, id ASC");

// Load 3 featured facilities
$featuredFacs = $conn->query("SELECT * FROM facilities WHERE is_active=1 ORDER BY sort_order ASC LIMIT 3");


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars(cfg('hospital_name','Apex Health Care')) ?> – Expert Medical Care in Ujjain</title>
  <meta name="description" content="<?= htmlspecialchars(cfg('hospital_name','Apex Health Care')) ?> offers world-class medical services in Ujjain, Madhya Pradesh. Book an appointment with our expert doctors today.">
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

<!-- HERO -->
<section id="hospital-info">
  <div class="hero-bg"></div>
  <div class="hero-overlay"></div>
  <div class="container d-flex flex-column" style="min-height:100vh;min-height:100svh;">
    <div class="hero-content">
      <div class="hero-badge"><i class="fas fa-heartbeat"></i> <?= htmlspecialchars(cfg('hero_badge','24/7 Emergency Service')) ?></div>
      <h1 class="hero-title">
        <?= htmlspecialchars(cfg('hero_title_line1','Advanced Care.')) ?><br>
        <span><?= htmlspecialchars(cfg('hero_title_line2','Exceptional Health.')) ?></span>
      </h1>
      <p class="hero-subtitle"><?= htmlspecialchars(cfg('hero_subtitle','Welcome to Apex Health Care, the premier medical facility in Ujjain.')) ?></p>
      <div class="hero-actions">
        <a href="book.php" class="read-more-btn btn-primary-hero">Book Appointment <i class="fas fa-arrow-right"></i></a>
        <a href="about.php" class="read-more-btn btn-outline-hero">Learn More</a>
      </div>
    </div>
    <!-- Stats -->
    <div class="hero-stats w-100 mt-auto">
      <div class="row g-0 justify-content-center">
        <?php for($i=1;$i<=4;$i++): ?>
        <div class="col-6 col-md-3 stat-item" data-aos="fade-up" data-aos-delay="<?= $i*100 ?>">
          <span class="stat-number"><?= htmlspecialchars(cfg("stat{$i}_number",['25+','10k+','15+','24/7'][$i-1])) ?></span>
          <div class="stat-label"><?= htmlspecialchars(cfg("stat{$i}_label",['Specialist Doctors','Happy Patients','Years Experience','Emergency Care'][$i-1])) ?></div>
        </div>
        <?php endfor; ?>
      </div>
    </div>
  </div>
  <div class="hero-scroll"><span>Scroll</span><i class="fas fa-chevron-down"></i></div>
</section>

<!-- ABOUT PREVIEW -->
<section class="about">
  <div class="container">
    <div class="row align-items-center g-4 g-lg-0">
      <div class="col-12 col-lg-5" data-aos="fade-right">
        <div class="position-relative">
          <img src="assets/img/about-us.jpg" alt="About <?= htmlspecialchars(cfg('hospital_name','Apex Health Care')) ?>" class="about-image">
          <div class="about-badge-card"><i class="fas fa-award"></i><div><h4>Top Rated</h4><p>Hospital in Ujjain</p></div></div>
        </div>
      </div>
      <div class="col-12 col-lg-6 offset-lg-1 about-content" data-aos="fade-left">
        <span class="section-eyebrow">Who We Are</span>
        <h2 class="section-heading">Commitment to <span>Excellence</span></h2>
        <p>At <?= htmlspecialchars(cfg('hospital_name','Apex Health Care')) ?>, we believe that health is your greatest wealth. Our hospital is built on a foundation of compassionate care, advanced medical technology, and highly skilled professionals.</p>
        <div class="about-features">
          <div class="feature-card"><i class="fas fa-stethoscope about-icon"></i><h4>Expert Doctors</h4><p>Our specialists are leaders in their respective fields.</p></div>
          <div class="feature-card"><i class="fas fa-microscope about-icon"></i><h4>Modern Tech</h4><p>Equipped with state-of-the-art medical technology.</p></div>
        </div>
        <a href="about.php" class="btn-teal">Read More About Us <i class="fas fa-arrow-right ms-2"></i></a>
      </div>
    </div>
  </div>
</section>

<!-- FACILITIES PREVIEW (dynamic) -->
<section class="facilities" style="background:var(--grey-50);">
  <div class="container">
    <div class="row align-items-end mb-5 g-3">
      <div class="col-12 col-md-8" data-aos="fade-up">
        <span class="section-eyebrow">Our Specialties</span>
        <h2 class="section-heading mb-0">Center of <span>Excellence</span></h2>
      </div>
      <div class="col-12 col-md-4 d-flex justify-content-md-end" data-aos="fade-up" data-aos-delay="100">
        <a href="facilities.php" class="btn-teal btn-teal-outline w-100 w-md-auto justify-content-center">View All Facilities</a>
      </div>
    </div>
    <div class="row g-4">
      <?php
      $iconMap = ['cardiology'=>'fa-heartbeat','pediatrics'=>'fa-baby','emergency'=>'fa-ambulance','surgery'=>'fa-scalpel','radiology'=>'fa-x-ray','dental'=>'fa-tooth','laboratory'=>'fa-flask','pharmacy'=>'fa-pills','physical'=>'fa-walking'];
      $delay = 100;
      if ($featuredFacs->num_rows > 0):
        while ($fac = $featuredFacs->fetch_assoc()):
          $icon = 'fa-hospital';
          foreach ($iconMap as $kw=>$ic) if (stripos($fac['name'],$kw)!==false) { $icon=$ic; break; }
      ?>
      <div class="col-12 col-sm-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= $delay ?>">
        <div class="service-item">
          <div class="service-image-wrap">
            <img src="<?= htmlspecialchars($fac['image']) ?>" alt="<?= htmlspecialchars($fac['name']) ?>" class="service-image">
            <div class="service-icon-badge"><i class="fas <?= $icon ?>"></i></div>
          </div>
          <div class="service-body">
            <h3><?= htmlspecialchars($fac['name']) ?></h3>
            <p><?= htmlspecialchars($fac['description']) ?></p>
          </div>
        </div>
      </div>
      <?php $delay+=100; endwhile; else: ?>
      <!-- fallback static -->
      <div class="col-12 col-sm-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
        <div class="service-item"><div class="service-image-wrap"><img src="assets/img/cardiology.jpg" alt="Cardiology" class="service-image"><div class="service-icon-badge"><i class="fas fa-heartbeat"></i></div></div><div class="service-body"><h3>Cardiology</h3><p>Comprehensive heart care, diagnosis, and advanced treatments.</p></div></div>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- HOW IT WORKS -->
<section class="how-it-works-section" style="padding:80px 0;background:#fff;">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <span class="section-eyebrow">Simple Process</span>
      <h2 class="section-heading">How It <span>Works</span></h2>
      <p class="text-muted" style="max-width:560px;margin:0 auto;">Getting the care you need is easy. Follow these simple steps to begin your health journey with us.</p>
    </div>
    <div class="row g-4 text-center">
      <?php
      $steps = [
        ['fa-search','01','Find a Doctor','Browse our team of expert specialists and find the right doctor for your condition.'],
        ['fa-calendar-check','02','Book Appointment','Schedule your visit online at a time that suits you — quick and hassle-free.'],
        ['fa-user-md','03','Get Consultation','Meet your doctor for a thorough consultation and personalised care plan.'],
        ['fa-heart','04','Recover & Thrive','Follow your treatment plan and enjoy ongoing support from our care team.'],
      ];
      foreach ($steps as $i=>$st):
      ?>
      <div class="col-12 col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="<?= ($i+1)*100 ?>">
        <div class="hiw-card">
          <div class="hiw-icon-wrap"><i class="fas <?= $st[0] ?>"></i></div>
          <div class="hiw-step"><?= $st[1] ?></div>
          <h4><?= $st[2] ?></h4>
          <p class="text-muted"><?= $st[3] ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>



<!-- CTA BANNER -->
<section style="padding:80px 0;background:linear-gradient(135deg,var(--navy,#0a2540) 0%,var(--teal,#00c9a7) 100%);">
  <div class="container text-center" data-aos="fade-up">
    <h2 style="color:#fff;font-family:'Poppins',sans-serif;font-size:clamp(1.6rem,4vw,2.8rem);font-weight:700;margin-bottom:16px;">
      Your Health Is Our <span style="color:#00c9a7;text-shadow:0 0 20px rgba(0,201,167,.4);">Priority</span>
    </h2>
    <p style="color:rgba(255,255,255,.8);max-width:560px;margin:0 auto 32px;font-size:1.05rem;">
      Don't wait until it's too late. Book an appointment with one of our expert doctors today.
    </p>
    <div class="d-flex flex-wrap gap-3 justify-content-center">
      <a href="book.php" class="read-more-btn btn-primary-hero">Book Appointment <i class="fas fa-arrow-right"></i></a>
      <a href="contact.php" class="read-more-btn btn-outline-hero">Contact Us</a>
    </div>
  </div>
</section>

<!-- TESTIMONIALS (dynamic) -->
<section id="testimonials">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <span class="section-eyebrow">Patient Feedback</span>
      <h2 class="section-heading">What Our <span>Patients Say</span></h2>
    </div>
    <div class="row g-4 justify-content-center">
      <?php $delay=100; $count=0; while($t=$testimonials->fetch_assoc()): $count++; ?>
      <div class="col-12 col-sm-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= $delay ?>">
        <div class="testimonial">
          <?php if($t['photo']): ?>
            <img src="<?= htmlspecialchars($t['photo']) ?>" alt="<?= htmlspecialchars($t['name']) ?>">
          <?php else: ?>
            <div style="width:72px;height:72px;border-radius:50%;background:rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:2rem;color:var(--teal)"><i class="fas fa-user"></i></div>
          <?php endif; ?>
          <div class="text-center">
            <div class="stars">
              <?php for($i=1;$i<=5;$i++) echo $i<=$t['rating']?'<i class="fas fa-star"></i>':'<i class="far fa-star"></i>'; ?>
            </div>
            <p class="testimonial-text">"<?= htmlspecialchars($t['quote']) ?>"</p>
            <h5 class="testimonial-author"><?= htmlspecialchars($t['name']) ?></h5>
          </div>
        </div>
      </div>
      <?php $delay+=100; endwhile; ?>
      <?php if($count===0): ?>
      <!-- Fallback static testimonials -->
      <div class="col-12 col-sm-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
        <div class="testimonial"><img src="assets/img/testimonial1.jpg" alt="Rahul Sharma">
          <div class="text-center"><div class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
          <p class="testimonial-text">"The care I received at Apex Health Care was phenomenal."</p>
          <h5 class="testimonial-author">Rahul Sharma</h5></div></div>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
</body>
</html>
