<nav class="navbar navbar-expand-lg fixed-top" id="mainNav">
  <div class="container">
    <a class="navbar-brand" href="index.php"><img src="assets/img/logo.png" alt="Apex Health Care Logo"></a>

    <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse">
      <ul class="navbar-nav mx-auto">
        <li class="nav-item"><a class="nav-link <?php echo ($page == 'home') ? 'active' : ''; ?>" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link <?php echo ($page == 'about') ? 'active' : ''; ?>" href="about.php">About</a></li>
        <li class="nav-item"><a class="nav-link <?php echo ($page == 'facilities') ? 'active' : ''; ?>" href="facilities.php">Facilities</a></li>
        <li class="nav-item"><a class="nav-link <?php echo ($page == 'doctors') ? 'active' : ''; ?>" href="doctors.php">Doctors</a></li>
        <li class="nav-item"><a class="nav-link <?php echo ($page == 'contact') ? 'active' : ''; ?>" href="contact.php">Contact</a></li>
      </ul>
      <a href="book.php" class="login-button">
        <i class="fas fa-calendar-check"></i> Book Appointment
      </a>
    </div>
  </div>
</nav>

<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="offcanvasNavbarLabel">
      <a class="navbar-brand" href="index.php"><img src="assets/img/logo.png" alt="Apex Health Care Logo"></a>
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <ul class="navbar-nav justify-content-center flex-grow-1 pe-3">
      <li class="nav-item"><a class="nav-link <?php echo ($page == 'home') ? 'active' : ''; ?>" href="index.php">Home</a></li>
      <li class="nav-item"><a class="nav-link <?php echo ($page == 'about') ? 'active' : ''; ?>" href="about.php">About</a></li>
      <li class="nav-item"><a class="nav-link <?php echo ($page == 'facilities') ? 'active' : ''; ?>" href="facilities.php">Facilities</a></li>
      <li class="nav-item"><a class="nav-link <?php echo ($page == 'doctors') ? 'active' : ''; ?>" href="doctors.php">Doctors</a></li>
      <li class="nav-item"><a class="nav-link <?php echo ($page == 'contact') ? 'active' : ''; ?>" href="contact.php">Contact</a></li>
    </ul>
    <div class="d-lg-none mt-4">
      <a href="book.php" class="login-button w-100 justify-content-center">
        <i class="fas fa-calendar-check"></i> Book Appointment
      </a>
    </div>
  </div>
</div>
