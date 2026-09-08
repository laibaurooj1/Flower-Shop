<?php
require 'config.php';
require 'includes/functions.php';
require_login();
require 'includes/cart_actions.php';

$page_title = 'Home';
require 'includes/head.php';
require 'header.php';

$featured = mysqli_query($conn, "SELECT * FROM `products` ORDER BY created_at DESC LIMIT 6");
?>

<section class="hero">
   <div class="container">
      <div class="copy">
         <p class="eyebrow">Fresh from the garden, this morning</p>
         <h1>Flowers for the moments worth slowing down for</h1>
         <p class="lede">Hand-tied bouquets and arrangements, cut and bundled the same day you order. Simple, seasonal, delivered with care.</p>
         <div class="actions">
            <a href="shop.php" class="btn">Shop the collection</a>
            <a href="about.php" class="btn-outline">Our story</a>
         </div>
      </div>
      <div class="figure">
         <img src="images/home-bg.png" alt="A fresh mixed flower arrangement">
      </div>
   </div>
</section>

<section class="category-strip">
   <div class="container">
      <div class="row">
         <a href="shop.php">All flowers</a>
         <a href="shop.php?category=Roses">Roses</a>
         <a href="shop.php?category=Tulips">Tulips</a>
         <a href="shop.php?category=Bouquets">Bouquets</a>
         <a href="shop.php?category=Mixed+Arrangements">Mixed arrangements</a>
      </div>
   </div>
</section>

<section class="section products">
   <div class="container">
      <div class="section-head">
         <p class="eyebrow">This week's picks</p>
         <h2>Newest arrivals</h2>
      </div>

      <div class="box-container">
         <?php if (mysqli_num_rows($featured) > 0): ?>
            <?php while ($product = mysqli_fetch_assoc($featured)): ?>
               <?php include 'includes/product_card.php'; ?>
            <?php endwhile; ?>
         <?php else: ?>
            <p class="empty"><i class="fas fa-seedling"></i>No products added yet -- check back soon.</p>
         <?php endif; ?>
      </div>

      <div class="more-btn">
         <a href="shop.php" class="btn-outline">See the full shop</a>
      </div>
   </div>
</section>

<section class="section value-props">
   <div class="container">
      <div class="value-prop">
         <i class="fas fa-leaf"></i>
         <h3>Cut fresh, not stockpiled</h3>
         <p>We order small and often, so what you receive was still in bud a day or two ago.</p>
      </div>
      <div class="value-prop">
         <i class="fas fa-truck-fast"></i>
         <h3>Same-day dispatch</h3>
         <p>Order before 2pm and your bouquet leaves the studio the same afternoon.</p>
      </div>
      <div class="value-prop">
         <i class="fas fa-hands-holding-circle"></i>
         <h3>Hand-tied by florists</h3>
         <p>Every bunch is arranged by hand -- never machine-bundled, never rushed.</p>
      </div>
   </div>
</section>

<section class="home-contact">
   <div class="container">
      <h2>Have a question about an order or a custom arrangement?</h2>
      <p>Our small team replies to every message personally, usually within a few hours.</p>
      <a href="contact.php" class="btn">Get in touch</a>
   </div>
</section>

<?php require 'footer.php'; require 'includes/foot.php'; ?>
