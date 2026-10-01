<?php
require 'config.php';
$title = 'Rooms'; $active = 'rooms';
$rooms = get_listable_rooms($db);
include 'includes/header.php';
?>
<section class="wrap page">
  <h1>Rooms</h1>
  <p class="lead">Prices are per month and include Wi-Fi and utilities.</p>
  <div class="filters" role="group" aria-label="Filter by type">
    <button class="chip on" data-filter="all">All</button>
    <button class="chip" data-filter="single">Single</button>
    <button class="chip" data-filter="shared">Shared</button>
    <button class="chip" data-filter="ensuite">En-suite</button>
    <button class="chip" data-filter="studio">Studio</button>
  </div>
  <?php if (!$rooms): ?>
  <p class="muted">No rooms with photos are listed yet. Add images under <code>assets/img/rooms/</code> and link them in <code>config.php</code>.</p>
  <?php endif; ?>
  <div class="room-grid">
  <?php foreach ($rooms as $r): $left = $r['capacity'] - $r['taken']; $ph = get_room_photos($r['code']); ?>
    <article class="room card" data-type="<?= e($r['type']) ?>">
      <a class="room-photo" href="room.php?id=<?= (int)$r['id'] ?>"><img src="<?= e($ph[0]) ?>" alt="<?= e($r['name']) ?>, room <?= e($r['code']) ?>" loading="lazy"><span class="code-chip"><?= e($r['code']) ?></span></a>
      <h3><?= e($r['name']) ?></h3>
      <p class="price"><?= money($r['price']) ?> <small>/ month</small></p>
      <p><?= e($r['description']) ?></p>
      <p class="muted"><?= (int)$r['size_sqm'] ?> m&sup2;, <?= e($r['bed_type']) ?>. <?= e($r['amenities']) ?></p>
      <p><a href="room.php?id=<?= (int)$r['id'] ?>">See photos and layout</a></p>
      <p class="avail <?= $left > 0 ? '' : 'full' ?>"><?= $left > 0 ? "$left of {$r['capacity']} beds free" : 'Full' ?></p>
      <?php if ($left > 0): ?>
        <?php if (logged_in()): ?>
          <form method="post" action="book.php" class="book">
            <?= csrf_field() ?>
            <input type="hidden" name="room_id" value="<?= (int)$r['id'] ?>">
            <label>Move-in date<input type="date" name="move_in" required></label>
            <button class="btn" type="submit">Book this room</button>
          </form>
        <?php else: ?>
          <a class="btn" href="login.php">Log in to book</a>
        <?php endif; ?>
      <?php endif; ?>
    </article>
  <?php endforeach; ?>
  </div>
</section>
<?php include 'includes/footer.php'; ?>
