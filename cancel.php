<?php
require 'config.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_ok()) { flash('Invalid request.', 'error'); redirect('dashboard.php'); }
$id = (int)($_POST['booking_id'] ?? 0); $uid = (int)$_SESSION['user']['id'];
$s = $db->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ? AND user_id = ? AND status = 'active'");
$s->bind_param('ii', $id, $uid); $s->execute();
flash($s->affected_rows ? 'Booking cancelled.' : 'No active booking found.', $s->affected_rows ? 'ok' : 'error');
redirect('dashboard.php');
