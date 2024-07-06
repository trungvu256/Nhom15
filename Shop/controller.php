<?php

include 'model.php';
$list_user = getAllUser();
$user_id = (!empty($_GET['user_id']? $_GET('user_id') : ''));
$user = getUser($user_id);
include 'view.php';