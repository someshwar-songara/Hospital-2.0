<?php
// Load settings if not already loaded
if (!function_exists('cfg')) { require_once __DIR__ . '/settings.php'; }
?>
<footer class="site-footer">
  <div class="container footer-content">
    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4 g-lg-5">

      <div class="col footer-column footer-brand-col">
        <img src="assets/img/logo.png" alt="<?= htmlspecialchars(cfg('hospital_name','Apex Health Care')) ?>" class="footer-logo">
        <p class="footer-tagline"><?= htmlspecialchars(cfg('hospital_tagline','Expert healthcare in Ujjain.')) ?></p>
        <a href="book.php" class="login-button footer-cta">
          <i class="fa-regular fa-calendar-check"></i> Book Appointment
        </a>
      </div>

      <div class="col footer-column footer-quick-col">
        <h5>Quick Links</h5>
        <nav class="footer-links footer-quick-links" aria-label="Footer navigation">
          <a href="index.php"><i class="fas fa-chevron-right"></i><span>Home</span></a>
          <a href="about.php"><i class="fas fa-chevron-right"></i><span>About Us</span></a>
          <a href="facilities.php"><i class="fas fa-chevron-right"></i><span>Facilities</span></a>
          <a href="doctors.php"><i class="fas fa-chevron-right"></i><span>Doctors</span></a>
          <a href="contact.php"><i class="fas fa-chevron-right"></i><span>Contact</span></a>
        </nav>
      </div>

      <div class="col footer-column footer-address-col">
        <h5>Contact Info</h5>
        <div class="footer-links footer-contact-links">
          <a href="<?= htmlspecialchars(cfg('hospital_maps_url','#')) ?>" target="_blank" rel="noopener noreferrer" class="footer-contact-item footer-contact-address">
            <i class="fas fa-map-marker-alt"></i>
            <span><?= htmlspecialchars(cfg('hospital_address','Apex Health Care, Freeganj, Ujjain, MP 456001')) ?></span>
          </a>
          <div class="footer-contact-row">
            <a href="tel:<?= htmlspecialchars(cfg('hospital_phone','+919111000013')) ?>" class="footer-contact-item">
              <i class="fas fa-phone"></i>
              <span><?= htmlspecialchars(cfg('hospital_phone','+91 9111XXXX13')) ?></span>
            </a>
            <a href="mailto:<?= htmlspecialchars(cfg('hospital_email','apex.healthcare@gmail.com')) ?>" class="footer-contact-item footer-contact-email">
              <i class="fas fa-envelope"></i>
              <span><?= htmlspecialchars(cfg('hospital_email','apex.healthcare@gmail.com')) ?></span>
            </a>
          </div>
          <?php if(cfg('hours_weekday')): ?>
          <div class="footer-contact-item" style="margin-top:6px">
            <i class="fas fa-clock"></i>
            <span>
              <?= htmlspecialchars(cfg('hours_weekday')) ?><br>
              <?= htmlspecialchars(cfg('hours_sunday')) ?>
            </span>
          </div>
          <?php endif; ?>
        </div>
      </div>

      <div class="col footer-column footer-social-col">
        <h5>Follow Us</h5>
        <div class="footer-social-inner">
          <p class="footer-note">Stay connected for health tips and updates.</p>
          <div class="social-icons footer-social-icons">
            <?php if(cfg('social_facebook','#') !== ''): ?>
              <a href="<?= htmlspecialchars(cfg('social_facebook','#')) ?>" aria-label="Facebook" <?= cfg('social_facebook','#')!=='#'?'target="_blank" rel="noopener"':'' ?>><i class="fab fa-facebook-f"></i></a>
            <?php endif; ?>
            <?php if(cfg('social_instagram','#') !== ''): ?>
              <a href="<?= htmlspecialchars(cfg('social_instagram','#')) ?>" aria-label="Instagram" <?= cfg('social_instagram','#')!=='#'?'target="_blank" rel="noopener"':'' ?>><i class="fab fa-instagram"></i></a>
            <?php endif; ?>
            <?php if(cfg('social_twitter') && cfg('social_twitter') !== '#'): ?>
              <a href="<?= htmlspecialchars(cfg('social_twitter')) ?>" aria-label="Twitter" target="_blank" rel="noopener"><i class="fab fa-twitter"></i></a>
            <?php endif; ?>
            <?php if(cfg('social_youtube') && cfg('social_youtube') !== '#'): ?>
              <a href="<?= htmlspecialchars(cfg('social_youtube')) ?>" aria-label="YouTube" target="_blank" rel="noopener"><i class="fab fa-youtube"></i></a>
            <?php endif; ?>
          </div>
        </div>
      </div>

    </div>
  </div>
  <div class="footer-bottom">
    <div class="container">
      <p class="footer-copyright">
        <span><?= htmlspecialchars(cfg('footer_copyright','© 2025 Apex Health Care. All rights reserved.')) ?></span>
        <span class="footer-copyright-sep" aria-hidden="true">·</span>
        <span><?= htmlspecialchars(cfg('footer_city','Ujjain, Madhya Pradesh')) ?></span>
      </p>
    </div>
  </div>
</footer>

<script>
  const nav = document.getElementById('mainNav');
  if(nav){ window.addEventListener('scroll',()=>{ nav.classList.toggle('scrolled',window.scrollY>60); }); }
  if(typeof AOS!=='undefined'){ AOS.init({duration:700,once:true,easing:'ease-out-cubic'}); }
  const offcanvasEl = document.getElementById('offcanvasNavbar');
  if(offcanvasEl){ offcanvasEl.querySelectorAll('.nav-link').forEach(link=>{ link.addEventListener('click',()=>{ const i=bootstrap.Offcanvas.getInstance(offcanvasEl); if(i)i.hide(); }); }); }
</script>
