<?php
require 'config.php';
require 'includes/functions.php';
require_login();
require 'includes/cart_actions.php';

$page_title = 'Search';
require 'includes/head.php';
require 'header.php';

$search_box = trim($_GET['search_box'] ?? ($_POST['search_box'] ?? ''));
$has_searched = $search_box !== '';
$products = null;

if ($has_searched) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM `products` WHERE name LIKE ? OR details LIKE ? ORDER BY name ASC");
    $like = '%' . $search_box . '%';
    mysqli_stmt_bind_param($stmt, 'ss', $like, $like);
    mysqli_stmt_execute($stmt);
    $products = mysqli_stmt_get_result($stmt);
}
?>

<section class="page-heading">
   <div class="container">
      <h1>Search</h1>
      <p><a href="home.php">Home</a> / Search</p>
   </div>
</section>

<section class="section" style="padding-bottom:0;">
   <div class="container">
      <form action="" method="GET" style="display:flex;gap:12px;flex-wrap:wrap;max-width:520px;">
         <input type="text" class="box" placeholder="Search for roses, tulips, bouquets..." name="search_box" value="<?php echo e($search_box); ?>" style="flex:1;">
         <button type="submit" class="btn">Search</button>
      </form>
   </div>
</section>

<section class="section products">
   <div class="container">
      <div class="box-container">
         <?php if (!$has_searched): ?>
            <p class="empty"><i class="fas fa-magnifying-glass"></i>Start typing to search our flowers.</p>
         <?php elseif (mysqli_num_rows($products) > 0): ?>
            <?php while ($product = mysqli_fetch_assoc($products)): ?>
               <?php include 'includes/product_card.php'; ?>
            <?php endwhile; ?>
         <?php else: ?>
            <p class="empty"><i class="fas fa-seedling"></i>No results for "<?php echo e($search_box); ?>".</p>
         <?php endif; ?>
      </div>
   </div>
</section>

<?php require 'footer.php'; require 'includes/foot.php'; ?>
