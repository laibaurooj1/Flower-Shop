<?php
require 'config.php';
require 'includes/functions.php';
require_login();

$user_id = current_user_id();

if (isset($_POST['add_to_cart'])) {
    csrf_check();
    $product_id = (int) ($_POST['product_id'] ?? 0);

    $stmt = mysqli_prepare($conn, "SELECT id, name, price, image, stock FROM `products` WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $product_id);
    mysqli_stmt_execute($stmt);
    $product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$product) {
        flash('That product could not be found.', 'error');
    } elseif ((int) $product['stock'] <= 0) {
        flash($product['name'] . ' is currently out of stock.', 'error');
    } else {
        $check = mysqli_prepare($conn, "SELECT id, quantity FROM `cart` WHERE pid = ? AND user_id = ?");
        mysqli_stmt_bind_param($check, 'ii', $product_id, $user_id);
        mysqli_stmt_execute($check);
        $existing = mysqli_fetch_assoc(mysqli_stmt_get_result($check));

        if ($existing) {
            $new_qty = min((int) $product['stock'], (int) $existing['quantity'] + 1);
            $upd = mysqli_prepare($conn, "UPDATE `cart` SET quantity = ? WHERE id = ?");
            mysqli_stmt_bind_param($upd, 'ii', $new_qty, $existing['id']);
            mysqli_stmt_execute($upd);
        } else {
            $ins = mysqli_prepare($conn, "INSERT INTO `cart`(user_id, pid, name, price, quantity, image) VALUES (?, ?, ?, ?, 1, ?)");
            mysqli_stmt_bind_param($ins, 'iisds', $user_id, $product_id, $product['name'], $product['price'], $product['image']);
            mysqli_stmt_execute($ins);
        }

        $del = mysqli_prepare($conn, "DELETE FROM `wishlist` WHERE pid = ? AND user_id = ?");
        mysqli_stmt_bind_param($del, 'ii', $product_id, $user_id);
        mysqli_stmt_execute($del);

        flash($product['name'] . ' moved to your cart.');
    }
    redirect('wishlist.php');
}

if (isset($_GET['delete'])) {
    $delete_id = (int) $_GET['delete'];
    $stmt = mysqli_prepare($conn, "DELETE FROM `wishlist` WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, 'ii', $delete_id, $user_id);
    mysqli_stmt_execute($stmt);
    flash('Item removed from your wishlist.');
    redirect('wishlist.php');
}

if (isset($_GET['delete_all'])) {
    $stmt = mysqli_prepare($conn, "DELETE FROM `wishlist` WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    flash('Your wishlist has been cleared.');
    redirect('wishlist.php');
}

$page_title = 'Your wishlist';
require 'includes/head.php';
require 'header.php';

$stmt = mysqli_prepare($conn, "SELECT * FROM `wishlist` WHERE user_id = ? ORDER BY added_at DESC");
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$wishlist_items = mysqli_stmt_get_result($stmt);
$grand_total = 0;
$item_count = mysqli_num_rows($wishlist_items);
?>

<section class="page-heading">
   <div class="container">
      <h1>Your wishlist</h1>
      <p><a href="home.php">Home</a> / Wishlist</p>
   </div>
</section>

<section class="section products">
   <div class="container">

      <div class="box-container">
         <?php if ($item_count > 0): ?>
            <?php while ($item = mysqli_fetch_assoc($wishlist_items)): $grand_total += $item['price']; ?>
            <div class="product-card">
               <div class="thumb">
                  <a href="wishlist.php?delete=<?php echo (int) $item['id']; ?>" class="quick-view" style="right:auto;left:10px;" onclick="return confirm('Remove this from your wishlist?');" aria-label="Remove"><i class="fas fa-xmark"></i></a>
                  <a href="view_page.php?pid=<?php echo (int) $item['pid']; ?>" class="quick-view" aria-label="View details"><i class="fas fa-eye"></i></a>
                  <img src="uploaded_img/<?php echo e($item['image']); ?>" alt="<?php echo e($item['name']); ?>" loading="lazy">
               </div>
               <form action="" method="POST" class="body">
                  <?php echo csrf_field(); ?>
                  <input type="hidden" name="product_id" value="<?php echo (int) $item['pid']; ?>">
                  <div class="name"><?php echo e($item['name']); ?></div>
                  <div class="price">$<?php echo money($item['price']); ?></div>
                  <div class="actions">
                     <button type="submit" name="add_to_cart" class="btn"><i class="fas fa-shopping-cart"></i> Move to cart</button>
                  </div>
               </form>
            </div>
            <?php endwhile; ?>
         <?php else: ?>
            <p class="empty"><i class="fas fa-heart-crack"></i>Your wishlist is empty.</p>
         <?php endif; ?>
      </div>

      <div class="summary-card" style="max-width:420px;margin-top:34px;">
         <div class="row total"><span>Wishlist total</span><span>$<?php echo money($grand_total); ?></span></div>
         <div class="actions">
            <a href="shop.php" class="btn-outline btn-block">Continue shopping</a>
            <?php if ($item_count > 0): ?>
               <a href="wishlist.php?delete_all" class="btn-block" style="display:flex;align-items:center;justify-content:center;border:1.5px solid #E7C9C2;border-radius:4px;padding:11px;color:#A23B2E;" onclick="return confirm('Clear your entire wishlist?');">Clear wishlist</a>
            <?php endif; ?>
         </div>
      </div>

   </div>
</section>

<?php require 'footer.php'; require 'includes/foot.php'; ?>
