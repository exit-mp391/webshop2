<?php

require_once './User.class.php';

User::logout();

header('Location: index.php');
