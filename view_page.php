<?php
require 'config.php';
require 'includes/functions.php';
require_login();
require 'includes/cart_actions.php';

$pid = (int) ($_GET['pid'] ?? 0);
$stmt = mysqli_prepare($conn, "SELECT * FROM `products` WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $pid);
mysqli_stmt_execute($stmt);
$product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

$page_title = $product ? $product['name'] : 'Product details';
require 'includes/head.php';
require 'header.php';
?>

<section class="page-heading">
   <div class="container">
      <h1>Product details</h1>
      <p><a href="home.php">Home</a> / <a href="shop.php">Shop</a> / <?php echo e($product['name'] ?? 'Not found'); ?></p>
   </div>
</section>

<section class="section">
   <div class="container">

   <?php if ($product): ?>
      <?php
         $low_stock = $product['stock'] <= 5 && $product['stock'] > 0;
         $out_of_stock = $product['stock'] <= 0;
      ?>
      <div class="product-detail">
         <div class="gallery">
            <img src="uploaded_img/<?php echo e($product['image']); ?>" alt="<?php echo e($product['name']); ?>">
         </div>
         <div class="info">
            <div class="category"><?php echo e($product['category']); ?></div>
            <h1><?php echo e($product['name']); ?></h1>
            <div class="price">$<?php echo money($product['price']); ?></div>
            <p class="details"><?php echo nl2br(e($product['details'])); ?></p>

            <p class="stock-line <?php echo $low_stock ? 'low' : ''; ?>">
               <?php if ($out_of_stock): ?>
                  <i class="fas fa-circle-xmark"></i> Out of stock
               <?php elseif ($low_stock): ?>
                  <i class="fas fa-triangle-exclamation"></i> Only <?php echo (int) $product['stock']; ?> left in stock
               <?php else: ?>
                  <i class="fas fa-circle-check"></i> In stock
               <?php endif; ?>
            </p>

            <form action="" method="POST">
               <?php echo csrf_field(); ?>
               <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>">
               <input type="hidden" name="product_name" value="<?php echo e($product['name']); ?>">
               <input type="hidden" name="product_price" value="<?php echo e($product['price']); ?>">
               <input type="hidden" name="product_image" value="<?php echo e($product['image']); ?>">

               <div class="qty-row" style="margin-bottom:18px;">
                  <div class="qty-stepper" data-step>
                     <button type="button" data-action="dec">&minus;</button>
                     <input type="number" name="product_quantity" value="1" min="1" max="<?php echo max(1, (int) $product['stock']); ?>" class="qty" <?php echo $out_of_stock ? 'disabled' : ''; ?>>
                     <button type="button" data-action="inc">+</button>
                  </div>
               </div>

               <div class="actions">
                  <button type="submit" name="add_to_cart" class="btn" <?php echo $out_of_stock ? 'disabled' : ''; ?>><i class="fas fa-shopping-cart"></i> Add to cart</button>
                  <button type="submit" name="add_to_wishlist" class="btn-outline" <?php echo $out_of_stock ? 'disabled' : ''; ?>><i class="fas fa-heart"></i> Add to wishlist</button>
               </div>
            </form>
         </div>
      </div>
   <?php else: ?>
      <p class="empty"><i class="fas fa-seedling"></i>We couldn't find that product. It may have been removed.</p>
      <div class="more-btn"><a href="shop.php" class="btn-outline">Back to shop</a></div>
   <?php endif; ?>

   </div>
</section>

<?php require 'footer.php'; require 'includes/foot.php'; ?>
