<?php
require 'config.php';
require 'includes/functions.php';
require_admin();

if (isset($_GET['read'])) {
    $id = (int) $_GET['read'];
    $stmt = mysqli_prepare($conn, "UPDATE `message` SET is_read = 1 WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    redirect('admin_contacts.php');
}

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = mysqli_prepare($conn, "DELETE FROM `message` WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    flash('Message deleted.');
    redirect('admin_contacts.php');
}

$page_title = 'Messages';
require 'includes/admin_head.php';

$messages = mysqli_query($conn, "SELECT * FROM `message` ORDER BY is_read ASC, created_at DESC");
?>

<div class="admin-title">
   <h1>Messages</h1>
   <p>Enquiries submitted through the contact form.</p>
</div>

<div class="data-list">
   <?php if (mysqli_num_rows($messages) > 0): ?>
      <?php while ($m = mysqli_fetch_assoc($messages)): ?>
         <div class="data-card">
            <div class="head-row">
               <div class="title-block">
                  <h4><?php echo e($m['name']); ?></h4>
               </div>
               <?php if (!$m['is_read']): ?>
                  <span class="badge unread">New</span>
               <?php endif; ?>
            </div>
            <dl>
               <dt>Email</dt><dd><?php echo e($m['email']); ?></dd>
               <dt>Phone</dt><dd><?php echo e($m['number']); ?></dd>
               <dt>Received</dt><dd><?php echo date('d M Y, g:ia', strtotime($m['created_at'])); ?></dd>
               <dt>Message</dt><dd><?php echo nl2br(e($m['message'])); ?></dd>
            </dl>
            <div class="actions">
               <?php if (!$m['is_read']): ?>
                  <a href="admin_contacts.php?read=<?php echo (int) $m['id']; ?>" class="option-btn btn-sm">Mark as read</a>
               <?php endif; ?>
               <a href="admin_contacts.php?delete=<?php echo (int) $m['id']; ?>" class="delete-btn btn-sm" onclick="return confirm('Delete this message?');">Delete</a>
            </div>
         </div>
      <?php endwhile; ?>
   <?php else: ?>
      <div class="empty-state"><i class="fas fa-envelope-open"></i>No messages yet.</div>
   <?php endif; ?>
</div>

<?php require 'includes/admin_foot.php'; ?>
