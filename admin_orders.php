<?php
require 'config.php';
require 'includes/functions.php';
require_admin();

if (isset($_POST['update_status'])) {
    csrf_check();
    $order_id = (int) ($_POST['order_id'] ?? 0);
    $status = $_POST['payment_status'] ?? 'pending';
    if (!in_array($status, ['pending', 'completed'], true)) $status = 'pending';

    $stmt = mysqli_prepare($conn, "UPDATE `orders` SET payment_status = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'si', $status, $order_id);
    mysqli_stmt_execute($stmt);

    flash('Order #' . $order_id . ' marked as ' . $status . '.');
    redirect('admin_orders.php');
}

if (isset($_GET['delete'])) {
    $delete_id = (int) $_GET['delete'];
    $stmt = mysqli_prepare($conn, "DELETE FROM `orders` WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $delete_id);
    mysqli_stmt_execute($stmt);
    flash('Order deleted.');
    redirect('admin_orders.php');
}

$page_title = 'Orders';
require 'includes/admin_head.php';

$filter = $_GET['status'] ?? 'all';
$sql = "SELECT * FROM `orders`";
if ($filter === 'pending' || $filter === 'completed') {
    $sql .= " WHERE payment_status = '" . ($filter === 'pending' ? 'pending' : 'completed') . "'";
}
$sql .= " ORDER BY placed_on DESC";
$orders = mysqli_query($conn, $sql);
?>

<div class="admin-title">
   <h1>Orders</h1>
   <p>Review incoming orders and mark payments as completed.</p>
</div>

<div class="panel" style="padding-bottom:14px;">
   <div style="display:flex;gap:10px;flex-wrap:wrap;">
      <a href="admin_orders.php?status=all" class="option-btn btn-sm <?php echo $filter === 'all' ? 'btn' : ''; ?>">All</a>
      <a href="admin_orders.php?status=pending" class="option-btn btn-sm <?php echo $filter === 'pending' ? 'btn' : ''; ?>">Pending</a>
      <a href="admin_orders.php?status=completed" class="option-btn btn-sm <?php echo $filter === 'completed' ? 'btn' : ''; ?>">Completed</a>
   </div>
</div>

<div class="data-list">
   <?php if (mysqli_num_rows($orders) > 0): ?>
      <?php while ($order = mysqli_fetch_assoc($orders)): ?>
         <div class="data-card">
            <div class="head-row">
               <div class="title-block">
                  <h4>Order #<?php echo (int) $order['id']; ?> -- <?php echo e($order['name']); ?></h4>
               </div>
               <span class="badge <?php echo e($order['payment_status']); ?>"><?php echo e(ucfirst($order['payment_status'])); ?></span>
            </div>
            <dl>
               <dt>Placed</dt><dd><?php echo date('d M Y, g:ia', strtotime($order['placed_on'])); ?></dd>
               <dt>Phone</dt><dd><?php echo e($order['number']); ?></dd>
               <dt>Email</dt><dd><?php echo e($order['email']); ?></dd>
               <dt>Address</dt><dd><?php echo e($order['address']); ?></dd>
               <dt>Method</dt><dd><?php echo e($order['method']); ?></dd>
               <dt>Items</dt><dd><?php echo e($order['total_products']); ?></dd>
               <dt>Total</dt><dd><strong>$<?php echo money($order['total_price']); ?></strong></dd>
            </dl>
            <div class="actions">
               <form action="" method="POST">
                  <?php echo csrf_field(); ?>
                  <input type="hidden" name="order_id" value="<?php echo (int) $order['id']; ?>">
                  <select name="payment_status" class="status-select">
                     <option value="pending" <?php echo $order['payment_status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                     <option value="completed" <?php echo $order['payment_status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                  </select>
                  <button type="submit" name="update_status" class="option-btn btn-sm">Update status</button>
               </form>
               <a href="admin_orders.php?delete=<?php echo (int) $order['id']; ?>" class="delete-btn btn-sm" onclick="return confirm('Delete this order record?');">Delete</a>
            </div>
         </div>
      <?php endwhile; ?>
   <?php else: ?>
      <div class="empty-state"><i class="fas fa-box-open"></i>No orders found.</div>
   <?php endif; ?>
</div>

<?php require 'includes/admin_foot.php'; ?>
