
<?php

class User {
  private $config;
  private $db;
  public $id;
  public $email;
  public $password;
  public $name;
  public $last_name;
  public $newsletter;
  public $address;
  public $city;
  public $country;
  public $phone_number;
  public $date_of_birth;
  public $account_type;
  public $password_reset_token;

  function __construct($id = NULL) {
    // ucitavanje config i db fajlova tako da budu dostupni
    // celoj klasi
    $this->config = require './config.inc.php';
    $this->db = require './db.inc.php';

    // provera da li je konstruktoru prosledjen ID korisnika
    // ukoliko jeste prosledjen ID, konstruktor uzima iz baze informacije o korisniku i njima popunjava promenljive
    // u klasi (tako da budu dostupne svim metodama klase)
    if ($id != NULL) {
      $q_getUserInfo = $this->db->prepare("
        SELECT *
        FROM `users`
        WHERE `id` = :id
      ");
      $q_getUserInfo->bindParam(':id', $id);
      $q_getUserInfo->execute();

      // konstruktor uzima iz baze informacije o korisniku
      // i njima popunjava promenljive u klasi (tako da
      // budu dostupne svim metodama klase)
      $user_info = $q_getUserInfo->fetch();
      $this->id = $user_info['id'];
      $this->email = $user_info['email'];
      $this->password = $user_info['password'];
      $this->name = $user_info['name'];
      $this->last_name = $user_info['last_name'];
      $this->newsletter = $user_info['newsletter'];
      $this->address = $user_info['address'];
      $this->city = $user_info['city'];
      $this->country = $user_info['country'];
      $this->phone_number = $user_info['phone_number'];
      $this->date_of_birth = $user_info['date_of_birth'];
      $this->account_type = $user_info['account_type'];
      $this->password_reset_token = $user_info['password_reset_token'];
    }
  }

  public function save() {
    // provera da li u klasi postoji vrednost za id korisnika
    // ukoliko postoji znaci da metoda save treba da uradi update
    // informacija o korisniku, ukoliko ne postoji id znaci da
    // metoda treba da unese novog korisnika
    if ($this->id == NULL) {
      return $this->insert();
    } else {
      return $this->update();
    }
  }

  public function insert() {
    $q_insertUser = $this->db->prepare("
      INSERT INTO `users`
      (`email`, `password`, `name`, `last_name`, `newsletter`, `address`, `city`, `country`, `phone_number`, `date_of_birth`, `account_type`, `password_reset_token`)
      VALUES
      (:email, :password, :name, :last_name, :newsletter, :address, :city, :country, :phone_number, :date_of_birth, :account_type, :password_reset_token)
    ");
     //specifiramo vrednosti koje ce da bindujemo(na values)
    //pa onda pozivamo bindmetodu to je ovo dole bindParam
    //bindparam vezuje placeholdere sa user entered data/sa podacima koje je uneo korisnik
    //ovde bindujemo/vezujemo svoje podatke, dajemo placeholder i specifiramo sta
    $q_insertUser->bindParam(":email", $this->email);
    $q_insertUser->bindParam(":password", $this->password);
    $q_insertUser->bindParam(":name", $this->name);
    $q_insertUser->bindParam(":last_name", $this->last_name);
    $q_insertUser->bindParam(":newsletter", $this->newsletter);
    $q_insertUser->bindParam(":address", $this->address);
    $q_insertUser->bindParam(":city", $this->city);
    $q_insertUser->bindParam(":country", $this->country);
    $q_insertUser->bindParam(":phone_number", $this->phone_number);
    $q_insertUser->bindParam(":date_of_birth", $this->date_of_birth);
    $q_insertUser->bindParam(":account_type", $this->account_type);
    $q_insertUser->bindParam(":password_reset_token", $this->password_reset_token);
    $result = $q_insertUser->execute();
    $this->id = $this->db->lastInsertId();
    //vraca zadnji id, i tako proveravamo dali je nesto insertovano
    return $result;
  }

  public function update() {
    $q_updateUser = $this->db->prepare("
      UPDATE `users`
      SET
        `email` = :email,
        `password` = :password,
        `name` = :name,
        `last_name` = :last_name,
        `newsletter` = :newsletter,
        `address` = :address,
        `city` = :city,
        `country` = :country,
        `phone_number` = :phone_number,
        `date_of_birth` = :date_of_birth,
        `account_type` = :account_type,
        `password_reset_token` = :password_reset_token
      WHERE `id` = :id
    ");
    $q_updateUser->bindParam(":id", $this->id);
    $q_updateUser->bindParam(":email", $this->email);
    $q_updateUser->bindParam(":password", $this->password);
    $q_updateUser->bindParam(":name", $this->name);
    $q_updateUser->bindParam(":last_name", $this->last_name);
    $q_updateUser->bindParam(":newsletter", $this->newsletter);
    $q_updateUser->bindParam(":address", $this->address);
    $q_updateUser->bindParam(":city", $this->city);
    $q_updateUser->bindParam(":country", $this->country);
    $q_updateUser->bindParam(":phone_number", $this->phone_number);
    $q_updateUser->bindParam(":date_of_birth", $this->date_of_birth);
    $q_updateUser->bindParam(":account_type", $this->account_type);
    $q_updateUser->bindParam(":password_reset_token", $this->password_reset_token);
    $result = $q_updateUser->execute();

    // if successfully saved, update session
    //updejtovali smo podatke sad updejtujemo sesiju
    if ($result) {
      require_once './Helper.class.php';
      Helper::session_start();
      $_SESSION['user'] = [
        'id' => $this->id,
        'email' => $this->email,
        'password' => $this->password,
        'name' => $this->name,
        'last_name' => $this->last_name,
        'newsletter' => $this->newsletter,
        'address' => $this->address,
        'city' => $this->city,
        'country' => $this->country,
        'phone_number' => $this->phone_number,
        'date_of_birth' => $this->date_of_birth,
        'account_type' => $this->account_type,
        'password_reset_token' => $this->password_reset_token,
      ];
    }

    return $result;
  }

  public function delete() {
    if ($this->id != null) {
      $q_deleteCategory = $this->db->prepare("
        DELETE
        FROM `users`
        WHERE `id` = :id
      ");
      $q_deleteCategory->bindParam(':id', $this->id);
      $result = $q_deleteCategory->execute();
      $this->id = null;
      return $result;
    }
  }

  public static function userId() {
    require_once './Helper.class.php';
    Helper::session_start();

    if ( !isset($_SESSION['user_id']) ) {
      return false;
    }

    return $_SESSION['user_id'];
  }

  public function login($email, $password) {
    $q_getUser = $this->db->prepare("
      SELECT *
      FROM `users`
      WHERE `email` = :email
      AND `password` = :password
    ");
    $password = md5($password);
    $q_getUser->bindParam(":email", $email);
    $q_getUser->bindParam(":password", $password);
    $q_getUser->execute();
    $userInfo = $q_getUser->fetch();

    if ( !$userInfo ) {
      return false;
    }

    require_once './Helper.class.php';
    Helper::session_start();
    $_SESSION['user_id'] = $userInfo['id'];
    $_SESSION['user'] = $userInfo;
    return true;
  }
  //podesavamo vrednosti sesije

  public static function logout() {
    require_once './Helper.class.php';
    Helper::session_destroy();
  }

  public static function isAdmin() {
    require_once './Helper.class.php';
    Helper::session_start();

    if (
      isset($_SESSION['user'])
      && isset($_SESSION['user']['account_type'])
      && $_SESSION['user']['account_type'] == 'admin'
    ) {
        return true;
    }

    return false;
  }

  public function getCart() {
    $user_id = $this->userId();
    if (!$user_id) { return false; }

    $q_getCart = $this->db->prepare("
      SELECT
        `carts`.`id`,
        `products`.`title`,
        `products`.`price`,
        `carts`.`quantity`
      FROM `carts`, `products`
      WHERE `carts`.`product_id` = `products`.`id`
      AND `carts`.`user_id` = :user_id
    ");
    $q_getCart->bindParam(':user_id', $user_id);
    $q_getCart->execute();
    $cart = $q_getCart->fetchAll();

    for ($i = 0; $i < count($cart); $i++) {
      $cart[$i]['total_price'] = $cart[$i]['quantity'] * $cart[$i]['price'];
    }
    
    
    return $cart;
  }

  public function number_of_items_in_cart() {
    $user_id = $this->userId();
    if (!$user_id) { return false; }

    $q_getNumberOfProducts = $this->db->prepare("
      SELECT count(*)
      FROM `carts`
      WHERE `user_id` = :user_id
    ");
    $q_getNumberOfProducts->bindParam(':user_id', $user_id);
    $q_getNumberOfProducts->execute();
    return $q_getNumberOfProducts->fetchColumn();
  }
}
