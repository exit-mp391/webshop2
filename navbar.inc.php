<?php

require_once './User.class.php';

$user = new User(User::userId());

?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">

    <a class="navbar-brand" href="./index.php">WebShop</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item active">
          <a class="nav-link" href="./index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="./products.php">Products</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Contact</a>
        </li>
      </ul>

      <form action="./products.php" method="get" class="form-inline my-2 my-lg-0">
        <input type="text" name="search" class="form-control mr-sm-2" placeholder="Search" value="<?= (isset($_GET['search'])) ? $_GET['search'] : null ?>">
        <button class="btn btn-outline-muted my-2 my-sm-0" type="submit">Search</button>
      </form>

      <ul class="navbar-nav ml-auto">
        <li class="nav-item">

        <li>
          <a href="./cart.php" class="nav-link">
            Cart
            <sup class="badge badge-success">
              <?= $user->number_of_items_in_cart() ?>
            </sup>
          </a>
        </li>

          <div class="dropdown">
            <a class="nav-link dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <?php
              if (User::userId()) {
                if ( isset($_SESSION['user']['name']) && $_SESSION['user']['name'] != '' ) {
                  echo $_SESSION['user']['name'] . ' <small>(' . $_SESSION['user']['email'] . ')</small>';
                } else {
                  echo $_SESSION['user']['email'];
                }
              } else {
                echo "Login";
              }
              ?>
            </a>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
              
              <?php if (User::isAdmin()): ?>
              <h6 class="dropdown-header">Administration</h6>
              <a class="dropdown-item" href="./add-product.php">Add product</a>
              <?php endif; ?>

              <?php if (User::userId()): ?>
              <h6 class="dropdown-header">User options</h6>
              <a class="dropdown-item" href="./settings.php">Settings</a>
              
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="./logout.php">Log out</a>
              <?php else: ?>
              <a class="dropdown-item" href="./login.php">Login</a>
              <a class="dropdown-item" href="./register.php">Register</a>
              <?php endif; ?>

            </div>
          </div>

        </li>
      </ul>
    </div>
  </div>
</nav>