<?php

require_once './Helper.class.php';
$errors = [];

if ( isset($_POST['submit']) ) {

  // email
  if( !isset($_POST['email']) || $_POST['email'] == '') {
    $errors[] = 'E-mail address is required.';
  }

  // password
  if( !isset($_POST['password']) || $_POST['password'] == '') {
    $errors[] = 'Password is required.';
  }

  // password repeat
  if( !isset($_POST['password_repeat']) || $_POST['password_repeat'] == '') {
    $errors[] = 'You have to enter password twice.';
  }

  // passwords match
  if ( empty($errors) ) {
    if( $_POST['password'] != $_POST['password_repeat'] ) {
      $errors[] = 'Passwords don\'t match.';
    }
  }

  // terms of service checked
  if ( !isset($_POST['tos']) || $_POST['tos'] != 'on' ) {
    $errors[] = 'You have to agree to the terms of service.';
  }

  if( empty($errors) ) {
    require_once './User.class.php';
    $u = new User();
    $u->email = $_POST['email'];
    $u->password = md5($_POST['password']);
    $u->name = $_POST['name'];
    $u->last_name = $_POST['last_name'];
    $u->address = $_POST['address'];
    $u->city = $_POST['city'];
    $u->country = $_POST['country'];
    $u->phone_number = $_POST['phone_number'];
    $u->date_of_birth = ( isset($_POST['date_of_birth']) && $_POST['date_of_birth'] != '' ) ? $_POST['date_of_birth'] : null;
    $u->newsletter = ( isset($_POST['newsletter']) && $_POST['newsletter'] == 'on' ) ? true : false;
    $registration = $u->save();

    if( $registration ) {
      header('Location: ./login.php?registration=succeess');
    }
  }

}

?>

<?php include './header.layout.php'; ?>

<h1>Register</h1>

<?php

if ( isset($registration) && $registration ) {
  Helper::success('Registration successfull.');
}

if( isset($registration) && !$registration ) {
  Helper::error('Failed to add user to database. Make sure you don\'t already have an account.');
}

if ( !empty($errors) ) {
  Helper::error($errors);
}
  
?>

<form action="./register.php" method="post">

  <div class="row mt-5">

    <!-- EMAIL -->
    <div class="col-md-12">
      <div class="form-group">
        <label for="inputEmail">E-mail</label>
        <input type="email" name="email" class="form-control" id="inputEmail" placeholder="E-mail address" />
      </div>
    </div>

    <!-- PASSWORD -->
    <div class="col-md-6">
      <div class="form-group">
        <label for="inputPassword">Password</label>
        <input type="password" name="password" class="form-control" id="inputPassword" placeholder="Password" />
      </div>
    </div>

      <!-- PASSWORD REPEAT -->
    <div class="col-md-6">
      <div class="form-group">
        <label for="inputPasswordRepeat">Password again</label>
        <input type="password" name="password_repeat" class="form-control" id="inputPasswordRepeat" placeholder="Password again" />
      </div>
    </div>

    <!-- NAME -->
    <div class="col-md-6">
      <div class="form-group">
        <label for="inputName">Name</label>
        <input type="text" name="name" class="form-control" id="inputName" placeholder="Name" />
      </div>
    </div>

    <!-- LAST NAME -->
    <div class="col-md-6">
      <div class="form-group">
        <label for="inputLastName">Last name</label>
        <input type="text" name="last_name" class="form-control" id="inputLastName" placeholder="Last name" />
      </div>
    </div>

    <!-- ADDRESS -->
    <div class="col-md-12">
      <div class="form-group">
        <label for="inputAddress">Address</label>
        <input type="text" name="address" class="form-control" id="inputAddress" placeholder="Address" />
      </div>
    </div>

    <!-- CITY -->
    <div class="col-md-6">
      <div class="form-group">
        <label for="inputCity">City</label>
        <input type="text" name="city" class="form-control" id="inputCity" placeholder="City" />
      </div>
    </div>

    <!-- COUNTRY -->
    <div class="col-md-6">
      <div class="form-group">
        <label for="inputCountry">Country</label>
        <input type="text" name="country" class="form-control" id="inputCountry" placeholder="Country" />
      </div>
    </div>

    <!-- PHONE NUMBER -->
    <div class="col-md-6">
      <div class="form-group">
        <label for="inputPhoneNumber">Phone number</label>
        <input type="text" name="phone_number" class="form-control" id="inputPhoneNumber" placeholder="Phone number" />
      </div>
    </div>

    <!-- DATE OF BIRTH -->
    <div class="col-md-6">
      <div class="form-group">
        <label for="inputDateOfBirth">Date of birth</label>
        <input type="date" name="date_of_birth" class="form-control" id="inputDateOfBirth" />
      </div>
    </div>

    <!-- NEWSLETTER -->
    <div class="col-md-12">
      <div class="form-check">
        <label class="custom-control custom-checkbox">
          <input type="checkbox" name="newsletter" class="custom-control-input" checked />
          <span class="custom-control-indicator"></span>
          <span class="custom-control-description">I would like to receive newsletter</span>
        </label>
      </div>
    </div>

    <!-- TERMS OF SERVICE -->
    <div class="col-md-12">
      <div class="form-check">
        <label class="custom-control custom-checkbox">
          <input type="checkbox" name="tos" class="custom-control-input" />
          <span class="custom-control-indicator"></span>
          <span class="custom-control-description">I read and agree to Terms of Service</span>
        </label>
      </div>
    </div>

    <!-- BUTTON -->
    <div class="col-md-12">
      <button type="submit" name="submit" class="btn btn-primary btn-block">Register</button>
    </div>


  </div>

</form>

<?php include './footer.layout.php'; ?>
