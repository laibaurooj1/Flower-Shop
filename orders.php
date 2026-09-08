<?php
require 'config.php';
require 'includes/functions.php';
require_login();

$page_title = 'Your orders';
require 'includes/head.php';
require 'header.php';

$user_id = current_user_id();
$stmt = mysqli_prepare($conn, "SELECT * FROM `orders` WHERE user_id = ? ORDER BY placed_on DESC");
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$orders = mysqli_stmt_get_result($stmt);
?>

<section class="page-heading">
   <div class="container">
      <h1>Your orders</h1>
      <p><a href="home.php">Home</a> / Orders</p>
   </div>
</section>

<section class="section">
   <div class="container">

      <?php if (mysqli_num_rows($orders) > 0): ?>
         <div class="orders-list">
            <?php while ($order = mysqli_fetch_assoc($orders)): ?>
               <div class="order-card">
                  <div class="top-row">
                     <div>
                        <strong>Order #<?php echo (int) $order['id']; ?></strong>
                        <div class="date"><?php echo date('d M Y, g:ia', strtotime($order['placed_on'])); ?></div>
                     </div>
                     <span class="badge <?php echo e($order['payment_status']); ?>"><?php echo e(ucfirst($order['payment_status'])); ?></span>
                  </div>
                  <dl>
                     <dt>Recipient</dt><dd><?php echo e($order['name']); ?></dd>
                     <dt>Phone</dt><dd><?php echo e($order['number']); ?></dd>
                     <dt>Email</dt><dd><?php echo e($order['email']); ?></dd>
                     <dt>Address</dt><dd><?php echo e($order['address']); ?></dd>
                     <dt>Payment method</dt><dd><?php echo e($order['method']); ?></dd>
                     <dt>Items</dt><dd><?php echo e($order['total_products']); ?></dd>
                     <dt>Total</dt><dd><strong>$<?php echo money($order['total_price']); ?></strong></dd>
                  </dl>
               </div>
            <?php endwhile; ?>
         </div>
      <?php else: ?>
         <p class="empty"><i class="fas fa-box-open"></i>No orders placed yet.</p>
         <div class="more-btn"><a href="shop.php" class="btn-outline">Start shopping</a></div>
      <?php endif; ?>

   </div>
</section>

<?php require 'footer.php'; require 'includes/foot.php'; ?>
