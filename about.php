<?php
require 'config.php';
require 'includes/functions.php';
require_login();

$page_title = 'About';
require 'includes/head.php';
require 'header.php';
?>

<section class="page-heading">
   <div class="container">
      <h1>About us</h1>
      <p><a href="home.php">Home</a> / About</p>
   </div>
</section>

<section class="section">
   <div class="container">

      <div class="about-row">
         <div class="image"><img src="images/about-img-1.png" alt="Florist arranging a bouquet"></div>
         <div class="content">
            <h3>Why choose us?</h3>
            <p>Every stem passes through the hands of one of our florists before it reaches yours. We keep our range small on purpose, so nothing sits in a cold room for a week waiting to be picked.</p>
            <a href="shop.php" class="btn">Shop now</a>
         </div>
      </div>

      <div class="about-row reverse">
         <div class="content">
            <h3>What we provide</h3>
            <p>Hand-tied bunches, seasonal bouquets and mixed arrangements for birthdays, sympathy, weddings and the days that don't need an occasion at all. Same-day dispatch on orders placed before 2pm.</p>
            <a href="contact.php" class="btn">Contact us</a>
         </div>
         <div class="image"><img src="images/about-img-2.jpg" alt="A finished mixed bouquet"></div>
      </div>

      <div class="about-row">
         <div class="image"><img src="images/about-img-3.jpg" alt="Fresh flowers in the studio"></div>
         <div class="content">
            <h3>Who we are</h3>
            <p>Bloom &amp; Petal started as a single market stall and grew into a small studio of florists who still buy from the same growers we did on day one.</p>
            <a href="#reviews" class="btn">Read reviews</a>
         </div>
      </div>

   </div>
</section>

<section class="section reviews" id="reviews">
   <div class="container">
      <div class="section-head">
         <p class="eyebrow">What people say</p>
         <h2>Customer reviews</h2>
      </div>

      <div class="box-container">
         <div class="review-card">
            <img src="images/pic-1.png" alt="">
            <div class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-stroke"></i></div>
            <p>The roses lasted almost two weeks and the arrangement looked exactly like the photo. Will be ordering again for my mum's birthday.</p>
            <h3>Priya M.</h3>
         </div>
         <div class="review-card">
            <img src="images/pic-2.png" alt="">
            <div class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-stroke"></i></div>
            <p>Ordered same-day for an anniversary and it arrived beautifully wrapped. The tulips were still tightly closed, which I loved.</p>
            <h3>James O.</h3>
         </div>
         <div class="review-card">
            <img src="images/pic-3.png" alt="">
            <div class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-stroke"></i></div>
            <p>Sent the white bouquet for a friend's sympathy. Simple, elegant, and the checkout process was quick from my phone.</p>
            <h3>Aisha K.</h3>
         </div>
         <div class="review-card">
            <img src="images/pic-4.png" alt="">
            <div class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-stroke"></i></div>
            <p>The mixed arrangement was even better in person than in the listing photo. Great value for the price.</p>
            <h3>Daniel R.</h3>
         </div>
         <div class="review-card">
            <img src="images/pic-5.png" alt="">
            <div class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-stroke"></i></div>
            <p>Customer support replied to my question within the hour and helped me change the delivery address.</p>
            <h3>Sara T.</h3>
         </div>
         <div class="review-card">
            <img src="images/pic-6.png" alt="">
            <div class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-stroke"></i></div>
            <p>My go-to for last-minute gifts now. The wishlist feature makes it easy to plan ahead for birthdays too.</p>
            <h3>Marcus L.</h3>
         </div>
      </div>
   </div>
</section>

<?php require 'footer.php'; require 'includes/foot.php'; ?>
