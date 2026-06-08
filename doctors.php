<?php
$page = 'doctors';
require_once 'includes/settings.php';
$doctors = $conn->query("SELECT * FROM doctors WHERE is_active=1 ORDER BY sort_order ASC, name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Our Doctors – <?= htmlspecialchars(cfg('hospital_name','Apex Health Care')) ?></title>
  <link rel="shortcut icon" href="assets/img/55.ico" type="image/x-icon">
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
  <link rel="stylesheet" href="assets/css/main.css">
  <link rel="stylesheet" href="assets/css/doctors.css">
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="page-banner">
  <div class="page-banner-bg"></div><div class="page-banner-overlay"></div>
  <div class="container page-banner-content">
    <h1>Our <span>Doctors</span></h1>
    <div class="breadcrumb-wrap"><a href="index.php">Home</a><span class="sep">›</span><span class="current">Doctors</span></div>
  </div>
</div>

<section class="doctors-page-section">
  <div class="container">
    <div class="doctors-page-header text-center" data-aos="fade-up">
      <span class="section-eyebrow">Expert Team</span>
      <h2 class="section-heading">Meet Our <span>Specialists</span></h2>
      <p class="section-subtext">Highly qualified doctors dedicated to providing compassionate, expert care across every specialty.</p>
    </div>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
      <?php $delay=0; while($doc=$doctors->fetch_assoc()): ?>
      <div class="col" data-aos="fade-up" data-aos-delay="<?= $delay ?>">
        <article class="doctor-card glass-card hover-lift h-100">
          <div class="doctor-card-image">
            <img src="<?= htmlspecialchars($doc['photo']) ?>" alt="<?= htmlspecialchars($doc['name']) ?>" class="doctor-card-img" loading="lazy">
          </div>
          <div class="doctor-card-body">
            <h3 class="doctor-card-name"><?= htmlspecialchars($doc['name']) ?></h3>
            <p class="doctor-card-specialty"><?= htmlspecialchars($doc['specialty']) ?></p>
            <?php if($doc['profile_url']): ?>
              <a href="<?= htmlspecialchars($doc['profile_url']) ?>" class="btn btn-outline-teal rounded-pill doctor-card-btn">View Profile</a>
            <?php endif; ?>
          </div>
        </article>
      </div>
      <?php $delay=($delay+100)%400; endwhile; ?>
      <?php if($doctors->num_rows===0): ?>
        <div class="col-12"><p class="text-center text-muted py-5">No doctors listed yet.</p></div>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
</body>
</html>
