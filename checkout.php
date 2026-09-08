<?php
require 'config.php';
require 'includes/functions.php';
require_login();

$user_id = current_user_id();

if (isset($_POST['order'])) {
    csrf_check();

    $name    = trim($_POST['name'] ?? '');
    $number  = trim($_POST['number'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $method  = trim($_POST['method'] ?? '');
    $address = trim(
        'Flat/House: ' . ($_POST['flat'] ?? '') .
        ', ' . ($_POST['street'] ?? '') .
        ', ' . ($_POST['city'] ?? '') .
        ', ' . ($_POST['state'] ?? '') .
        ', ' . ($_POST['country'] ?? '') .
        ' - ' . ($_POST['pin_code'] ?? '')
    );

    $errors = [];
    if ($name === '' || $number === '' || $email === '') {
        $errors[] = 'Please fill in your name, phone number and email.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    $stmt = mysqli_prepare($conn, "SELECT * FROM `cart` WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $cart_result = mysqli_stmt_get_result($stmt);

    $cart_total = 0;
    $cart_lines = [];
    while ($item = mysqli_fetch_assoc($cart_result)) {
        $cart_lines[] = $item['name'] . ' (' . $item['quantity'] . ')';
        $cart_total += $item['price'] * $item['quantity'];
    }
    $total_products = implode(', ', $cart_lines);

    if ($cart_total <= 0) {
        $errors[] = 'Your cart is empty.';
    }

    if ($errors) {
        foreach ($errors as $err) flash($err, 'error');
        redirect('checkout.php');
    }

    $ins = mysqli_prepare($conn, "INSERT INTO `orders`(user_id, name, number, email, method, address, total_products, total_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($ins, 'issssssd', $user_id, $name, $number, $email, $method, $address, $total_products, $cart_total);
    mysqli_stmt_execute($ins);
    mysqli_stmt_close($ins);

    $del = mysqli_prepare($conn, "DELETE FROM `cart` WHERE user_id = ?");
    mysqli_stmt_bind_param($del, 'i', $user_id);
    mysqli_stmt_execute($del);

    flash('Order placed successfully! You can track it on the Orders page.');
    redirect('orders.php');
}

$page_title = 'Checkout';
require 'includes/head.php';
require 'header.php';

$stmt = mysqli_prepare($conn, "SELECT * FROM `cart` WHERE user_id = ? ORDER BY added_at DESC");
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$cart_items = mysqli_stmt_get_result($stmt);
$grand_total = 0;
$rows = [];
while ($row = mysqli_fetch_assoc($cart_items)) {
    $row['sub_total'] = $row['price'] * $row['quantity'];
    $grand_total += $row['sub_total'];
    $rows[] = $row;
}

$user_stmt = mysqli_prepare($conn, "SELECT name, email FROM `users` WHERE id = ?");
mysqli_stmt_bind_param($user_stmt, 'i', $user_id);
mysqli_stmt_execute($user_stmt);
$account = mysqli_fetch_assoc(mysqli_stmt_get_result($user_stmt));
?>

<section class="page-heading">
   <div class="container">
      <h1>Checkout</h1>
      <p><a href="home.php">Home</a> / <a href="cart.php">Cart</a> / Checkout</p>
   </div>
</section>

<section class="section">
   <div class="container">

      <?php if (empty($rows)): ?>
         <p class="empty"><i class="fas fa-basket-shopping"></i>Your cart is empty -- add something lovely before checking out.</p>
         <div class="more-btn"><a href="shop.php" class="btn-outline">Go to shop</a></div>
      <?php else: ?>

      <div class="checkout-layout">

         <form action="" method="POST" class="checkout-form">
            <?php echo csrf_field(); ?>

            <fieldset>
               <legend>Contact details</legend>
               <div class="checkout-form grid-2">
                  <div class="field">
                     <label for="name">Full name</label>
                     <input type="text" id="name" name="name" placeholder="Your name" value="<?php echo e($account['name'] ?? ''); ?>" required>
                  </div>
                  <div class="field">
                     <label for="number">Phone number</label>
                     <input type="tel" id="number" name="number" placeholder="e.g. 9876543210" required>
                  </div>
               </div>
               <div class="field">
                  <label for="email">Email</label>
                  <input type="email" id="email" name="email" placeholder="you@example.com" value="<?php echo e($account['email'] ?? ''); ?>" required>
               </div>
            </fieldset>

            <fieldset>
               <legend>Delivery address</legend>
               <div class="grid-2">
                  <div class="field">
                     <label for="flat">Flat / house no.</label>
                     <input type="text" id="flat" name="flat" placeholder="e.g. Flat 4B" required>
                  </div>
                  <div class="field">
                     <label for="street">Street</label>
                     <input type="text" id="street" name="street" placeholder="e.g. Garden Lane" required>
                  </div>
                  <div class="field">
                     <label for="city">City</label>
                     <input type="text" id="city" name="city" placeholder="e.g. Mumbai" required>
                  </div>
                  <div class="field">
                     <label for="state">State</label>
                     <input type="text" id="state" name="state" placeholder="e.g. Maharashtra" required>
                  </div>
                  <div class="field">
                     <label for="country">Country</label>
                     <input type="text" id="country" name="country" placeholder="e.g. India" required>
                  </div>
                  <div class="field">
                     <label for="pin_code">PIN / ZIP code</label>
                     <input type="text" id="pin_code" name="pin_code" placeholder="e.g. 400001" required>
                  </div>
               </div>
            </fieldset>

            <fieldset>
               <legend>Payment method</legend>
               <div class="field">
                  <select name="method" required>
                     <option value="cash on delivery">Cash on delivery</option>
                     <option value="credit card">Credit card</option>
                     <option value="paypal">PayPal</option>
                     <option value="paytm">Paytm</option>
                  </select>
               </div>
            </fieldset>

            <button type="submit" name="order" class="btn btn-block">Place order -- $<?php echo money($grand_total); ?></button>
         </form>

         <div>
            <div class="order-review">
               <h3 style="font-size:1.05rem;margin-bottom:12px;">Order summary</h3>
               <?php foreach ($rows as $row): ?>
                  <div class="item-row">
                     <span><?php echo e($row['name']); ?> &times; <?php echo (int) $row['quantity']; ?></span>
                     <span>$<?php echo money($row['sub_total']); ?></span>
                  </div>
               <?php endforeach; ?>
            </div>
            <div class="summary-card">
               <div class="row total"><span>Grand total</span><span>$<?php echo money($grand_total); ?></span></div>
            </div>
         </div>

      </div>

      <?php endif; ?>

   </div>
</section>

<?php require 'footer.php'; require 'includes/foot.php'; ?>
