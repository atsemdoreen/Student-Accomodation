<?php
require 'config.php';
$title = 'Contact'; $active = 'contact';
$errors = []; $v = ['name' => $_SESSION['user']['name'] ?? '', 'email' => '', 'subject' => '', 'body' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($v as $k => $_) $v[$k] = trim($_POST[$k] ?? '');
    if (!csrf_ok()) $errors[] = 'Your session expired. Try again.';
    if ($v['name'] === '') $errors[] = 'Enter your name.';
    if (!filter_var($v['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if ($v['subject'] === '') $errors[] = 'Enter a subject.';
    if (strlen($v['body']) < 10) $errors[] = 'Write a message of at least 10 characters.';
    if (!$errors) {
        $s = $db->prepare('INSERT INTO messages (name,email,subject,body) VALUES (?,?,?,?)');
        $s->bind_param('ssss', $v['name'], $v['email'], $v['subject'], $v['body']);
        $s->execute();
        flash('Message sent. We reply within two working days.');
        redirect('contact.php');
    }
}
include 'includes/header.php';
?>
<section class="wrap page cols2">
  <div>
    <h1>Contact us</h1>
    <p class="lead">Questions about a room, a payment or a move-in date? Write to us.</p>
    <p><strong>Email</strong><br><?= e(CONTACT_EMAIL) ?></p>
    <p><strong>Reception hours</strong><br>Monday to Friday, 8 am to 6 pm</p>
    <p><strong>Emergencies</strong><br>The 24-hour security desk is in the main lobby.</p>
  </div>
  <form method="post" class="card form" novalidate>
    <?php foreach ($errors as $er): ?><p class="flash error"><?= e($er) ?></p><?php endforeach; ?>
    <?= csrf_field() ?>
    <label>Name<input name="name" value="<?= e($v['name']) ?>" required></label>
    <label>Email<input type="email" name="email" value="<?= e($v['email']) ?>" required></label>
    <label>Subject<input name="subject" value="<?= e($v['subject']) ?>" required></label>
    <label>Message<textarea name="body" rows="5" required><?= e($v['body']) ?></textarea></label>
    <button class="btn" type="submit">Send message</button>
  </form>
</section>
<?php include 'includes/footer.php'; ?>
