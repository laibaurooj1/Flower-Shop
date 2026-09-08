<?php
require 'config.php';
require 'includes/functions.php';
require_admin();

$upload_dir = __DIR__ . '/uploaded_img/';
$allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
$allowed_mime = ['image/jpeg', 'image/png', 'image/webp'];

if (isset($_POST['add_product'])) {
    csrf_check();

    $name     = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $details  = trim($_POST['details'] ?? '');
    $price    = (float) ($_POST['price'] ?? 0);
    $stock    = (int) ($_POST['stock'] ?? 0);

    $errors = [];
    if ($name === '' || $category === '' || $details === '') {
        $errors[] = 'Please fill in every field.';
    }
    if ($price <= 0) {
        $errors[] = 'Price must be greater than zero.';
    }
    if ($stock < 0) {
        $errors[] = 'Stock cannot be negative.';
    }

    $image_name = '';
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $mime = mime_content_type($_FILES['image']['tmp_name']);

        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'There was a problem uploading the image.';
        } elseif (!in_array($ext, $allowed_ext, true) || !in_array($mime, $allowed_mime, true)) {
            $errors[] = 'Image must be a JPG, PNG, or WEBP file.';
        } elseif ($_FILES['image']['size'] > 4 * 1024 * 1024) {
            $errors[] = 'Image must be smaller than 4MB.';
        } else {
            $image_name = bin2hex(random_bytes(8)) . '.' . $ext;
        }
    } else {
        $errors[] = 'Please choose a product image.';
    }

    if ($errors) {
        foreach ($errors as $err) flash($err, 'error');
        redirect('admin_products.php');
    }

    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
    move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name);

    $stmt = mysqli_prepare($conn, "INSERT INTO `products`(name, category, details, price, stock, image) VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'sssdis', $name, $category, $details, $price, $stock, $image_name);
    mysqli_stmt_execute($stmt);

    flash('Product added successfully.');
    redirect('admin_products.php');
}

if (isset($_GET['delete'])) {
    $delete_id = (int) $_GET['delete'];

    $stmt = mysqli_prepare($conn, "SELECT image FROM `products` WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $delete_id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    $del = mysqli_prepare($conn, "DELETE FROM `products` WHERE id = ?");
    mysqli_stmt_bind_param($del, 'i', $delete_id);
    mysqli_stmt_execute($del);

    if ($row && $row['image'] && is_file($upload_dir . $row['image'])) {
        unlink($upload_dir . $row['image']);
    }

    flash('Product deleted.');
    redirect('admin_products.php');
}

$page_title = 'Products';
require 'includes/admin_head.php';

$products = mysqli_query($conn, "SELECT * FROM `products` ORDER BY created_at DESC");
?>

<div class="admin-title">
   <h1>Products</h1>
   <p>Add new arrangements and manage what's currently for sale.</p>
</div>

<div class="panel">
   <h2>Add a new product</h2>
   <form action="" method="POST" enctype="multipart/form-data">
      <?php echo csrf_field(); ?>
      <div class="form-grid cols-2">
         <div>
            <label class="field-label" for="p_name">Product name</label>
            <input type="text" id="p_name" name="name" class="box" placeholder="e.g. Pink Rose Bunch" required>
         </div>
         <div>
            <label class="field-label" for="p_category">Category</label>
            <input type="text" id="p_category" name="category" class="box" placeholder="e.g. Roses" required list="category-list">
            <datalist id="category-list">
               <option value="Roses">
               <option value="Tulips">
               <option value="Bouquets">
               <option value="Mixed Arrangements">
            </datalist>
         </div>
         <div>
            <label class="field-label" for="p_price">Price (USD)</label>
            <input type="number" id="p_price" name="price" class="box" placeholder="e.g. 24.99" step="0.01" min="0.01" required>
         </div>
         <div>
            <label class="field-label" for="p_stock">Stock quantity</label>
            <input type="number" id="p_stock" name="stock" class="box" placeholder="e.g. 20" min="0" required>
         </div>
      </div>
      <label class="field-label" for="p_details">Description</label>
      <textarea id="p_details" name="details" class="box" placeholder="Describe the arrangement..." required></textarea>
      <label class="field-label" for="p_image">Product image (JPG, PNG, or WEBP, max 4MB)</label>
      <input type="file" id="p_image" name="image" class="box" accept="image/jpeg,image/png,image/webp" required>
      <button type="submit" name="add_product" class="btn">Add product</button>
   </form>
</div>

<div class="panel">
   <h2>Current products (<?php echo mysqli_num_rows($products); ?>)</h2>
   <div class="data-list">
      <?php if (mysqli_num_rows($products) > 0): ?>
         <?php while ($product = mysqli_fetch_assoc($products)): ?>
            <div class="data-card">
               <div class="head-row">
                  <div class="title-block">
                     <div class="thumb"><img src="uploaded_img/<?php echo e($product['image']); ?>" alt="<?php echo e($product['name']); ?>"></div>
                     <div>
                        <h4><?php echo e($product['name']); ?></h4>
                        <div class="sub"><?php echo e($product['category']); ?> &middot; $<?php echo money($product['price']); ?></div>
                     </div>
                  </div>
                  <?php if ($product['stock'] <= 5): ?>
                     <span class="badge low-stock"><?php echo (int) $product['stock']; ?> left</span>
                  <?php else: ?>
                     <span class="badge completed"><?php echo (int) $product['stock']; ?> in stock</span>
                  <?php endif; ?>
               </div>
               <div class="actions">
                  <a href="admin_update_product.php?pid=<?php echo (int) $product['id']; ?>" class="option-btn btn-sm">Edit</a>
                  <a href="admin_products.php?delete=<?php echo (int) $product['id']; ?>" class="delete-btn btn-sm" onclick="return confirm('Delete this product? This cannot be undone.');">Delete</a>
               </div>
            </div>
         <?php endwhile; ?>
      <?php else: ?>
         <div class="empty-state"><i class="fas fa-seedling"></i>No products yet -- add your first one above.</div>
      <?php endif; ?>
   </div>
</div>

<?php require 'includes/admin_foot.php'; ?>
