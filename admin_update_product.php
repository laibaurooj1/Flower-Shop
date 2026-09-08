<?php
require 'config.php';
require 'includes/functions.php';
require_admin();

$upload_dir = __DIR__ . '/uploaded_img/';
$allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
$allowed_mime = ['image/jpeg', 'image/png', 'image/webp'];

$pid = (int) ($_GET['pid'] ?? 0);
$stmt = mysqli_prepare($conn, "SELECT * FROM `products` WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $pid);
mysqli_stmt_execute($stmt);
$product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$product) {
    flash('That product could not be found.', 'error');
    redirect('admin_products.php');
}

if (isset($_POST['update_product'])) {
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

    $image_name = $product['image'];
    $old_image = null;

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
            $old_image = $image_name;
            $image_name = bin2hex(random_bytes(8)) . '.' . $ext;
        }
    }

    if ($errors) {
        foreach ($errors as $err) flash($err, 'error');
        redirect('admin_update_product.php?pid=' . $pid);
    }

    if ($old_image !== null) {
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name);
        if (is_file($upload_dir . $old_image)) unlink($upload_dir . $old_image);
    }

    $upd = mysqli_prepare($conn, "UPDATE `products` SET name = ?, category = ?, details = ?, price = ?, stock = ?, image = ? WHERE id = ?");
    mysqli_stmt_bind_param($upd, 'sssdisi', $name, $category, $details, $price, $stock, $image_name, $pid);
    mysqli_stmt_execute($upd);

    flash('Product updated successfully.');
    redirect('admin_products.php');
}

$page_title = 'Edit product';
require 'includes/admin_head.php';
?>

<div class="admin-title">
   <h1>Edit product</h1>
   <p>Update the details for this listing.</p>
</div>

<div class="panel">
   <div class="image-preview-current">
      <img src="uploaded_img/<?php echo e($product['image']); ?>" alt="<?php echo e($product['name']); ?>">
      <span>Current image -- upload a new file below only if you want to replace it.</span>
   </div>

   <form action="" method="POST" enctype="multipart/form-data">
      <?php echo csrf_field(); ?>
      <div class="form-grid cols-2">
         <div>
            <label class="field-label" for="p_name">Product name</label>
            <input type="text" id="p_name" name="name" class="box" value="<?php echo e($product['name']); ?>" required>
         </div>
         <div>
            <label class="field-label" for="p_category">Category</label>
            <input type="text" id="p_category" name="category" class="box" value="<?php echo e($product['category']); ?>" required>
         </div>
         <div>
            <label class="field-label" for="p_price">Price (USD)</label>
            <input type="number" id="p_price" name="price" class="box" value="<?php echo e($product['price']); ?>" step="0.01" min="0.01" required>
         </div>
         <div>
            <label class="field-label" for="p_stock">Stock quantity</label>
            <input type="number" id="p_stock" name="stock" class="box" value="<?php echo (int) $product['stock']; ?>" min="0" required>
         </div>
      </div>
      <label class="field-label" for="p_details">Description</label>
      <textarea id="p_details" name="details" class="box" required><?php echo e($product['details']); ?></textarea>
      <label class="field-label" for="p_image">Replace image (optional)</label>
      <input type="file" id="p_image" name="image" class="box" accept="image/jpeg,image/png,image/webp">
      <div style="display:flex;gap:10px;">
         <button type="submit" name="update_product" class="btn">Save changes</button>
         <a href="admin_products.php" class="option-btn">Cancel</a>
      </div>
   </form>
</div>

<?php require 'includes/admin_foot.php'; ?>
