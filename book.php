<?php
require 'config.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_ok()) { flash('Invalid request.', 'error'); redirect('rooms.php'); }

$roomId = (int)($_POST['room_id'] ?? 0);
$date = $_POST['move_in'] ?? '';
$d = DateTime::createFromFormat('Y-m-d', $date);
if (!$d || $d->format('Y-m-d') !== $date || $date < date('Y-m-d')) {
    flash('Choose a move-in date that is today or later.', 'error');
    redirect('rooms.php');
}
$uid = (int)$_SESSION['user']['id'];

$db->begin_transaction();
try {
    $s = $db->prepare('SELECT capacity, name, code FROM rooms WHERE id = ? FOR UPDATE');   // lock row to prevent double booking
    $s->bind_param('i', $roomId); $s->execute();
    $room = $s->get_result()->fetch_assoc();
    if (!$room) throw new Exception('That room does not exist.');
    if (!room_has_photos($room['code'])) throw new Exception('That room is not available for booking.');

    $s = $db->prepare("SELECT COUNT(*) c FROM bookings WHERE user_id = ? AND status = 'active'");
    $s->bind_param('i', $uid); $s->execute();
    if ($s->get_result()->fetch_assoc()['c'] > 0) throw new Exception('You already have an active booking. Cancel it to book a different room.');

    $s = $db->prepare("SELECT COUNT(*) c FROM bookings WHERE room_id = ? AND status = 'active'");
    $s->bind_param('i', $roomId); $s->execute();
    if ($s->get_result()->fetch_assoc()['c'] >= $room['capacity']) throw new Exception('That room is now full.');

    $s = $db->prepare('INSERT INTO bookings (user_id, room_id, move_in) VALUES (?,?,?)');
    $s->bind_param('iis', $uid, $roomId, $date); $s->execute();
    $db->commit();
    flash('Booked: ' . $room['name'] . ', moving in on ' . date('j M Y', strtotime($date)) . '.');
    redirect('dashboard.php');
} catch (mysqli_sql_exception $ex) {
    $db->rollback();
    flash('Something went wrong. Try again.', 'error');
} catch (Exception $ex) {
    $db->rollback();
    flash($ex->getMessage(), 'error');
}
redirect('rooms.php');
