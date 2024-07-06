<?php
include 'config.php';
function getAllUser(){
    global $pdo;
    $sql = 'select * from users';
    $query = $pdo->query($sql);
    $result = $query->fetchAll();
    return $result;
}
function getUser($user_id){
    global $pdo;
    $sql = "select * from users where id = '$user_id'";
    $query = $pdo->query($sql);
    $result = $query->fetch();
    return $result;
}
