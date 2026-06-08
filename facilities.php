<?php
$page = 'facilities';
require_once 'includes/settings.php';
$facilities = $conn->query("SELECT * FROM facilities WHERE is_active=1 ORDER BY sort_order ASC, id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Our Facilities – <?= htmlspecialchars(cfg('hospital_name','Apex Health Care')) ?></title>
  <link rel="shortcut icon" href="assets/img/55.ico" type="image/x-icon">
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
  <link rel="stylesheet" href="assets/css/main.css">
  <link rel="stylesheet" href="assets/css/facilities.css">
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="page-banner">
  <div class="page-banner-bg"></div><div class="page-banner-overlay"></div>
  <div class="container page-banner-content">
    <h1>Our <span>Facilities</span></h1>
    <div class="breadcrumb-wrap"><a href="index.php">Home</a><span class="sep">›</span><span class="current">Facilities</span></div>
  </div>
</div>

<section class="facilities-page-section">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <span class="section-eyebrow">Comprehensive Care</span>
      <h2 class="section-heading">Medical <span>Services & Facilities</span></h2>
    </div>
    <div class="filter-controls d-flex flex-wrap justify-content-center gap-2 mb-5" data-aos="fade-up" data-aos-delay="100">
      <button class="filter-btn active" data-filter="all">All</button>
      <button class="filter-btn" data-filter="medical">Medical</button>
      <button class="filter-btn" data-filter="surgical">Surgical</button>
    </div>
    <div class="row g-4" id="facilities-grid">
      <?php if($facilities->num_rows > 0): ?>
        <?php $d=0; while($f=$facilities->fetch_assoc()): $d=($d%3)*100; ?>
        <div class="col-lg-4 col-md-6 col-sm-6 facility-item <?= htmlspecialchars($f['category']) ?>" data-aos="fade-up" data-aos-delay="<?= $d ?>">
          <div class="facility-card glass-card hover-lift overflow-hidden h-100">
            <img src="<?= htmlspecialchars($f['image']) ?>" alt="<?= htmlspecialchars($f['name']) ?>" class="facility-card-img">
            <div class="p-4">
              <h4 class="text-secondary-theme fw-bold"><?= htmlspecialchars($f['name']) ?></h4>
              <p class="text-muted mb-0"><?= htmlspecialchars($f['description']) ?></p>
            </div>
          </div>
        </div>
        <?php $d++; endwhile; ?>
      <?php else: ?>
        <div class="col-12"><p class="text-center text-muted py-5">No facilities listed yet.</p></div>
      <?php endif; ?>
    </div>
  </div>
</section>

<script>
document.addEventListener("DOMContentLoaded",function(){
  const btns=document.querySelectorAll('.filter-btn'), items=document.querySelectorAll('.facility-item');
  btns.forEach(b=>{ b.addEventListener('click',()=>{
    btns.forEach(x=>x.classList.remove('active')); b.classList.add('active');
    const f=b.getAttribute('data-filter');
    items.forEach(i=>{ i.style.display=(f==='all'||i.classList.contains(f))?'block':'none'; });
  });});
});
</script>

<?php include 'includes/footer.php'; ?>
</body>
</html>
