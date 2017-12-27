 <?php

class Product {
  private $db;
  private $config;
  private $image_path = './img/products/';
  //postavili smo defoltnu vrednost
  private $allowed_image_extensions;
  private $max_image_size;
  public $id;
  public $cat_id;
  public $title;
  public $description;
  public $price;
  public $image;
  public $category;
  public $image_info;

  function __construct($id = null) {
    $this->config = require './config.inc.php';
    $this->db = require './db.inc.php';

    $this->max_image_size = 2 * 1024 * 1024;
    $this->allowed_image_extensions = [
      'image/jpeg', 'image/png', 'image/gif'
    ];
    
// provera da li je konstruktoru prosledjen ID korisnika
    // ukoliko jeste prosledjen ID, konstruktor uzima iz baze
    // informacije o korisniku i njima popunjava promenljive
    // u klasi (tako da budu dostupne svim metodama klase)

    if ( $id != null ) {
      $q_getProductInfo = $this->db->prepare("
        SELECT *
        FROM `products`
        WHERE `id` = :id
      ");
      $q_getProductInfo->bindParam(':id', $id);
      $q_getProductInfo->execute();
      $productInfo = $q_getProductInfo->fetch();

      $this->id = $productInfo['id'];
      $this->cat_id = $productInfo['cat_id'];
      $this->title = $productInfo['title'];
      $this->description = $productInfo['description'];
      $this->price = $productInfo['price'];
      $this->image = $productInfo['image'];
    }
  }

  public function save() {
    if ($this->id == NULL) {
      return $this->insert();
    } else {
      return $this->update();
    }
  }
 // provera da li u klasi postoji vrednost za id korisnika
    // ukoliko postoji znaci da metoda save treba da uradi update
    // informacija o korisniku, ukoliko ne postoji id znaci da
    // metoda treba da unese novog korisnika


  public function all($limit = 6, $page = 1) {
    $total = $this->db->query("
      SELECT COUNT(*)
      FROM `products`
    ")->fetchColumn();
    $pages = ceil( $total / $limit );
    $offset = ( $page - 1 ) * $limit;
    $start = $offset + 1;
    $end = min( ($offset + $limit), $total );

    $q_getAllProducts = $this->db->prepare("
      SELECT *
      FROM `products`
      LIMIT $offset, $limit
    ");
    $q_getAllProducts->execute();
    return [
      'products' => $q_getAllProducts->fetchAll(),
      'total_pages' => $pages,
      'total_products' => $total
    ];
  }

  public function insert() {
    $this->handleDirectories();
    $q_insertProduct = $this->db->prepare("
      INSERT INTO `products`
      (`cat_id`, `title`, `description`, `price`, `image`)
      VALUES
      (:cat_id, :title, :description, :price, :image)
    ");
    $q_insertProduct->bindParam(":cat_id", $this->cat_id);
    $q_insertProduct->bindParam(":title", $this->title);
    $q_insertProduct->bindParam(":description", $this->description);
    $q_insertProduct->bindParam(":price", $this->price);
    $q_insertProduct->bindParam(":image", $this->image);
    $result = $q_insertProduct->execute();
    $this->id = $this->db->lastInsertId();

    if ( $result && $this->image_info != null ) {
      //ako uspesno unet rezultat u bazu i ako postavljena slika za upload/informacije o slici
      $fileNameArray = explode('.', $this->image_info['name']);
      $imageExt = strtolower( end( $fileNameArray ) );
      $imagePath = $this->image_path . $this->id . '.' . $imageExt;

      if ( !in_array( $this->image_info['type'], $this->allowed_image_extensions ) ) {
        return false;
      }

      if ( $this->image_info['size'] > $this->max_image_size ) {
        return false;
      }

      move_uploaded_file($this->image_info['tmp_name'], $imagePath);
      
 //premesti sliku na tu putanju
     //image_info - tu se drze privremene informacije

      $this->image = $imagePath;
      //unutar baze menjamo adresu za slike, odnosno updejtujemo u bazi
      $this->save();
      $this->image_info = null;
      //obrisiemo odnosno resetujemo
     //da bi mogla druga slika se ubaci
    }

    return $result;
  }

  public function update() {
    $q_updateProduct = $this->db->prepare("
      UPDATE `products`
      SET
        `cat_id` = :cat_id,
        `title` = :title,
        `description` = :description,
        `price` = :price,
        `image` = :image
      WHERE `id` = :id
    ");
    $q_updateProduct->bindParam(":id", $this->id);
    $q_updateProduct->bindParam(":cat_id", $this->cat_id);
    $q_updateProduct->bindParam(":title", $this->title);
    $q_updateProduct->bindParam(":description", $this->description);
    $q_updateProduct->bindParam(":price", $this->price);
    $q_updateProduct->bindParam(":image", $this->image);
    $result = $q_updateProduct->execute();

    if ( $result && $this->image_info != null && $this->image_info['error'] == 0 ) {
      $fileNameArray = explode('.', $this->image_info['name']);
      $imageExt = strtolower( end( $fileNameArray ) );
      $imagePath = $this->image_path . $this->id . '.' . $imageExt;

      if ( !in_array( $this->image_info['type'], $this->allowed_image_extensions ) ) {
        return false;
      }

      if ( $this->image_info['size'] > $this->max_image_size ) {
        return false;
      }

      $moving_image = move_uploaded_file($this->image_info['tmp_name'], $imagePath);

      $img_update = $this->update_image($imagePath);
      $this->image_info = null;
    }

    return $result;
  }

  public function delete() {
    if ($this->id != null) {
      $q_deleteProduct = $this->db->prepare("
        DELETE
        FROM `products`
        WHERE `id` = :id
      ");
      $q_deleteProduct->bindParam(':id', $this->id);
      $result = $q_deleteProduct->execute();
      $this->id = null;
      return $result;
    }
  }

  public function search($query) {
    $query = '%' . $query . '%';
    $q_search = $this->db->prepare("
      SELECT *
      FROM `products`
      WHERE `title` LIKE :query_title
      OR `description` LIKE :query_description
    ");
    $q_search->bindParam(':query_title', $query);
    $q_search->bindParam(':query_description', $query);
    $q_search->execute();
    return $q_search->fetchAll();
  }

  public function addToCart($quantity = 1) {
    require_once './User.class.php';
    $user_id = User::userId();
    if ( !$user_id ) { return false; }

    $q_getUserProduct = $this->db->prepare("
      SELECT *
      FROM `carts`
      WHERE `product_id` = :product_id
      AND `user_id` = :user_id
    ");
    $q_getUserProduct->bindParam(':product_id', $this->id);
    $q_getUserProduct->bindParam(':user_id', $user_id);
    $q_getUserProduct->execute();
    $user_product_details = $q_getUserProduct->fetch();

    if ( $q_getUserProduct->rowCount() > 0 ) {
      $new_quantity = $user_product_details['quantity'] + $quantity;
      $q_updateQunatity = $this->db->prepare("
        UPDATE `carts`
        SET `quantity` = :quantity
        WHERE `id` = :id
      ");
      $q_updateQunatity->bindParam(':quantity', $new_quantity);
      $q_updateQunatity->bindParam(':id', $user_product_details['id']);
      return $q_updateQunatity->execute();
    } else {
      $q_addToCart = $this->db->prepare("
        INSERT INTO `carts`
        (`user_id`, `product_id`, `quantity`)
        VALUES
        (:user_id, :product_id, :quantity)
      ");
      $q_addToCart->bindParam(':user_id', $user_id);
      $q_addToCart->bindParam(':product_id', $this->id);
      $q_addToCart->bindParam(':quantity', $quantity);
      return $q_addToCart->execute();
    }
  }

  /*
    Helper methodes
  */
  private function handleDirectories() {
    if ( !file_exists($this->image_path) ) {
      mkdir($this->image_path, 0777, true);
    }
  }
  private function update_image($imagePath) {
    $q_updateProduct = $this->db->prepare("
      UPDATE `products`
      SET
        `image` = :image
      WHERE `id` = :id
    ");
    $q_updateProduct->bindParam(":id", $this->id);
    $q_updateProduct->bindParam(":image", $imagePath);
    return $q_updateProduct->execute();
  }

  /*
    Comments
  */
  public function comments() {
    $q_getComments = $this->db->prepare("
      SELECT
        `users`.`id` as user_id,
        `users`.`email`,
        `comments`.`id` as comment_id,
        `comments`.`comment`,
        `comments`.`created_at`
      FROM `users`, `comments`
      WHERE `comments`.`product_id` = :product_id
      AND `users`.`id` = `comments`.`user_id`
      ORDER BY `comments`.`created_at` DESC
    ");
    $q_getComments->bindParam(':product_id', $this->id);
    $q_getComments->execute();
    return $q_getComments->fetchAll();
  }

  public function addComment($comment) {
    require_once './User.class.php';
    $user_id = User::userId();
    if ( !$user_id ) { return false; }
    $q_addComment = $this->db->prepare("
      INSERT INTO `comments`
      (`user_id`, `product_id`, `comment`)
      VALUES
      (:user_id, :product_id, :comment)
    ");
    $q_addComment->bindParam(':user_id', $user_id);
    $q_addComment->bindParam(':product_id', $this->id);
    $q_addComment->bindParam(':comment', $comment);
    return $q_addComment->execute();
  }
}
