<?php
require 'config.php';
require 'includes/functions.php';
require_login();

$user_id = current_user_id();

if (isset($_POST['update_quantity'])) {
    csrf_check();
    $cart_id = (int) ($_POST['cart_id'] ?? 0);
    $qty = max(1, (int) ($_POST['cart_quantity'] ?? 1));

    // clamp to available stock
    $stmt = mysqli_prepare($conn, "SELECT c.id, p.stock, p.name FROM `cart` c JOIN `products` p ON p.id = c.pid WHERE c.id = ? AND c.user_id = ?");
    mysqli_stmt_bind_param($stmt, 'ii', $cart_id, $user_id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    if ($row) {
        $qty = min($qty, max(1, (int) $row['stock']));
        $upd = mysqli_prepare($conn, "UPDATE `cart` SET quantity = ? WHERE id = ? AND user_id = ?");
        mysqli_stmt_bind_param($upd, 'iii', $qty, $cart_id, $user_id);
        mysqli_stmt_execute($upd);
        mysqli_stmt_close($upd);
        flash('Updated quantity for ' . $row['name'] . '.');
    }
    redirect('cart.php');
}

if (isset($_GET['delete'])) {
    $delete_id = (int) $_GET['delete'];
    $stmt = mysqli_prepare($conn, "DELETE FROM `cart` WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, 'ii', $delete_id, $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    flash('Item removed from your cart.');
    redirect('cart.php');
}

if (isset($_GET['delete_all'])) {
    $stmt = mysqli_prepare($conn, "DELETE FROM `cart` WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    flash('Your cart has been emptied.');
    redirect('cart.php');
}

$page_title = 'Your cart';
require 'includes/head.php';
require 'header.php';

$stmt = mysqli_prepare($conn, "SELECT * FROM `cart` WHERE user_id = ? ORDER BY added_at DESC");
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$cart_items = mysqli_stmt_get_result($stmt);
$grand_total = 0;
$item_count = mysqli_num_rows($cart_items);
?>

<section class="page-heading">
   <div class="container">
      <h1>Your cart</h1>
      <p><a href="home.php">Home</a> / Cart</p>
   </div>
</section>

<section class="section">
   <div class="container">
      <div class="cart-layout">

         <div>
            <?php if ($item_count > 0): ?>
               <?php while ($item = mysqli_fetch_assoc($cart_items)):
                  $sub_total = $item['price'] * $item['quantity'];
                  $grand_total += $sub_total;
               ?>
               <div class="line-item">
                  <a href="cart.php?delete=<?php echo (int) $item['id']; ?>" class="remove-btn fas fa-xmark" onclick="return confirm('Remove this item from your cart?');" aria-label="Remove item"></a>
                  <div class="thumb"><img src="uploaded_img/<?php echo e($item['image']); ?>" alt="<?php echo e($item['name']); ?>"></div>
                  <div class="meta">
                     <a href="view_page.php?pid=<?php echo (int) $item['pid']; ?>" class="name"><?php echo e($item['name']); ?></a>
                     <div class="unit-price">$<?php echo money($item['price']); ?> each</div>
                     <div class="row-actions">
                        <form action="" method="POST" style="display:flex;align-items:center;gap:10px;">
                           <?php echo csrf_field(); ?>
                           <input type="hidden" name="cart_id" value="<?php echo (int) $item['id']; ?>">
                           <div class="qty-stepper" data-step>
                              <button type="button" data-action="dec">&minus;</button>
                              <input type="number" min="1" value="<?php echo (int) $item['quantity']; ?>" name="cart_quantity" class="qty">
                              <button type="button" data-action="inc">+</button>
                           </div>
                           <button type="submit" name="update_quantity" class="btn-outline btn-sm">Update</button>
                        </form>
                        <div class="sub-total">$<?php echo money($sub_total); ?></div>
                     </div>
                  </div>
               </div>
               <?php endwhile; ?>
            <?php else: ?>
               <p class="empty"><i class="fas fa-basket-shopping"></i>Your cart is empty.</p>
            <?php endif; ?>
         </div>

         <div class="summary-card">
            <h3>Order summary</h3>
            <div class="row"><span>Items</span><span><?php echo $item_count; ?></span></div>
            <div class="row total"><span>Grand total</span><span>$<?php echo money($grand_total); ?></span></div>
            <div class="actions">
               <a href="checkout.php" class="btn btn-block <?php echo $item_count > 0 ? '' : 'disabled'; ?>">Proceed to checkout</a>
               <a href="shop.php" class="btn-outline btn-block">Continue shopping</a>
               <?php if ($item_count > 0): ?>
                  <a href="cart.php?delete_all" class="btn-delete btn-block" style="display:flex;align-items:center;justify-content:center;border:1.5px solid #E7C9C2;border-radius:4px;padding:11px;color:#A23B2E;" onclick="return confirm('Remove everything from your cart?');">Empty cart</a>
               <?php endif; ?>
            </div>
         </div>

      </div>
   </div>
</section>

<?php require 'footer.php'; require 'includes/foot.php'; ?>
