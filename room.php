<?php
require 'config.php';
require 'includes/plan.php';
$id = (int)($_GET['id'] ?? 0);
$s = $db->prepare("SELECT r.*, (SELECT COUNT(*) FROM bookings b WHERE b.room_id = r.id AND b.status='active') AS taken FROM rooms r WHERE r.id = ?");
$s->bind_param('i', $id); $s->execute();
$r = $s->get_result()->fetch_assoc();
if (!$r) { flash('That room was not found.', 'error'); redirect('rooms.php'); }
$photos = get_room_photos($r['code']);
if (!$photos) { flash('That room is not available to view yet.', 'error'); redirect('rooms.php'); }
$title = $r['code'] . ' ' . $r['name']; $active = 'rooms';
$left = $r['capacity'] - $r['taken'];
include 'includes/header.php';
?>
<section class="wrap page">
  <p><a href="rooms.php">Back to all rooms</a></p>
  <div class="cols2 room-detail">
    <div>
      <img id="rp-main" class="rp-main" src="<?= e($photos[0]) ?>" alt="<?= e($r['name']) ?>">
    </div>

    <div>
      <h1><?= e($r['name']) ?></h1>
      <p class="price"><?= money($r['price']) ?> <small>/ month, Wi-Fi and utilities included</small></p>
      <p><?= e($r['description']) ?></p>
      <dl class="facts">
        <div><dt>Room</dt><dd><?= e($r['code']) ?>, floor <?= (int)$r['floor'] ?></dd></div>
        <div><dt>Size</dt><dd><?= (int)$r['size_sqm'] ?> m&sup2;</dd></div>
        <div><dt>Beds</dt><dd><?= e($r['bed_type']) ?></dd></div>
        <div><dt>Sleeps</dt><dd><?= (int)$r['capacity'] ?></dd></div>
      </dl>
      <p class="avail <?= $left > 0 ? '' : 'full' ?>"><?= $left > 0 ? "$left of {$r['capacity']} beds free" : 'Full' ?></p>
      <?php if ($left > 0): ?>
        <?php if (logged_in()): ?>
          <form method="post" action="book.php" class="book card">
            <?= csrf_field() ?>
            <input type="hidden" name="room_id" value="<?= (int)$r['id'] ?>">
            <label>Move-in date<input type="date" name="move_in" required></label>
            <button class="btn" type="submit">Book this room</button>
          </form>
        <?php else: ?>
          <a class="btn" href="login.php">Log in to book</a>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>

  <div class="cols2 band">
    <div>
      <h2>How it looks</h2>
      <p><?= e($r['look']) ?></p>
      <h3>What is in the room</h3>
      <ul class="ticks">
        <?php foreach (explode(',', $r['amenities']) as $a): ?><li><?= e(ucfirst(trim($a))) ?></li><?php endforeach; ?>
      </ul>
    </div>
    <div>
      <h2>Layout</h2>
      <div class="card"><?= room_plan($r['type'], $r['capacity']) ?></div>
      <p class="muted">Sketch of the layout, not to scale. Gold is the window side.</p>
    </div>
  </div>
</section>
<?php include 'includes/footer.php'; ?>
