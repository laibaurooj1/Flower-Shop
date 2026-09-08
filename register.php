<?php
require 'config.php';
require 'includes/functions.php';

if (current_user_id()) redirect('home.php');
if (current_admin_id()) redirect('admin_page.php');

if (isset($_POST['submit'])) {
    csrf_check();

    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['pass'] ?? '';
    $cpass = $_POST['cpass'] ?? '';

    $errors = [];
    if ($name === '' || $email === '' || $pass === '') {
        $errors[] = 'Please fill in every field.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if (strlen($pass) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }
    if ($pass !== $cpass) {
        $errors[] = 'Passwords do not match.';
    }

    if (!$errors) {
        $stmt = mysqli_prepare($conn, "SELECT id FROM `users` WHERE email = ?");
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        if (mysqli_stmt_get_result($stmt)->num_rows > 0) {
            $errors[] = 'An account with that email already exists.';
        }
    }

    if ($errors) {
        foreach ($errors as $err) flash($err, 'error');
        redirect('register.php');
    }

    $hash = password_hash($pass, PASSWORD_BCRYPT);
    $ins = mysqli_prepare($conn, "INSERT INTO `users`(name, email, password) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($ins, 'sss', $name, $email, $hash);
    mysqli_stmt_execute($ins);

    flash('Account created! You can log in now.');
    redirect('login.php');
}

$page_title = 'Register';
require 'includes/head.php';
?>

<section class="auth-page">
   <div class="auth-card">
      <a href="home.php" class="logo">Bloom<span>&amp;</span>Petal</a>
      <h1>Create your account</h1>
      <form action="" method="POST">
         <?php echo csrf_field(); ?>
         <div class="field">
            <label for="name">Full name</label>
            <input type="text" id="name" name="name" placeholder="Your name" required autofocus>
         </div>
         <div class="field">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="you@example.com" required>
         </div>
         <div class="field">
            <label for="pass">Password</label>
            <input type="password" id="pass" name="pass" placeholder="At least 8 characters" required minlength="8">
         </div>
         <div class="field">
            <label for="cpass">Confirm password</label>
            <input type="password" id="cpass" name="cpass" placeholder="Repeat your password" required minlength="8">
         </div>
         <button type="submit" name="submit" class="btn btn-block">Register</button>
      </form>
      <p class="switch">Already have an account? <a href="login.php">Log in now</a></p>
   </div>
</section>

<?php require 'includes/foot.php'; ?>
