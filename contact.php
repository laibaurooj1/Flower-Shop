<?php
require 'config.php';
require 'includes/functions.php';
require_login();

$user_id = current_user_id();

if (isset($_POST['send'])) {
    csrf_check();
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $number  = trim($_POST['number'] ?? '');
    $msg     = trim($_POST['message'] ?? '');

    if ($name === '' || $msg === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        flash('Please fill in your name, a valid email, and a message.', 'error');
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO `message`(user_id, name, email, number, message) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'issss', $user_id, $name, $email, $number, $msg);
        mysqli_stmt_execute($stmt);
        flash('Message sent! We usually reply within a few hours.');
    }
    redirect('contact.php');
}

$page_title = 'Contact';
require 'includes/head.php';
require 'header.php';
?>

<section class="page-heading">
   <div class="container">
      <h1>Contact us</h1>
      <p><a href="home.php">Home</a> / Contact</p>
   </div>
</section>

<section class="section">
   <div class="container">
      <div class="contact-layout">

         <div>
            <div class="section-head">
               <p class="eyebrow">Say hello</p>
               <h2>Send us a message</h2>
            </div>
            <form action="" method="POST">
               <?php echo csrf_field(); ?>
               <div class="field">
                  <label for="c_name">Your name</label>
                  <input type="text" id="c_name" name="name" placeholder="Jane Doe" required>
               </div>
               <div class="field">
                  <label for="c_email">Email</label>
                  <input type="email" id="c_email" name="email" placeholder="you@example.com" required>
               </div>
               <div class="field">
                  <label for="c_number">Phone number</label>
                  <input type="tel" id="c_number" name="number" placeholder="e.g. 9876543210" required>
               </div>
               <div class="field">
                  <label for="c_message">Message</label>
                  <textarea id="c_message" name="message" placeholder="How can we help?" required></textarea>
               </div>
               <button type="submit" name="send" class="btn">Send message</button>
            </form>
         </div>

         <div>
            <div class="section-head">
               <p class="eyebrow">Find us</p>
               <h2>Get in touch</h2>
            </div>
            <div class="contact-info-list">
               <div class="item">
                  <i class="fas fa-phone"></i>
                  <div>
                     <h4>Phone</h4>
                     <p>+1 234 567 8900 &middot; +1 111 222 3333</p>
                  </div>
               </div>
               <div class="item">
                  <i class="fas fa-envelope"></i>
                  <div>
                     <h4>Email</h4>
                     <p>hello@bloomandpetal.test</p>
                  </div>
               </div>
               <div class="item">
                  <i class="fas fa-location-dot"></i>
                  <div>
                     <h4>Studio</h4>
                     <p>221 Garden Lane, Mumbai 400001</p>
                  </div>
               </div>
               <div class="item">
                  <i class="fas fa-clock"></i>
                  <div>
                     <h4>Hours</h4>
                     <p>Mon-Sat, 9am-6pm. Orders before 2pm ship same day.</p>
                  </div>
               </div>
            </div>
         </div>

      </div>
   </div>
</section>

<?php require 'footer.php'; require 'includes/foot.php'; ?>
