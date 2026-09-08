<?php
require 'config.php';
require 'includes/functions.php';
require_login();
require 'includes/cart_actions.php';

$page_title = 'Shop';
require 'includes/head.php';
require 'header.php';

/* -- filters -- */
$category = trim($_GET['category'] ?? '');
$search   = trim($_GET['q'] ?? '');
$sort     = $_GET['sort'] ?? 'newest';

$sort_map = [
    'newest'     => 'created_at DESC',
    'price_low'  => 'price ASC',
    'price_high' => 'price DESC',
    'name'       => 'name ASC',
];
$order_by = $sort_map[$sort] ?? $sort_map['newest'];

$where = [];
$types = '';
$params = [];

if ($category !== '') {
    $where[] = 'category = ?';
    $types .= 's';
    $params[] = $category;
}
if ($search !== '') {
    $where[] = '(name LIKE ? OR details LIKE ?)';
    $types .= 'ss';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}

$sql = 'SELECT * FROM `products`';
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY ' . $order_by;

$stmt = mysqli_prepare($conn, $sql);
if ($types !== '') {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$products = mysqli_stmt_get_result($stmt);
$total_results = mysqli_num_rows($products);

$categories = mysqli_query($conn, "SELECT DISTINCT category FROM `products` ORDER BY category");
?>

<section class="page-heading">
   <div class="container">
      <h1>Shop</h1>
      <p><a href="home.php">Home</a> / Shop</p>
   </div>
</section>

<section class="section products" style="padding-top:40px;">
   <div class="container">

      <div class="products-toolbar">
         <form action="" method="GET">
            <input type="text" name="q" class="box" placeholder="Search flowers..." value="<?php echo e($search); ?>" style="width:220px;">
            <select name="category" onchange="this.form.submit()">
               <option value="">All categories</option>
               <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                  <option value="<?php echo e($cat['category']); ?>" <?php echo $category === $cat['category'] ? 'selected' : ''; ?>><?php echo e($cat['category']); ?></option>
               <?php endwhile; ?>
            </select>
            <select name="sort" onchange="this.form.submit()">
               <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest first</option>
               <option value="price_low" <?php echo $sort === 'price_low' ? 'selected' : ''; ?>>Price: low to high</option>
               <option value="price_high" <?php echo $sort === 'price_high' ? 'selected' : ''; ?>>Price: high to low</option>
               <option value="name" <?php echo $sort === 'name' ? 'selected' : ''; ?>>Name: A to Z</option>
            </select>
            <button type="submit" class="btn-outline btn-sm">Apply</button>
         </form>
         <p class="results-count"><?php echo $total_results; ?> product<?php echo $total_results === 1 ? '' : 's'; ?> found</p>
      </div>

      <div class="box-container">
         <?php if ($total_results > 0): ?>
            <?php while ($product = mysqli_fetch_assoc($products)): ?>
               <?php include 'includes/product_card.php'; ?>
            <?php endwhile; ?>
         <?php else: ?>
            <p class="empty"><i class="fas fa-magnifying-glass"></i>No products match your search. Try a different keyword or category.</p>
         <?php endif; ?>
      </div>

   </div>
</section>

<?php require 'footer.php'; require 'includes/foot.php'; ?>
