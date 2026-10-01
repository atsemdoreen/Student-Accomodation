<?php
require 'config.php';
$title = 'Home'; $active = 'home';
$rooms = get_listable_rooms($db);
$board = [];
foreach ($rooms as $r) {
    $t = $r['type'];
    $board[$t] ??= ['cap' => 0, 'taken' => 0, 'from' => $r['price']];
    $board[$t]['cap'] += $r['capacity'];
    $board[$t]['taken'] += $r['taken'];
    $board[$t]['from'] = min($board[$t]['from'], $r['price']);
}
$labels = ['single' => 'Single rooms', 'shared' => 'Shared rooms', 'ensuite' => 'En-suite rooms', 'studio' => 'Studios'];
include 'includes/header.php';
?>
<section class="hero wrap">
  <div>
    <h1>Your room for the semester, booked before the queue forms.</h1>
    <p class="lead">Compare rooms, see how many beds are left, and reserve yours online. No paperwork at the front desk.</p>
    <p class="actions">
      <a class="btn" href="rooms.php">Browse rooms</a>
      <?php if (!logged_in()): ?><a class="btn btn-ghost" href="register.php">Create account</a><?php endif; ?>
    </p>
  </div>
  <aside class="board" aria-label="Current availability">
    <h2>Beds left today</h2>
    <?php foreach ($board as $t => $b): $left = $b['cap'] - $b['taken']; $pct = $b['cap'] ? round($b['taken'] / $b['cap'] * 100) : 0; ?>
      <div class="board-row">
        <div><strong><?= e($labels[$t]) ?></strong><small>from <?= money($b['from']) ?> / month</small></div>
        <div class="left"><b><?= $left ?></b> of <?= $b['cap'] ?></div>
        <div class="meter" style="--fill:<?= $pct ?>%"></div>
      </div>
    <?php endforeach; ?>
  </aside>
</section>

<section class="wrap band">
  <h2>What you get</h2>
  <div class="cols3">
    <div><h3>Fixed monthly price</h3><p>Wi-Fi, water and electricity are included in every room price.</p></div>
    <div><h3>Ten minutes to campus</h3><p>Walk to lectures, the library and the sports hall.</p></div>
    <div><h3>Staff on site</h3><p>A warden and a maintenance team are here every day of term.</p></div>
  </div>
</section>

<section class="wrap band">
  <h2>How booking works</h2>
  <ol class="steps">
    <li><strong>Create an account</strong> with your student ID.</li>
    <li><strong>Pick a room</strong> and choose your move-in date.</li>
    <li><strong>Get confirmation</strong> and see it under My booking.</li>
  </ol>
</section>
<?php include 'includes/footer.php'; ?>
