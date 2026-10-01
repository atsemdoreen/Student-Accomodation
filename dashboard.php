<?php
require 'config.php';
require_login();
$title = 'My booking'; $active = 'dash';
$uid = (int)$_SESSION['user']['id'];
$s = $db->prepare('SELECT b.id, b.move_in, b.status, b.created_at, r.code, r.name, r.price, r.floor
                   FROM bookings b JOIN rooms r ON r.id = b.room_id WHERE b.user_id = ? ORDER BY b.created_at DESC');
$s->bind_param('i', $uid); $s->execute();
$rows = $s->get_result()->fetch_all(MYSQLI_ASSOC);
include 'includes/header.php';
?>
<section class="wrap page">
  <h1>My booking</h1>
  <p class="lead">Signed in as <?= e($_SESSION['user']['name']) ?>.</p>
  <?php if (!$rows): ?>
    <div class="card"><p>You have not booked a room yet.</p><a class="btn" href="rooms.php">Browse rooms</a></div>
  <?php else: ?>
    <div class="table-wrap"><table>
      <thead><tr><th>Room</th><th>Floor</th><th>Price / month</th><th>Move-in</th><th>Status</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($rows as $b): ?>
        <tr>
          <td><strong><?= e($b['code']) ?></strong> <?= e($b['name']) ?></td>
          <td><?= (int)$b['floor'] ?></td>
          <td><?= money($b['price']) ?></td>
          <td><?= date('j M Y', strtotime($b['move_in'])) ?></td>
          <td><span class="tag <?= e($b['status']) ?>"><?= e(ucfirst($b['status'])) ?></span></td>
          <td><?php if ($b['status'] === 'active'): ?>
            <form method="post" action="cancel.php" data-confirm="Cancel this booking?">
              <?= csrf_field() ?><input type="hidden" name="booking_id" value="<?= (int)$b['id'] ?>">
              <button class="btn btn-ghost" type="submit">Cancel</button>
            </form><?php endif; ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
  <?php endif; ?>
</section>
<?php include 'includes/footer.php'; ?>
