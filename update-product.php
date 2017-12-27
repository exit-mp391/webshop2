<?php

require_once './User.class.php';

if ( !isset($_GET['id']) ) {
  header('Location: ./products.php');
}

require_once './Product.class.php';
$p = new Product($_GET['id']);

if ( isset($_POST['update_product']) ) {
  $p->title = $_POST['title'];
  $p->description = $_POST['description'];
  $p->price = $_POST['price'];
  $p->cat_id = $_POST['cat_id'];
  $p->image_info = $_FILES['image'];
  $updated = $p->save();

  if ($updated) {
    $p = new Product($_GET['id']);
  }
}

?>
<?php include './header.layout.php'; ?>

<h1>Update: <?= $p->title ?></h1>

<?php

if ( isset($_POST['update_product']) && $updated ) {
  require_once './Helper.class.php';
  Helper::success('Product details updated.');
}

if ( isset($_POST['update_product']) && !$updated ) {
  require_once './Helper.class.php';
  Helper::error('Failed to update product.');
}

?>

<form action="./update-product.php?id=<?= $_GET['id'] ?>" method="post" enctype="multipart/form-data">

  <div class="row mt-5">

    <!-- OLD IMAGE -->
    <div class="col-md-6">
      <div class="image-container">
        <img src="<?= ($p->image) ? $p->image : './img/product.png' ?>" class="img-fluid old-image" />
      </div>
    </div>

    <div class="col-md-6">
      <!-- NEW IMAGE -->
      <div class="form-group">
        <div class="mb-2">New image</div>
        <label class="custom-file">
          <input type="file" name="image" id="inputImage" class="custom-file-input" />
          <span class="custom-file-control"></span>
        </label>
      </div>

      <!-- CATEGORY -->
      <div class="form-group">
        <label for="inputCategory">Category</label>
        <select name="cat_id" class="form-control" id="inputCategory">
          <?php foreach($categories as $category): ?>
            <option value="<?= $category['id'] ?>" <?= ($p->cat_id == $category['id']) ? 'selected' : '' ?>>
              <?= $category['title'] ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <!-- TITLE -->
    <div class="col-md-6">
      <div class="form-group">
        <label for="inputTitle">Title</label>
        <input type="text" name="title" class="form-control" id="inputTitle" placeholder="Product title" value="<?= $p->title ?>" />
      </div>
    </div>
    <!-- PRICE -->
    <div class="col-md-6">
      <label for="inputPrice">Price</label>
      <div class="input-group">
        <span class="input-group-addon">$</span>
        <input type="number" name="price" class="form-control" id="inputPrice" placeholder="Product price" value="<?= $p->price ?>" />
      </div>
    </div>
    <!-- DESCRIPTION -->
    <div class="col-md-12">
      <div class="form-group">
        <label for="inputDescription">Description</label>
        <textarea name="description" class="form-control" id="inputDescription" placeholder="Detailed product description"><?= $p->description ?></textarea>
      </div>
    </div>

    <!-- BUTTON -->
    <div class="col-md-12 clearfix">
      <button class="btn btn-primary float-right" type="submit" name="update_product">Update product</button>
    </div>
  </div>

</form>


<?php include './footer.layout.php'; ?>