<?php

require_once './Product.class.php';
require_once './Helper.class.php';

$p = new Product();

echo "<pre>";
var_dump( $p->all(4, 3) );