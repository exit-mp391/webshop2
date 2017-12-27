<?php

require_once './User.class.php';
require_once './Helper.class.php';

if ( !isset($_GET['id']) ) {
  header('Location: ./products.php');
}

require_once './Product.class.php';
$p = new Product($_GET['id']);


if ( isset($_GET['delete']) ) {
  if ( User::isAdmin() ) {
    $p->delete();
    header('Location: ./products.php');
  }
}

if ( isset($_GET['add_to_cart']) && User::userId() ) {
    $add_to_cart = $p->addToCart();
}

if ( isset($_POST['add_comment']) ) {
  $p->addComment($_POST['comment']);
}

$comments = $p->comments();

?>
<?php include './header.layout.php'; ?>

<h1><?= $p->title ?></h1>

<?php

if ( isset($add_to_cart) && $add_to_cart ) {
  Helper::success('Product successfully added into your cart.');
}

if ( isset($add_to_cart) && !$add_to_cart ) {
  Helper::error('Failed to add product to cart.');
}

?>

<div class="row mt-5">
  <div class="col-md-5">
    <img src="<?= ($p->image) ? $p->image : './img/product.png' ?>" class="img-fluid" />
  </div>
  <div class="col-md-7">
    <h2>Price</h2>
    <p>&euro; <?= $p->price ?></p>
    <h2  class="mt-5">Description</h2>
    <p><?= $p->description ?></p>
    <div class="clearfix mt-5">
      <a href="./product-details.php?id=<?= $p->id ?>&add_to_cart" class="btn btn-success float-right <?= (!User::userId()) ? 'disabled' : null ?>">Add to cart</a>
    <?php if (User::isAdmin()): ?>
      <a href="./update-product.php?id=<?= $p->id ?>" class="btn btn-warning float-right">Update product</a>
      <a href="./product-details.php?id=<?= $p->id ?>&delete" class="btn btn-danger float-right">Delete product</a>
      <?php endif; ?>
    </div>
  </div>
</div>

<div class="row mt-5">
  <div class="col-md-12">

<?php if(User::userId()): ?>
    <h3 class="mt-5">Add comment</h3>

    <form action="./product-details.php?id=<?= $_GET['id'] ?>" method="post">
      <div class="add-comment-form">
        <div class="comment-inpt">
          <div class="form-group">
            <textarea name="comment" class="form-control" placeholder="Write your comment here..."></textarea>
          </div>
        </div>
        <div class="comment-btn">
          <button name="add_comment" class="btn btn-primary">Add comment</button>
        </div>
      </div>
    </form>
<?php endif; ?>

    <h3 class="mt-5">Comments</h3>

    <?php foreach($comments as $comment): ?>
      <div class="comment">
        <div class="card">
          <div class="card-body clearfix">
            <p class="card-text"><?= $comment['comment'] ?></p>
            <h6 class="card-subtitle mb-2 text-muted comment-info float-right">
              Posted by <?= $comment['email'] ?>
              at <?= $comment['created_at'] ?>
            </h6>
          </div>
        </div>
        </div>
    <?php endforeach; ?>
    
  </div>
</div>

<?php include './footer.layout.php'; ?>