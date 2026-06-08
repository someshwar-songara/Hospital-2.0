<?php
require_once 'auth.php'; require_login();
require_once 'db.php';
$pageTitle = 'Site Settings'; $activeNav = 'settings';

$msg = ''; $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $keys = [
        'hospital_name','hospital_tagline','hospital_address','hospital_phone',
        'hospital_email','hospital_maps_url',
        'social_facebook','social_instagram','social_twitter','social_youtube',
        'hero_badge','hero_title_line1','hero_title_line2','hero_subtitle',
        'stat1_number','stat1_label','stat2_number','stat2_label',
        'stat3_number','stat3_label','stat4_number','stat4_label',
        'footer_copyright','footer_city',
        'hours_weekday','hours_sunday','hours_emergency',
    ];
    $stmt = $conn->prepare("INSERT INTO site_settings (`key`,`value`) VALUES (?,?) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`), updated_at=NOW()");
    foreach ($keys as $k) {
        $v = trim($_POST[$k] ?? '');
        $stmt->bind_param('ss', $k, $v);
        $stmt->execute();
    }
    $msg = 'Settings saved successfully.';
}

// Load all settings into array
$res = $conn->query("SELECT `key`,`value` FROM site_settings");
$s = [];
while ($row = $res->fetch_assoc()) $s[$row['key']] = $row['value'];
$g = fn($k,$d='') => htmlspecialchars($s[$k] ?? $d);

include 'includes/header.php';
?>

<?php if($msg):?><div class="alert-success"><i class="fas fa-check-circle"></i> <?= $msg ?></div><?php endif;?>

<form method="POST">
<div class="settings-grid">

  <!-- Hospital Info -->
  <div class="admin-card">
    <div class="admin-card-header"><h3><i class="fas fa-hospital"></i> Hospital Information</h3></div>
    <div class="admin-form">
      <div class="field-group-admin"><label>Hospital Name</label>
        <input type="text" name="hospital_name" value="<?= $g('hospital_name','Apex Health Care') ?>"></div>
      <div class="field-group-admin"><label>Tagline <small>(shown in footer)</small></label>
        <input type="text" name="hospital_tagline" value="<?= $g('hospital_tagline','Expert healthcare in Ujjain.') ?>"></div>
      <div class="field-group-admin"><label>Address</label>
        <textarea name="hospital_address" rows="2"><?= $g('hospital_address') ?></textarea></div>
      <div class="field-group-admin"><label>Phone</label>
        <input type="text" name="hospital_phone" value="<?= $g('hospital_phone') ?>"></div>
      <div class="field-group-admin"><label>Email</label>
        <input type="email" name="hospital_email" value="<?= $g('hospital_email') ?>"></div>
      <div class="field-group-admin"><label>Google Maps URL</label>
        <input type="text" name="hospital_maps_url" value="<?= $g('hospital_maps_url') ?>"></div>
    </div>
  </div>

  <!-- Working Hours -->
  <div class="admin-card">
    <div class="admin-card-header"><h3><i class="fas fa-clock"></i> Working Hours</h3></div>
    <div class="admin-form">
      <div class="field-group-admin"><label>Weekdays (Mon–Sat)</label>
        <input type="text" name="hours_weekday" value="<?= $g('hours_weekday','Monday – Saturday: 8:00 AM – 9:00 PM') ?>"></div>
      <div class="field-group-admin"><label>Sunday</label>
        <input type="text" name="hours_sunday" value="<?= $g('hours_sunday','Sunday: 9:00 AM – 2:00 PM') ?>"></div>
      <div class="field-group-admin"><label>Emergency Notice</label>
        <input type="text" name="hours_emergency" value="<?= $g('hours_emergency','24/7 Emergency Available') ?>"></div>
    </div>
  </div>

  <!-- Social Media -->
  <div class="admin-card">
    <div class="admin-card-header"><h3><i class="fas fa-share-alt"></i> Social Media Links</h3></div>
    <div class="admin-form">
      <div class="field-group-admin"><label><i class="fab fa-facebook-f me-1" style="color:#1877f2"></i> Facebook URL</label>
        <input type="text" name="social_facebook" value="<?= $g('social_facebook','#') ?>"></div>
      <div class="field-group-admin"><label><i class="fab fa-instagram me-1" style="color:#e1306c"></i> Instagram URL</label>
        <input type="text" name="social_instagram" value="<?= $g('social_instagram','#') ?>"></div>
      <div class="field-group-admin"><label><i class="fab fa-twitter me-1" style="color:#1da1f2"></i> Twitter / X URL</label>
        <input type="text" name="social_twitter" value="<?= $g('social_twitter','#') ?>"></div>
      <div class="field-group-admin"><label><i class="fab fa-youtube me-1" style="color:#ff0000"></i> YouTube URL</label>
        <input type="text" name="social_youtube" value="<?= $g('social_youtube','#') ?>"></div>
    </div>
  </div>

  <!-- Hero Section -->
  <div class="admin-card">
    <div class="admin-card-header"><h3><i class="fas fa-image"></i> Homepage Hero Section</h3></div>
    <div class="admin-form">
      <div class="field-group-admin"><label>Badge Text <small>(top tag)</small></label>
        <input type="text" name="hero_badge" value="<?= $g('hero_badge','24/7 Emergency Service') ?>"></div>
      <div class="field-group-admin"><label>Title Line 1</label>
        <input type="text" name="hero_title_line1" value="<?= $g('hero_title_line1','Advanced Care.') ?>"></div>
      <div class="field-group-admin"><label>Title Line 2 <small>(teal coloured)</small></label>
        <input type="text" name="hero_title_line2" value="<?= $g('hero_title_line2','Exceptional Health.') ?>"></div>
      <div class="field-group-admin"><label>Subtitle paragraph</label>
        <textarea name="hero_subtitle" rows="3"><?= $g('hero_subtitle') ?></textarea></div>
    </div>
  </div>

  <!-- Stats -->
  <div class="admin-card">
    <div class="admin-card-header"><h3><i class="fas fa-chart-bar"></i> Homepage Statistics</h3></div>
    <div class="admin-form">
      <?php for($i=1;$i<=4;$i++): ?>
      <div class="stats-row">
        <div class="field-group-admin" style="flex:0 0 110px">
          <label>Stat <?= $i ?> Number</label>
          <input type="text" name="stat<?= $i ?>_number" value="<?= $g("stat{$i}_number") ?>">
        </div>
        <div class="field-group-admin" style="flex:1">
          <label>Stat <?= $i ?> Label</label>
          <input type="text" name="stat<?= $i ?>_label" value="<?= $g("stat{$i}_label") ?>">
        </div>
      </div>
      <?php endfor; ?>
    </div>
  </div>

  <!-- Footer -->
  <div class="admin-card">
    <div class="admin-card-header"><h3><i class="fas fa-grip-horizontal"></i> Footer Text</h3></div>
    <div class="admin-form">
      <div class="field-group-admin"><label>Copyright Text</label>
        <input type="text" name="footer_copyright" value="<?= $g('footer_copyright','© 2025 Apex Health Care. All rights reserved.') ?>"></div>
      <div class="field-group-admin"><label>City / Location</label>
        <input type="text" name="footer_city" value="<?= $g('footer_city','Ujjain, Madhya Pradesh') ?>"></div>
    </div>
  </div>

</div><!-- /.settings-grid -->

<div class="settings-save-bar">
  <button type="submit" class="btn-admin-primary"><i class="fas fa-save"></i> Save All Settings</button>
  <span class="save-note">Changes apply to the live website immediately.</span>
</div>
</form>

<?php include 'includes/footer.php'; ?>
