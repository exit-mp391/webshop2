<?php

require_once './user.only.php';
require_once './User.class.php';

setlocale(LC_MONETARY, 'en_US');

$user = new User(User::userId());
$cart = $user->getCart();

$total_cart_value = 0;
foreach($cart as $item){
  $total_cart_value += $item['total_price'];
}

// echo "<pre>";
// var_dump($cart);

?>

<?php include './header.layout.php'; ?>

<h1>Cart</h1>

<table class="table table-hover mt-5">
  <thead class="thead-inverse">
    <tr>
      <th>Product title</th>
      <th>Price</th>
      <th>Quantity</th>
      <th>Total price</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($cart as $item): ?>
      <tr>
        <th><?= $item['title'] ?></th>
        <td>&euro; <?= number_format($item['price'], 2, '.', ','); ?></td>
        <td><?= $item['quantity'] ?></td>
        <td>&euro; <?= number_format($item['total_price'], 2, '.', ','); ?></td>
        <td>
          <a href="#"
            data-id="<?= $item['id'] ?>"
            class="btn btn-sm btn-danger remove-from-cart">
              Remove
          </a>
        </td>
      </tr>
    <?php endforeach; ?>

  </tbody>
  <tfoot>
    <tr>
        <th></th>
        <th></th>
        <th>TOTAL:</th>
        <th>&euro; <?= number_format($total_cart_value, 2, '.', ','); ?></th>
        <th></th>
      </tr>
  </tfoot>
</table>

<?php include './footer.layout.php'; ?>