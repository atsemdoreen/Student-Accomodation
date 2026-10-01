<?php
require 'config.php';
$title = 'About'; $active = 'about';
include 'includes/header.php';
?>
<section class="wrap page">
  <h1>About <?= SITE_NAME ?></h1>
  <p class="lead">We run student housing for people who would rather spend their time studying than chasing landlords.</p>
  <div class="cols2">
    <div>
      <h2>Our story</h2>
      <p>We started with a single block of twelve rooms and one rule: students should know the full price before they apply. That rule still holds.</p>
      <p>Today we offer single rooms, shared rooms, en-suites and studios, all within walking distance of campus.</p>
    </div>
    <div>
      <h2>House rules, in short</h2>
      <p>Quiet hours run from 10 pm to 7 am. Guests sign in at reception. Cooking is for kitchens only. The full policy is handed out at move-in.</p>
      <h2>Safety</h2>
      <p>Card-access doors, CCTV in shared areas and a 24-hour security desk.</p>
    </div>
  </div>
</section>
<?php include 'includes/footer.php'; ?>
