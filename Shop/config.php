<?php
$host = 'localhost';
$db_name = 'php2';
$user = 'root';
$pass = '';
$port = '3306';
$dns = "mysql:host=$host;dbname=$db_name;port=$port;charset=UTF8";

try {
    $pdo = new PDO($dns,$user,$pass);
    if ($pdo){
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_OBJ);
    }
} catch (PDOException $e){
    echo $e->getMessage();
}