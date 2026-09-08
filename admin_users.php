<?php
require 'config.php';
require 'includes/functions.php';
require_admin();

if (isset($_GET['delete'])) {
    $delete_id = (int) $_GET['delete'];
    if ($delete_id === (int) current_admin_id()) {
        flash("You can't delete your own admin account while logged in.", 'error');
    } else {
        $stmt = mysqli_prepare($conn, "DELETE FROM `users` WHERE id = ? AND user_type = 'user'");
        mysqli_stmt_bind_param($stmt, 'i', $delete_id);
        mysqli_stmt_execute($stmt);
        flash('Customer account deleted.');
    }
    redirect('admin_users.php');
}

$page_title = 'Customers';
require 'includes/admin_head.php';

$users = mysqli_query($conn, "SELECT u.*,
    (SELECT COUNT(*) FROM orders o WHERE o.user_id = u.id) AS order_count
    FROM `users` u WHERE u.user_type = 'user' ORDER BY u.created_at DESC");
?>

<div class="admin-title">
   <h1>Customers</h1>
   <p>Everyone who has registered an account on the shop.</p>
</div>

<div class="data-list">
   <?php if (mysqli_num_rows($users) > 0): ?>
      <?php while ($u = mysqli_fetch_assoc($users)): ?>
         <div class="data-card">
            <div class="head-row">
               <div class="title-block">
                  <h4><?php echo e($u['name']); ?></h4>
               </div>
               <span class="badge user">Customer</span>
            </div>
            <dl>
               <dt>Email</dt><dd><?php echo e($u['email']); ?></dd>
               <dt>Joined</dt><dd><?php echo date('d M Y', strtotime($u['created_at'])); ?></dd>
               <dt>Orders placed</dt><dd><?php echo (int) $u['order_count']; ?></dd>
            </dl>
            <div class="actions">
               <a href="admin_users.php?delete=<?php echo (int) $u['id']; ?>" class="delete-btn btn-sm" onclick="return confirm('Delete this customer account? This cannot be undone.');">Delete account</a>
            </div>
         </div>
      <?php endwhile; ?>
   <?php else: ?>
      <div class="empty-state"><i class="fas fa-users"></i>No customers registered yet.</div>
   <?php endif; ?>
</div>

<?php require 'includes/admin_foot.php'; ?>
