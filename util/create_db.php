<?php

$dbHost = 'localhost';
$dbUser = 'user';
$dbPassword = 'password';
$dbName = 'database';

try {

    $db = new mysqli($dbHost, $dbUser, $dbPassword);
    if (mysqli_connect_errno()) {
        throw new Exception("MySQL error");
    }

    $query = "CREATE DATABASE IF NOT EXISTS `" . $dbName . "`";
    $db->query($query);
    if (mysqli_errno($db)) {
        throw new Exception("MySQL error");
    }

    $db->select_db($dbName);
    if (mysqli_errno($db)) {
        throw new Exception("MySQL error");
    }

    $query = "CREATE TABLE IF NOT EXISTS `users` (
        `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(20) NOT NULL UNIQUE,
        `password` VARCHAR(32) NOT NULL,
        `token` VARCHAR(32),
        `regdate` DATETIME,
        `lastvisit` DATETIME
    )";

    $db->query($query);
    if (mysqli_errno($db)) {
        throw new Exception("MySQL error");
    }

} catch (Exception $e) {
    $e->getMessage();
    exit();
}
