<?php
require 'config.php';
require 'includes/functions.php';

// already signed in? send them where they belong
if (current_user_id()) redirect('home.php');
if (current_admin_id()) redirect('admin_page.php');

if (isset($_POST['submit'])) {
    csrf_check();

    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['pass'] ?? '';

    $stmt = mysqli_prepare($conn, "SELECT * FROM `users` WHERE email = ?");
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if ($user && password_verify($pass, $user['password'])) {
        session_regenerate_id(true);

        if ($user['user_type'] === 'admin') {
            $_SESSION['admin_name']  = $user['name'];
            $_SESSION['admin_email'] = $user['email'];
            $_SESSION['admin_id']    = $user['id'];
            redirect('admin_page.php');
        } else {
            $_SESSION['user_name']  = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_id']    = $user['id'];
            redirect('home.php');
        }
    } else {
        flash('Incorrect email or password.', 'error');
        redirect('login.php');
    }
}

$page_title = 'Log in';
require 'includes/head.php';
?>

<section class="auth-page">
   <div class="auth-card">
      <a href="home.php" class="logo">Bloom<span>&amp;</span>Petal</a>
      <h1>Welcome back</h1>
      <form action="" method="POST">
         <?php echo csrf_field(); ?>
         <div class="field">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="you@example.com" required autofocus>
         </div>
         <div class="field">
            <label for="pass">Password</label>
            <input type="password" id="pass" name="pass" placeholder="Enter your password" required>
         </div>
         <button type="submit" name="submit" class="btn btn-block">Log in</button>
      </form>
      <p class="switch">Don't have an account? <a href="register.php">Register now</a></p>
   </div>
</section>

<?php require 'includes/foot.php'; ?>
