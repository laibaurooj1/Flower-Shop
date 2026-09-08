<?php
$wishlist_count = 0;
$cart_count = 0;
if (current_user_id()) {
    $wishlist_count = count_rows($conn, 'wishlist', current_user_id());
    $cart_count = count_rows($conn, 'cart', current_user_id());
}
?>
<header class="site-header">
   <div class="flex">

      <button id="menu-btn" class="fas fa-bars" aria-label="Open menu"></button>

      <a href="home.php" class="logo">Bloom<span>&amp;</span>Petal</a>

      <nav class="navbar">
         <ul>
            <li><a href="home.php">Home</a></li>
            <li class="dropdown">
               <a href="#">Pages</a>
               <ul>
                  <li><a href="about.php">About</a></li>
                  <li><a href="contact.php">Contact</a></li>
               </ul>
            </li>
            <li><a href="shop.php">Shop</a></li>
            <li><a href="orders.php">My orders</a></li>
         </ul>
      </nav>

      <div class="icons">
         <a href="search_page.php" aria-label="Search"><i class="fas fa-search"></i></a>
         <a href="wishlist.php" aria-label="Wishlist"><i class="fas fa-heart"></i><span class="count"><?php echo $wishlist_count; ?></span></a>
         <a href="cart.php" aria-label="Cart"><i class="fas fa-shopping-cart"></i><span class="count"><?php echo $cart_count; ?></span></a>
         <button id="user-btn" aria-label="Account"><i class="fas fa-user"></i></button>
      </div>

      <div class="account-box">
         <p>Signed in as <span><?php echo e($_SESSION['user_name'] ?? ''); ?></span></p>
         <p>Email <span><?php echo e($_SESSION['user_email'] ?? ''); ?></span></p>
         <a href="logout.php" class="btn-delete btn btn-sm" style="background:transparent;color:#A23B2E;border:1.5px solid #E7C9C2;">Log out</a>
      </div>

   </div>
</header>

<div class="mobile-nav" id="mobile-nav">
   <div class="backdrop"></div>
   <div class="panel">
      <button class="close-btn fas fa-xmark" aria-label="Close menu"></button>
      <ul>
         <li><a href="home.php">Home</a></li>
         <li><a href="shop.php">Shop</a></li>
         <li><a href="about.php">About</a></li>
         <li><a href="contact.php">Contact</a></li>
         <li><a href="orders.php">My orders</a></li>
         <li><a href="wishlist.php">Wishlist (<?php echo $wishlist_count; ?>)</a></li>
         <li><a href="cart.php">Cart (<?php echo $cart_count; ?>)</a></li>
         <li><a href="logout.php">Log out</a></li>
      </ul>
   </div>
</div>
