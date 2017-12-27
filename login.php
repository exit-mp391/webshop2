<?php

if ( isset($_POST['email']) && isset($_POST['password']) ) {
  require_once './User.class.php';
  $u = new User();
  $login = $u->login($_POST['email'], $_POST['password']);
  if ( $login ) {
    header('Location: ./index.php');
  }
}

?>

<?php include './header.layout.php'; ?>

<?php

if ( isset($_GET['registration']) && $_GET['registration'] == 'succeess' ) {
  Helper::success('Account successfully created.');
}

?>

<?php if ( isset($login) && !$login ) : ?>

  <div class="row">
    <div class="col-md-12">
    <?php
      require_once './Helper.class.php';
      Helper::error('Wrong username and/or password.', 'Login error!');
    ?>
    </div>
  </div>
    
<?php endif; ?>

<div class="row">
  <div class="col-md-3"></div>
  <div class="col-md-6">
  <h1>Login</h1>
  <form action="./login.php" method="post" class="mt-5">
  <div class="form-group">
    <label for="inputEmail1">Email address</label>
    <input type="email" class="form-control" name="email" id="inputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
    <!-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> -->
  </div>
  <div class="form-group">
    <label for="inputPassword">Password</label>
    <input type="password" class="form-control" name="password" id="inputPassword" placeholder="Password">
  </div>
  <button type="submit" class="btn btn-primary">Login</button>
</form>
  </div>
</div>

<?php include './footer.layout.php'; ?>