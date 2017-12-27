<?php

$db = require './db.inc.php';
$config = require './config.inc.php';

/*
  Create users table
*/
$q_createUsersTable = $db->prepare("
  CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT,
    `email` varchar(255) UNIQUE,
    `password` varchar(255),
    `name` varchar(50),
    `last_name` varchar(50),
    `newsletter` boolean DEFAULT false,
    `address` varchar(255),
    `city` varchar(50),
    `country` varchar(50),
    `phone_number` varchar(20),
    `date_of_birth` date,
    `account_type` enum('user', 'admin') DEFAULT 'user',
    `password_reset_token` varchar(255),
    `registration_date` datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
  )
");
$q_createUsersTable->execute();

/*
    Insert administrative account
*/
$q_getUsers = $db->prepare("
  SELECT *
  FROM `users`
");
$q_getUsers->execute();
$numOfUsers = $q_getUsers->rowCount();

if ( $numOfUsers == 0 ) {
  $q_insertAdministrator = $db->prepare("
    INSERT INTO `users`
    (`email`, `password`, `account_type`)
    VALUES
    (:email, :password, :account_type)
  ");
  $q_insertAdministrator->bindParam(':email', $config['default_admin_email']);
  $q_insertAdministrator->bindParam(':password', $config['default_admin_password']);
  $q_insertAdministrator->bindParam(':account_type', $config['default_admin_account_type']);
  $q_insertAdministrator->execute();
}

/*
  Create categories table
*/
$q_createCategoriesTable = $db->prepare("
  CREATE TABLE IF NOT EXISTS `categories` (
    `id` int AUTO_INCREMENT,
    `title` varchar(255),
    PRIMARY KEY (`id`)
  )
");
$q_createCategoriesTable->execute();

/*
    Create products table
*/
$q_createProductsTable = $db->prepare("
  CREATE TABLE IF NOT EXISTS `products` (
    `id` int AUTO_INCREMENT,
    `cat_id` int,
    `title` varchar(255),
    `description` TEXT,
    `price` decimal(10, 2),
    `image` varchar(255),
    PRIMARY KEY (`id`)
  )
");
$q_createProductsTable->execute();


/*
    Create comments table
*/
$q_createCommentsTable = $db->prepare("
  CREATE TABLE IF NOT EXISTS `comments` (
    `id` int AUTO_INCREMENT,
    `user_id` int,
    `product_id` int,
    `comment` text,
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
  )
");
$q_createCommentsTable->execute();

/*
    Cart
*/

$q_createCartsTable = $db->prepare("
  CREATE TABLE IF NOT EXISTS `carts` (
    `id` int AUTO_INCREMENT,
    `user_id` int,
    `product_id` int,
    `quantity` int,
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
  )
");
$q_createCartsTable->execute();