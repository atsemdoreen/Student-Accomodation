<?php
require 'config.php';
if (logged_in()) redirect('dashboard.php');
$title = 'Log in'; $active = 'login';
$error = ''; $email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? ''); $pw = $_POST['password'] ?? '';
    if (!csrf_ok()) {
        $error = 'Your session expired. Try again.';
    } else {
        $s = $db->prepare('SELECT id, full_name, password_hash FROM users WHERE email = ?');
        $s->bind_param('s', $email);
        $s->execute();
        $u = $s->get_result()->fetch_assoc();
        if ($u && password_verify($pw, $u['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user'] = ['id' => $u['id'], 'name' => $u['full_name']];
            flash('Welcome back, ' . explode(' ', $u['full_name'])[0] . '.');
            redirect('dashboard.php');
        }
        $error = 'Email or password is incorrect.';
    }
}
include 'includes/header.php';
?>
<section class="wrap narrow page">
  <h1>Log in</h1>
  <form method="post" class="card form">
    <?php if ($error): ?><p class="flash error"><?= e($error) ?></p><?php endif; ?>
    <?= csrf_field() ?>
    <label>Email<input type="email" name="email" value="<?= e($email) ?>" required autofocus></label>
    <label>Password<input type="password" name="password" required></label>
    <button class="btn" type="submit">Log in</button>
    <p class="muted">New here? <a href="register.php">Create an account</a></p>
  </form>
</section>
<?php include 'includes/footer.php'; ?>
