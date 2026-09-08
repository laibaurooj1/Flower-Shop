<?php
require 'config.php';
require 'includes/functions.php';
require_admin();

$page_title = 'Dashboard';
require 'includes/admin_head.php';

$product_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM `products`"))['c'];
$order_count   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM `orders`"))['c'];
$pending_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM `orders` WHERE payment_status = 'pending'"))['c'];
$user_count    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM `users` WHERE user_type = 'user'"))['c'];
$revenue       = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total_price),0) t FROM `orders` WHERE payment_status = 'completed'"))['t'];
$low_stock     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM `products` WHERE stock <= 5"))['c'];
$unread_msgs   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM `message` WHERE is_read = 0"))['c'];

$recent_orders = mysqli_query($conn, "SELECT * FROM `orders` ORDER BY placed_on DESC LIMIT 5");
?>

<div class="admin-title">
   <h1>Dashboard</h1>
   <p>A quick overview of how the shop is doing.</p>
</div>

<div class="stat-grid">
   <div class="stat-card">
      <div class="icon"><i class="fas fa-seedling"></i></div>
      <h3><?php echo (int) $product_count; ?></h3>
      <p>Products listed</p>
   </div>
   <div class="stat-card">
      <div class="icon"><i class="fas fa-box"></i></div>
      <h3><?php echo (int) $order_count; ?></h3>
      <p>Total orders</p>
   </div>
   <div class="stat-card pending">
      <div class="icon"><i class="fas fa-clock"></i></div>
      <h3><?php echo (int) $pending_count; ?></h3>
      <p>Orders pending</p>
   </div>
   <div class="stat-card">
      <div class="icon"><i class="fas fa-users"></i></div>
      <h3><?php echo (int) $user_count; ?></h3>
      <p>Registered customers</p>
   </div>
   <div class="stat-card">
      <div class="icon"><i class="fas fa-sack-dollar"></i></div>
      <h3>$<?php echo money($revenue); ?></h3>
      <p>Revenue (completed orders)</p>
   </div>
   <div class="stat-card alert">
      <div class="icon"><i class="fas fa-triangle-exclamation"></i></div>
      <h3><?php echo (int) $low_stock; ?></h3>
      <p>Products low on stock</p>
   </div>
</div>

<div class="panel" style="margin-top:30px;">
   <h2>Recent orders</h2>
   <div class="data-list">
      <?php if (mysqli_num_rows($recent_orders) > 0): ?>
         <?php while ($order = mysqli_fetch_assoc($recent_orders)): ?>
            <div class="data-card">
               <div class="head-row">
                  <div class="title-block">
                     <h4>Order #<?php echo (int) $order['id']; ?> -- <?php echo e($order['name']); ?></h4>
                  </div>
                  <span class="badge <?php echo e($order['payment_status']); ?>"><?php echo e(ucfirst($order['payment_status'])); ?></span>
               </div>
               <dl>
                  <dt>Placed</dt><dd><?php echo date('d M Y, g:ia', strtotime($order['placed_on'])); ?></dd>
                  <dt>Total</dt><dd>$<?php echo money($order['total_price']); ?></dd>
               </dl>
            </div>
         <?php endwhile; ?>
      <?php else: ?>
         <div class="empty-state"><i class="fas fa-box-open"></i>No orders yet.</div>
      <?php endif; ?>
   </div>
   <div style="margin-top:18px;"><a href="admin_orders.php" class="option-btn">View all orders</a></div>
</div>

<?php if ($unread_msgs > 0): ?>
<div class="panel">
   <h2>You have <?php echo (int) $unread_msgs; ?> unread message<?php echo $unread_msgs === 1 ? '' : 's'; ?></h2>
   <a href="admin_contacts.php" class="option-btn">Read messages</a>
</div>
<?php endif; ?>

<?php require 'includes/admin_foot.php'; ?>
