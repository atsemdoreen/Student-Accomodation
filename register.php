<?php
require 'config.php';
if (logged_in()) redirect('dashboard.php');
$title = 'Create account'; $active = 'login';
$errors = []; $v = ['full_name' => '', 'email' => '', 'phone' => '', 'student_id' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($v as $k => $_) $v[$k] = trim($_POST[$k] ?? '');
    $pw = $_POST['password'] ?? ''; $pw2 = $_POST['confirm'] ?? '';
    if (!csrf_ok()) $errors[] = 'Your session expired. Try again.';
    if ($v['full_name'] === '') $errors[] = 'Enter your full name.';
    if (!filter_var($v['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if (!preg_match('/^[0-9+\s-]{7,20}$/', $v['phone'])) $errors[] = 'Enter a valid phone number.';
    if ($v['student_id'] === '') $errors[] = 'Enter your student ID.';
    if (strlen($pw) < 8) $errors[] = 'Use a password of at least 8 characters.';
    if ($pw !== $pw2) $errors[] = 'The passwords do not match.';
    if (!$errors) {
        try {
            $hash = password_hash($pw, PASSWORD_DEFAULT);
            $s = $db->prepare('INSERT INTO users (full_name,email,phone,student_id,password_hash) VALUES (?,?,?,?,?)');
            $s->bind_param('sssss', $v['full_name'], $v['email'], $v['phone'], $v['student_id'], $hash);
            $s->execute();
            flash('Account created. Log in to book a room.');
            redirect('login.php');
        } catch (mysqli_sql_exception $ex) {
            $errors[] = $ex->getCode() === 1062 ? 'An account with this email already exists.' : 'Could not create the account. Try again.';
        }
    }
}
include 'includes/header.php';
?>
<section class="wrap narrow page">
  <h1>Create account</h1>
  <form method="post" class="card form" novalidate>
    <?php foreach ($errors as $er): ?><p class="flash error"><?= e($er) ?></p><?php endforeach; ?>
    <?= csrf_field() ?>
    <label>Full name<input name="full_name" value="<?= e($v['full_name']) ?>" required></label>
    <label>Email<input type="email" name="email" value="<?= e($v['email']) ?>" required></label>
    <label>Phone<input type="tel" name="phone" value="<?= e($v['phone']) ?>" required></label>
    <label>Student ID<input name="student_id" value="<?= e($v['student_id']) ?>" required></label>
    <label>Password (8+ characters)<input type="password" id="password" name="password" required minlength="8"></label>
    <label>Confirm password<input type="password" id="confirm" name="confirm" required></label>
    <button class="btn" type="submit">Create account</button>
    <p class="muted">Already registered? <a href="login.php">Log in</a></p>
  </form>
</section>
<?php include 'includes/footer.php'; ?>
