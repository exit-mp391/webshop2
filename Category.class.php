<?php

class Category {
  private $db;
  private $config;
  public $id;
  public $title;

  function __construct($id = null) {
    $this->config = require './config.inc.php';
    $this->db = require './db.inc.php';

    if ($id != null) {
      $q_getCategoryInfo = $this->db->prepare("
        SELECT *
        FROM `categories`
        WHERE `id` = :id
      ");
      $q_getCategoryInfo->bindParam(':id', $id);
      $q_getCategoryInfo->execute();
      $categoryInfo = $q_getCategoryInfo->fetch();
      
      $this->id = $categoryInfo['id'];
      $this->title = $categoryInfo['title'];
    }
  }

  public function all() {
    $q_getAllCategories = $this->db->prepare("
      SELECT *
      FROM `categories`
    ");
    $q_getAllCategories->execute();
    return $q_getAllCategories->fetchAll();
  }

  public function products() {
    if ($this->id == null) {
      return [];
    }

    $q_getProductsFromCategory = $this->db->prepare("
      SELECT *
      FROM `products`
      WHERE `cat_id` = :cat_id
    ");
    $q_getProductsFromCategory->bindParam(':cat_id', $this->id);
    $q_getProductsFromCategory->execute();
    return $q_getProductsFromCategory->fetchAll();
  }

  public function save() {
    if ($this->id == null) {
      return $this->insert();
    } else {
      return $this->update();
    }
  }

  public function insert() {
    $q_insertCategory = $this->db->prepare("
      INSERT INTO `categories`
      (`title`)
      VALUES
      (:title)
    ");
    $q_insertCategory->bindParam(':title', $this->title);
    $result = $q_insertCategory->execute();
    $this->id = $this->db->lastInsertId();
    return $result;
  }

  public function update() {
    $q_updateCategory = $this->db->prepare("
      UPDATE `categories`
      SET
        `title` = :title
      WHERE `id` = :id
    ");
    $q_updateCategory->bindParam(':title', $this->title);
    $q_updateCategory->bindParam(':id', $this->id);
    return $q_updateCategory->execute();
  }

  public function delete() {
    if ($this->id != null) {
      $q_deleteCategory = $this->db->prepare("
        DELETE
        FROM `categories`
        WHERE `id` = :id
      ");
      $q_deleteCategory->bindParam(':id', $this->id);
      $result = $q_deleteCategory->execute();
      $this->id = null;
      return $result;
    }
  }

}